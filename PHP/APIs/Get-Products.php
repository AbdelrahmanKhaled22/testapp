<?php

namespace ProductData;

require_once __DIR__ . "/../Data/Database.php";
require_once __DIR__ . "/../Data/Product.php";
require_once __DIR__ . "/../Data/Book.php";
require_once __DIR__ . "/../Data/DVD.php";
require_once __DIR__ . "/../Data/Furniture.php";


header('Access-Control-Allow-Origin: http://localhost:3000', "Content-type: application/json");


$db = new Database();

$pdo = $db->getConnection();


// Load all books from the database
try {
    $books = Book::load($pdo);
    $dvds = DVD::load($pdo);
    $furniture = Furniture::load($pdo);
    // Prepare array to hold book and DVD data
    $items = [];

    // Iterate through loaded books
    foreach ($books as $book) {
        $bookData = [
            'type' => 'book',
            'id' => $book->getId(),
            'sku' => $book->getSku(),
            'name' => $book->getName(),
            'price' => $book->getPrice(),
            'author' => $book->getAuthor(),
            'weight' => $book->getWeight()
        ];
        $items[] = $bookData;
    }

    // Iterate through loaded DVDs
    foreach ($dvds as $dvd) {
        $dvdData = [
            'type' => 'dvd',
            'id' => $dvd->getId(),
            'sku' => $dvd->getSku(),
            'name' => $dvd->getName(),
            'price' => $dvd->getPrice(),
            'size' => $dvd->getSize()
        ];
        $items[] = $dvdData;
    }

    // Iterate through loaded Furniture
    foreach ($furniture as $f) {
        $furnitureData = [
            'type' => 'furniture',
            'id' => $f->getId(),
            'sku' => $f->getSku(),
            'name' => $f->getName(),
            'price' => $f->getPrice(),
            'material' => $f->getMaterial(),
            'dimensions' => $f->getDimensions()
        ];
        $items[] = $furnitureData;
    }

    // Output as JSON

    echo json_encode($items, JSON_PRETTY_PRINT);
} catch (\PDOException $e) {
    echo "Error loading books: " . $e->getMessage() . "\n";
}
