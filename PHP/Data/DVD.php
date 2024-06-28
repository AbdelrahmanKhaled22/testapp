<?php

namespace ProductData;

use Exception;

class DVD extends Product
{
    private $size;

    public function __construct($sku, $name, $price, $size, $type = "DVD")
    {
        parent::__construct($sku, $name, $price, $type);
        $this->size = $size;
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

            if ($e->errorInfo[1] == 1062) {
                http_response_code(409);

                $errorResponse = [
                    'error' => 'Please enter a unique SKU'
                ];

                echo json_encode($errorResponse);
                exit;
            } else {
                echo json_encode($e->getMessage());
            }
        }
    }

    private function saveSpecific($db)
    {
        // Insert new record into dvds table
        $stmt = $db->prepare("INSERT INTO dvds (product_id, size) VALUES (?, ?)");
        $stmt->execute([$this->id, $this->size]);
    }


    public static function load($db)
    {
        $stmt = $db->query("
            SELECT p.id, p.sku, p.name, p.price, d.size
            FROM products p
            JOIN dvds d ON p.id = d.product_id
        ");
        $data = $stmt->fetchAll();

        $dvds = [];
        foreach ($data as $row) {
            $dvd = new DVD($row['sku'], $row['name'], $row['price'], $row['size']);
            $dvd->id = $row['id']; // Assign the fetched ID to the object
            $dvds[] = $dvd;
        }

        return $dvds;
    }



    // Getters

    public function getSize()
    {
        return $this->size;
    }

    // Setters

    public function setSize($size)
    {
        $this->size = $size;
    }
}
