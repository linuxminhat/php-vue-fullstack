<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\BaseRepository;
use App\Models\OrderItem;
use PDO;

/**
 * OrderItem Repository
 * 
 * Handles all database operations for order items.
 */
class OrderItemRepository extends BaseRepository
{
    /**
     * Map database row to OrderItem entity
     */
    private function mapRow(array $row): OrderItem
    {
        $i = new OrderItem();
        $i->id = (int)$row['id'];
        $i->order_id = (int)$row['order_id'];
        $i->product_id = (int)$row['product_id'];
        $i->quantity = (int)$row['quantity'];
        $i->price_at_purchase = (float)$row['price_at_purchase'];
        $i->line_total = (float)$row['line_total'];
        $i->created_at = $row['created_at'];
        return $i;
    }

    /**
     * Find order items by order ID
     */
    public function findByOrderId(int $orderId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM order_items WHERE order_id = :order_id'
        );
        $stmt->execute(['order_id' => $orderId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn ($row) => $this->mapRow($row), $rows);
    }
}

