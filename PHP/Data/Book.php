<?php

namespace ProductData;

class Book extends Product
{

    private $author;
    private $weight;

    public function __construct($sku, $name, $price, $author, $weight, $type = "Book")
    {
        parent::__construct($sku, $name, $price, $type);
        $this->author = $author;
        $this->weight = $weight;
    }


    public function save($db)
    {
        $db->beginTransaction();

        try {
            // Insert new record into products table
            $stmt = $db->prepare("INSERT INTO products (sku, name, price, type) VALUES (?, ?, ?, ?)");
            $stmt->execute([$this->sku, $this->name, $this->price, $this->type]);
            $this->id = $db->lastInsertId();

            // Handle specific product table (e.g., books)
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
        // Insert new record into books table
        $stmt = $db->prepare("INSERT INTO books (product_id, author, weight) VALUES (?, ?, ?)");
        $stmt->execute([$this->id, $this->author, $this->weight]);
    }

    public static function load($db)
    {
        $stmt = $db->query("
            SELECT p.id, p.sku, p.name, p.price, b.author, b.weight
            FROM products p
            JOIN books b ON p.id = b.product_id
        ");
        $data = $stmt->fetchAll();

        $books = [];
        foreach ($data as $row) {
            $book = new Book($row['sku'], $row['name'], $row['price'], $row['author'], $row['weight']);
            $book->id = $row['id']; // Assign the fetched ID to the object
            $books[] = $book;
        }

        return $books;
    }

    // Getters
    public function getAuthor()
    {
        return $this->author;
    }

    public function getWeight()
    {
        return $this->weight;
    }

    // Setters

    public function setAuthor($author)
    {
        $this->author = $author;
    }

    public function setWeight($weight)
    {
        $this->weight = $weight;
    }
}
