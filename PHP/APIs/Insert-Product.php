<?php

namespace ProductData;


require_once __DIR__ . "/../Data/Database.php";
require_once __DIR__ . "/../Data/Product.php";
require_once __DIR__ . "/../Data/Book.php";
require_once __DIR__ . "/../Data/DVD.php";
require_once __DIR__ . "/../Data/Furniture.php";




// Allow requests from a specific origin
$allowedOrigin = 'http://localhost:3000'; // Replace with your allowed origin

if (isset($_SERVER['HTTP_ORIGIN']) && $_SERVER['HTTP_ORIGIN'] === $allowedOrigin) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    }
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    }
    exit(0);
}

header("Content-type: application/json");

// Mapping of types to class names
$classMap = [
    'Book' => 'ProductData\\Book',
    'Furniture' => 'ProductData\\Furniture',
    'DVD' => 'ProductData\\DVD',
    // Add other classes as needed
];

// Example payload from frontend
$payload = json_decode(file_get_contents('php://input'), true);

error_log("Received payload: " . print_r($payload, true));

$type = $payload['type']; // Type from frontend payload

// Ensure the type is valid and exists in the class map
if (isset($classMap[$type])) {
    $className = $classMap[$type];

    // Extract constructor arguments from payload, including nested 'attributes'
    $constructorArgs = [
        'sku' => $payload['sku'],
        'name' => $payload['name'],
        'price' => $payload['price'],
    ];

    // Add attributes based on type dynamically
    $typeAttributes = [
        'Book' => ['author', 'weight'],
        'Furniture' => ['material', 'dimensions'],
        'DVD' => ['size'],
    ];

    // Add type-specific attributes to constructor arguments
    foreach ($typeAttributes[$type] as $attribute) {
        if (isset($payload[$attribute])) {
            $constructorArgs[$attribute] = $payload[$attribute];
        }
    }

    // Print out the final constructor arguments to debug
    error_log(print_r($constructorArgs, true));

    // Using Reflection to dynamically create an instance
    try {
        $reflectionClass = new \ReflectionClass($className);
        $productInstance = $reflectionClass->newInstanceArgs(array_values($constructorArgs));


        // Example: Saving the product instance
        $db = new Database();
        $pdo = $db->getConnection();

        // Call the save method on the dynamically created instance
        $productInstance->save($pdo);

        // Output success or handle further logic
        echo json_encode(['message' => 'Product saved successfully']);
    } catch (\ReflectionException $e) {
        echo json_encode(['error' => 'Error creating product instance: ' . $e->getMessage()]);
    } catch (\PDOException $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    // Handle invalid type error
    echo json_encode(['error' => 'Invalid product type']);
}
