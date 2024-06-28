<?php

namespace ProductData;

abstract class Product
{
    protected $id;
    protected $sku;
    protected $name;
    protected $price;
    protected $type;


    protected function __construct(string $sku, string $name, int $price, string $type)
    {
        $this->sku = $sku;
        $this->name = $name;
        $this->price = $price;
        $this->type = $type;
    }

    abstract public function save($db);
    abstract public static function load($db);

    public static function deleteByIds($pdo, $ids)
    {
        try {
            $placeholders = rtrim(str_repeat('?,', count($ids)), ',');
            $stmt = $pdo->prepare("DELETE FROM products WHERE id IN ({$placeholders})");
            $stmt->execute($ids);
            return true; // Successful delete
        } catch (PDOException $e) {
            throw $e;
        }
    }


    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getSku()
    {
        return $this->sku;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPrice()
    {
        return $this->price;
    }

    // Setters
    public function setId($id)
    {
        $this->id = $id;
    }

    public function setSku($sku)
    {
        $this->sku = $sku;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setPrice($price)
    {
        $this->price = $price;
    }
}
