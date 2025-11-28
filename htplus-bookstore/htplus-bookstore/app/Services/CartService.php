<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\CartRepository;
use App\Repositories\CartItemRepository;
use App\Repositories\ProductRepository;
use RuntimeException;

/**
 * Cart Service
 * 
 * Handles business logic for shopping cart.
 * Logic giữ nguyên 100% từ code cũ.
 */
class CartService
{
    private CartRepository $cartRepository;
    private CartItemRepository $cartItemRepository;
    private ProductRepository $productRepository;

    public function __construct()
    {
        $this->cartRepository = new CartRepository();
        $this->cartItemRepository = new CartItemRepository();
        $this->productRepository = new ProductRepository();
    }

    /**
     * Get or create active cart for customer
     */
    public function getOrCreateCart(int $customerId): int
    {
        return $this->cartRepository->getOrCreateCart($customerId);
    }

    /**
     * Get cart items with product details
     */
    public function getCartItems(int $cartId): array
    {
        return $this->cartItemRepository->listByCartId($cartId);
    }

    /**
     * Get cart total amount
     */
    public function getCartTotal(int $cartId): float
    {
        return $this->cartRepository->getTotal($cartId);
    }

    /**
     * Get cart items count
     */
    public function getCartItemsCount(int $cartId): int
    {
        return $this->cartItemRepository->countItems($cartId);
    }

    /**
     * Add product to cart
     */
    public function addToCart(int $customerId, int $productId, int $quantity): void
    {
        if ($quantity <= 0) {
            throw new RuntimeException("Quantity must be greater than 0");
        }

        // Get product and verify it exists
        $product = $this->productRepository->findById($productId);
        if (!$product) {
            throw new RuntimeException("Product not found");
        }

        // Check stock
        if ($product->stock < $quantity) {
            throw new RuntimeException("Insufficient stock");
        }

        // Get or create cart
        $cartId = $this->getOrCreateCart($customerId);

        // Add item to cart
        $this->cartItemRepository->addItem($cartId, $productId, $quantity, $product->price);

        // Sync cart total
        $this->cartRepository->syncTotalAmount($cartId);
    }

    /**
     * Update cart item quantity
     */
    public function updateCartItemQuantity(int $cartItemId, int $quantity, int $userId): void
    {
        // Verify ownership
        if (!$this->cartItemRepository->isOwner($cartItemId, $userId)) {
            throw new RuntimeException("Unauthorized");
        }

        // Update quantity
        $this->cartItemRepository->updateQuantity($cartItemId, $quantity);

        // Get cart_id and sync total
        $cartId = $this->cartItemRepository->getCartIdByItemId($cartItemId);
        if ($cartId) {
            $this->cartRepository->syncTotalAmount($cartId);
        }
    }

    /**
     * Remove item from cart
     */
    public function removeCartItem(int $cartItemId, int $userId): void
    {
        // Verify ownership
        if (!$this->cartItemRepository->isOwner($cartItemId, $userId)) {
            throw new RuntimeException("Unauthorized");
        }

        // Get cart_id before removing
        $cartId = $this->cartItemRepository->getCartIdByItemId($cartItemId);

        // Remove item
        $this->cartItemRepository->removeItem($cartItemId);

        // Sync cart total
        if ($cartId) {
            $this->cartRepository->syncTotalAmount($cartId);
        }
    }

    /**
     * Clear all items in cart
     */
    public function clearCart(int $cartId): void
    {
        $this->cartItemRepository->clear($cartId);
        $this->cartRepository->syncTotalAmount($cartId);
    }

    /**
     * Get active cart for customer
     */
    public function getActiveCart(int $customerId): ?int
    {
        $cart = $this->cartRepository->getActiveCart($customerId);
        return $cart ? $cart->id : null;
    }

    /**
     * Verify product price (security check)
     */
    public function verifyProductPrice(int $productId, float $price): bool
    {
        return $this->cartItemRepository->verifyProductPrice($productId, $price);
    }
}

