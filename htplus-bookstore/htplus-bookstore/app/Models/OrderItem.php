<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\BaseModel;
use PDO;

class OrderItem extends BaseModel
{
    public int $id;
    public int $order_id;
    public int $product_id;
    public int $quantity;
    public float $price_at_purchase;
    public float $line_total;
    public string $created_at;

    private static function mapRow(array $row): self
    {
        $i = new self();
        $i->id               = (int)$row['id'];
        $i->order_id         = (int)$row['order_id'];
        $i->product_id       = (int)$row['product_id'];
        $i->quantity         = (int)$row['quantity'];
        $i->price_at_purchase = (float)$row['price_at_purchase'];
        $i->line_total       = (float)$row['line_total'];
        $i->created_at       = $row['created_at'];
        return $i;
    }

    public function findByOrderId(int $orderId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM order_items WHERE order_id = :order_id'
        );
        $stmt->execute(['order_id' => $orderId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn ($row) => self::mapRow($row), $rows);
    }
}
