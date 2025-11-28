<?php

declare(strict_types=1);

namespace App\Models;

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
