<?php

declare(strict_types=1);

namespace App\Models;

/**
 * CartItem Entity
 * 
 * Represents an item in a shopping cart.
 * This is a plain data object (entity) without any database logic.
 */
class CartItem
{
    public int $id;
    public int $cart_id;
    public int $product_id;
    public int $quantity;
    public float $price_at_add;
    public float $line_total;
    public string $created_at;
}
