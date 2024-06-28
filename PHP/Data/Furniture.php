<?php

namespace ProductData;

class Furniture extends Product
{

    private $material;
    private $dimensions;

    public function __construct($sku, $name, $price, $material, $dimensions, $type = "Furniture")
    {
        parent::__construct($sku, $name, $price, $type);
        $this->material = $material;
        $this->dimensions = $dimensions;
    }


    public function save($db)
    {
        $db->beginTransaction();

        try {
            // Insert new record into products table
            $stmt = $db->prepare("INSERT INTO products (sku, name, price, type) VALUES (?, ?, ?, ?)");
            $stmt->execute([$this->sku, $this->name, $this->price, $this->type]);
            $this->id = $db->lastInsertId();

            // Handle specific product table
            $this->saveSpecific($db);

            $db->commit();
        } catch (\PDOException $e) {
            $db->rollBack();
            http_response_code(409);

            $errorResponse = [
                'error' => 'Please enter a unique SKU'
            ];

            echo json_encode($errorResponse);
            exit;
        }
    }

    private function saveSpecific($db)
    {
        // Insert new record into furniture table
        $stmt = $db->prepare("INSERT INTO furniture (product_id, material, dimensions) VALUES (?, ?, ?)");
        $stmt->execute([$this->id, $this->material, $this->dimensions]);
    }

    public static function load($db)
    {
        $stmt = $db->query("
            SELECT p.id, p.sku, p.name, p.price, f.material, f.dimensions
            FROM products p
            JOIN furniture f ON p.id = f.product_id
        ");
        $data = $stmt->fetchAll();

        $furniture = [];
        foreach ($data as $row) {
            $f = new Furniture($row['sku'], $row['name'], $row['price'], $row['material'], $row['dimensions']);
            $f->id = $row['id']; // Assign the fetched ID to the object
            $furniture[] = $f;
        }

        return $furniture;
    }

    // Getters
    public function getMaterial()
    {
        return $this->material;
    }

    public function getDimensions()
    {
        return $this->dimensions;
    }

    // Setters

    public function setMaterial($material)
    {
        $this->material = $material;
    }

    public function setDimensions($dimensions)
    {
        $this->dimensions = $dimensions;
    }
}
