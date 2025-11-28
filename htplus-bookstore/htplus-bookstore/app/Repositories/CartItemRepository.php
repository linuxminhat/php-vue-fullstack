<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\BaseRepository;
use App\Models\CartItem;
use PDO;

/**
 * CartItem Repository
 * 
 * Handles all database operations for cart items.
 * Logic giữ nguyên 100% từ CartItem Model cũ.
 */
class CartItemRepository extends BaseRepository
{
    /**
     * Map database row to CartItem entity
     */
    private function mapRow(array $row): CartItem
    {
        $ci = new CartItem();
        $ci->id = (int)$row['id'];
        $ci->cart_id = (int)$row['cart_id'];
        $ci->product_id = (int)$row['product_id'];
        $ci->quantity = (int)$row['quantity'];
        $ci->price_at_add = (float)$row['price_at_add'];
        $ci->line_total = (float)$row['line_total'];
        $ci->created_at = $row['created_at'];
        return $ci;
    }

    /**
     * Find cart item by cart_id and product_id
     */
    public function findItem(int $cart_id, int $product_id): ?CartItem
    {
        $stmt = $this->db->prepare("
            SELECT * FROM cart_items
            WHERE cart_id = :cid AND product_id = :pid
            LIMIT 1
        ");
        $stmt->execute(['cid' => $cart_id, 'pid' => $product_id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->mapRow($row) : null;
    }

    /**
     * Add item to cart (or update if exists)
     */
    public function addItem(int $cart_id, int $product_id, int $quantity, float $price): void
    {
        $existing = $this->findItem($cart_id, $product_id);
        $lineTotal = $quantity * $price;

        if ($existing) {
            $stmt = $this->db->prepare("
                UPDATE cart_items
                SET quantity = quantity + :q,
                    line_total = line_total + :lt
                WHERE id = :id
            ");
            $stmt->execute([
                'q' => $quantity,
                'lt' => $lineTotal,
                'id' => $existing->id
            ]);
            return;
        }

        $stmt = $this->db->prepare("
            INSERT INTO cart_items (cart_id, product_id, quantity, price_at_add, line_total)
            VALUES (:cid, :pid, :q, :price, :lt)
        ");
        $stmt->execute([
            'cid' => $cart_id,
            'pid' => $product_id,
            'q' => $quantity,
            'price' => $price,
            'lt' => $lineTotal
        ]);
    }

    /**
     * Update cart item quantity
     */
    public function updateQuantity(int $id, int $quantity): void
    {
        if ($quantity <= 0) {
            $this->removeItem($id);
            return;
        }

        $stmt = $this->db->prepare("
            UPDATE cart_items
            SET quantity = :q,
                line_total = price_at_add * :q
            WHERE id = :id
        ");
        $stmt->execute(['q' => $quantity, 'id' => $id]);
    }

    /**
     * Remove cart item
     */
    public function removeItem(int $id): void
    {
        $stmt = $this->db->prepare("DELETE FROM cart_items WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    /**
     * Clear all items in cart
     */
    public function clear(int $cart_id): void
    {
        $stmt = $this->db->prepare("DELETE FROM cart_items WHERE cart_id = :cid");
        $stmt->execute(['cid' => $cart_id]);
    }

    /**
     * Get cart_id by cart_item_id
     */
    public function getCartIdByItemId(int $item_id): ?int
    {
        $stmt = $this->db->prepare("SELECT cart_id FROM cart_items WHERE id = :id");
        $stmt->execute(['id' => $item_id]);
        $result = $stmt->fetchColumn();
        return $result ? (int)$result : null;
    }

    /**
     * Verify product price matches database
     */
    public function verifyProductPrice(int $productId, float $priceInput): bool
    {
        $stmt = $this->db->prepare("
            SELECT price FROM products WHERE id = :id LIMIT 1
        ");
        $stmt->execute(['id' => $productId]);

        $dbPrice = $stmt->fetchColumn();
        if ($dbPrice === false) return false;

        return (float)$dbPrice === $priceInput;
    }

    /**
     * Check if user owns the cart item
     */
    public function isOwner(int $cartItemId, int $userId): bool
    {
        $stmt = $this->db->prepare("SELECT cart_id FROM cart_items WHERE id = :id");
        $stmt->execute(['id' => $cartItemId]);
        $cartId = $stmt->fetchColumn();

        if (!$cartId) return false;

        $stmt = $this->db->prepare("
            SELECT customer_id FROM carts WHERE id = :cid
        ");
        $stmt->execute(['cid' => $cartId]);

        $owner = $stmt->fetchColumn();
        return ((int)$owner === (int)$userId);
    }

    /**
     * List all items in cart with product details
     */
    public function listByCartId(int $cart_id): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                ci.id AS cart_item_id,
                ci.cart_id,
                ci.product_id,
                ci.quantity,
                ci.price_at_add AS price,
                ci.line_total,
                p.name,
                p.image
            FROM cart_items ci
            JOIN products p ON p.id = ci.product_id
            WHERE ci.cart_id = :cid
        ");

        $stmt->execute(['cid' => $cart_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Count total items quantity in cart
     */
    public function countItems(int $cart_id): int
    {
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(quantity), 0)
            FROM cart_items
            WHERE cart_id = :cid
        ");
        $stmt->execute(['cid' => $cart_id]);

        return (int)$stmt->fetchColumn();
    }
}

