<?php

declare(strict_types=1);

namespace App\Models;

/**
 * OrderItem Entity
 * 
 * Represents an item in an order.
 * This is a plain data object (entity) without any database logic.
 */
class OrderItem
{
    public int $id;
    public int $order_id;
    public int $product_id;
    public int $quantity;
    public float $price_at_purchase;
    public float $line_total;
    public string $created_at;
}
