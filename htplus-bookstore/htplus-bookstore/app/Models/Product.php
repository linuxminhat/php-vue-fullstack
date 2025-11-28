<?php

declare(strict_types=1);

namespace App\Models;

class Product
{
    public int $id;
    public ?int $category_id;
    public string $sku;
    public string $name;
    public ?string $author;
    public ?string $publisher;
    public ?string $isbn;
    public float $price;
    public int $stock;
    public ?string $image;
    public string $created_at;
    public ?string $updated_at;
    public string $description;
}
