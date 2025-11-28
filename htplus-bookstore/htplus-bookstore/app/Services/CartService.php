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

        $product = $this->productRepository->findById($productId);
        if (!$product) {
            throw new RuntimeException("Product not found");
        }

        if ($product->stock < $quantity) {
            throw new RuntimeException("Insufficient stock");
        }
        $cartId = $this->getOrCreateCart($customerId);
        $this->cartItemRepository->addItem($cartId, $productId, $quantity, $product->price);
        $this->cartRepository->syncTotalAmount($cartId);
    }

    public function updateCartItemQuantity(int $cartItemId, int $quantity, int $userId): void
    {
        if (!$this->cartItemRepository->isOwner($cartItemId, $userId)) {
            throw new RuntimeException("Unauthorized");
        }
        $this->cartItemRepository->updateQuantity($cartItemId, $quantity);
        $cartId = $this->cartItemRepository->getCartIdByItemId($cartItemId);
        if ($cartId) {
            $this->cartRepository->syncTotalAmount($cartId);
        }
    }
    public function removeCartItem(int $cartItemId, int $userId): void
    {
        if (!$this->cartItemRepository->isOwner($cartItemId, $userId)) {
            throw new RuntimeException("Unauthorized");
        }
        $cartId = $this->cartItemRepository->getCartIdByItemId($cartItemId);
        $this->cartItemRepository->removeItem($cartItemId);
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

