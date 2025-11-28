<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Order Entity
 * 
 * Represents an order in the system.
 * This is a plain data object (entity) without any database logic.
 */
class Order
{
    public int $id;
    public int $customer_id;
    public ?string $phone;
    public ?string $shipping_address;
    public ?int $created_by;
    public string $status;
    public float $total_amount;
    public string $created_at;
    public ?string $updated_at;

    // For admin display
    public ?string $customer_name = null;
}
