<?php

declare(strict_types=1);

namespace App\Models;
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
    public ?string $customer_name = null;
}
