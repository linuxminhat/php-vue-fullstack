<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\CartRepository;
use App\Repositories\CartItemRepository;
use App\Repositories\ProductRepository;
use RuntimeException;
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
    public function getOrCreateCart(int $customerId): int
    {
        return $this->cartRepository->getOrCreateCart($customerId);
    }
    public function getCartItems(int $cartId): array
    {
        return $this->cartItemRepository->listByCartId($cartId);
    }
    public function getCartTotal(int $cartId): float
    {
        return $this->cartRepository->getTotal($cartId);
    }
    public function getCartItemsCount(int $cartId): int
    {
        return $this->cartItemRepository->countItems($cartId);
    }
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
    public function clearCart(int $cartId): void
    {
        $this->cartItemRepository->clear($cartId);
        $this->cartRepository->syncTotalAmount($cartId);
    }
    public function getActiveCart(int $customerId): ?int
    {
        $cart = $this->cartRepository->getActiveCart($customerId);
        return $cart ? $cart->id : null;
    }
    public function verifyProductPrice(int $productId, float $price): bool
    {
        return $this->cartItemRepository->verifyProductPrice($productId, $price);
    }
}

