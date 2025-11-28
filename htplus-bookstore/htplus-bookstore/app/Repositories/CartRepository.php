<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\BaseRepository;
use App\Models\Cart;
use PDO;

/**
 * Cart Repository
 * 
 * Handles all database operations for carts.
 * Logic giữ nguyên 100% từ Cart Model cũ.
 */
class CartRepository extends BaseRepository
{
    /**
     * Map database row to Cart entity
     */
    private function mapRow(array $row): Cart
    {
        $c = new Cart();
        $c->id = (int)$row["id"];
        $c->customer_id = (int)$row["customer_id"];
        $c->created_by = $row["created_by"] !== null ? (int)$row["created_by"] : null;
        $c->status = $row["status"];
        $c->total_amount = (float)$row["total_amount"];
        $c->created_at = $row["created_at"];
        $c->updated_at = $row["updated_at"];
        return $c;
    }

    /**
     * Get active cart for customer
     */
    public function getActiveCart(int $customer_id): ?Cart
    {
        $stmt = $this->db->prepare("
            SELECT * FROM carts
            WHERE customer_id = :cid AND status = 'active'
            LIMIT 1
        ");
        $stmt->execute(['cid' => $customer_id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->mapRow($row) : null;
    }

    /**
     * Find cart by ID
     */
    public function findById(int $id): ?Cart
    {
        $stmt = $this->db->prepare("SELECT * FROM carts WHERE id = :id");
        $stmt->execute(["id" => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapRow($row) : null;
    }

    /**
     * Create new cart
     */
    public function createCart(int $customer_id, ?int $created_by = null): int
    {
        $existing = $this->getActiveCart($customer_id);
        if ($existing) return $existing->id;

        $stmt = $this->db->prepare("
            INSERT INTO carts (customer_id, created_by, status, total_amount)
            VALUES (:cid, :cb, 'active', 0)
        ");

        $stmt->execute([
            'cid' => $customer_id,
            'cb' => $created_by
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Sync cart total amount based on items
     */
    public function syncTotalAmount(int $cart_id): void
    {
        $stmt = $this->db->prepare("
            UPDATE carts
            SET total_amount = (
                SELECT COALESCE(SUM(line_total), 0)
                FROM cart_items
                WHERE cart_id = :cid
            )
            WHERE id = :cid
        ");

        $stmt->execute(["cid" => $cart_id]);
    }

    /**
     * List all carts
     */
    public function listAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM carts ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get or create cart for customer
     */
    public function getOrCreateCart(int $customer_id): int
    {
        $cart = $this->getActiveCart($customer_id);
        if ($cart) {
            return $cart->id;
        }
        return $this->createCart($customer_id);
    }

    /**
     * Get total amount of cart
     */
    public function getTotal(int $cart_id): float
    {
        $cart = $this->findById($cart_id);
        return $cart ? $cart->total_amount : 0.0;
    }
}

