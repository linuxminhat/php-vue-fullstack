<?php

declare(strict_types=1);

namespace App\Models;
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
