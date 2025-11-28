<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Cart Entity
 * 
 * Represents a shopping cart in the system.
 * This is a plain data object (entity) without any database logic.
 */
class Cart
{
    public int $id;
    public int $customer_id;
    public ?int $created_by;
    public string $status;
    public float $total_amount;
    public string $created_at;
    public ?string $updated_at;
}
