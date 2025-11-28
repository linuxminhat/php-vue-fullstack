<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Auth;
use App\Services\CartService;
use RuntimeException;

class CartController extends BaseController
{
    private CartService $cartService;

    public function __construct()
    {
        $this->cartService = new CartService();
    }

    private function authUserId(): int
    {
        if (!Auth::isLoggedIn()) {
            $this->json(['success' => false, 'message' => 'Vui lòng đăng nhập'], 401);
            exit;
        }
        return (int)Auth::id();
    }

    public function index(): void
    {
        $userId = $this->authUserId();

        $cartId = $this->cartService->getOrCreateCart($userId);
        $items = $this->cartService->getCartItems($cartId);
        $total = $this->cartService->getCartTotal($cartId);

        \App\Core\View::render('cart.index', [
            'items' => $items,
            'total' => $total
        ]);
    }

    public function myCart(): void
    {
        $userId = $this->authUserId();

        $cartId = $this->cartService->getOrCreateCart($userId);
        $items = $this->cartService->getCartItems($cartId);

        $this->json([
            'success' => true,
            'data' => [
                'cart_id' => $cartId,
                'items' => $items
            ]
        ]);
    }

    public function addToCart(): void
    {
        $userId = $this->authUserId();

        $data = json_decode(file_get_contents("php://input"), true) ?? $_POST;
        $productId = (int)($data['product_id'] ?? 0);
        $quantity = (int)($data['quantity'] ?? 1);
        $price = (float)($data['price'] ?? 0);

        if ($productId <= 0 || $quantity <= 0 || $price <= 0) {
            $this->json(['success' => false, 'message' => 'Dữ liệu không hợp lệ'], 422);
            return;
        }

        if (!$this->cartService->verifyProductPrice($productId, $price)) {
            $this->json(['success' => false, 'message' => 'Giá sản phẩm không đúng'], 400);
            return;
        }

        try {
            $this->cartService->addToCart($userId, $productId, $quantity);

            $cartId = $this->cartService->getOrCreateCart($userId);
            $_SESSION['cart_count'] = $this->cartService->getCartItemsCount($cartId);

            $this->json([
                'success' => true,
                'message' => 'Đã thêm vào giỏ hàng',
                'cart_count' => $_SESSION['cart_count']
            ], 201);
        } catch (RuntimeException $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function updateQuantity(): void
    {
        $userId = $this->authUserId();

        $data = json_decode(file_get_contents("php://input"), true) ?? $_POST;
        $cartItemId = (int)($data['cart_item_id'] ?? 0);
        $quantity = (int)($data['quantity'] ?? 0);

        if ($cartItemId <= 0 || $quantity < 0) {
            $this->json(['success' => false, 'message' => 'Dữ liệu không hợp lệ'], 422);
            return;
        }

        try {
            $this->cartService->updateCartItemQuantity($cartItemId, $quantity, $userId);

            $cartId = $this->cartService->getOrCreateCart($userId);
            $_SESSION['cart_count'] = $this->cartService->getCartItemsCount($cartId);

            $this->json(['success' => true, 'message' => 'Đã cập nhật']);
        } catch (RuntimeException $e) {
            $statusCode = $e->getMessage() === 'Unauthorized' ? 403 : 404;
            $this->json(['success' => false, 'message' => $e->getMessage()], $statusCode);
        }
    }

    public function removeItem(): void
    {
        $userId = $this->authUserId();

        $data = json_decode(file_get_contents("php://input"), true) ?? $_POST;
        $cartItemId = (int)($data['cart_item_id'] ?? 0);

        if ($cartItemId <= 0) {
            $this->json(['success' => false, 'message' => 'ID không hợp lệ'], 422);
            return;
        }

        try {
            $this->cartService->removeCartItem($cartItemId, $userId);

            $cartId = $this->cartService->getOrCreateCart($userId);
            $_SESSION['cart_count'] = $this->cartService->getCartItemsCount($cartId);

            $this->json(['success' => true, 'message' => 'Đã xóa sản phẩm']);
        } catch (RuntimeException $e) {
            $statusCode = $e->getMessage() === 'Unauthorized' ? 403 : 404;
            $this->json(['success' => false, 'message' => $e->getMessage()], $statusCode);
        }
    }

    public function clearCart(): void
    {
        $userId = $this->authUserId();

        $cartId = $this->cartService->getOrCreateCart($userId);
        $this->cartService->clearCart($cartId);
        $_SESSION['cart_count'] = 0;

        $this->json(['success' => true, 'message' => 'Đã xóa toàn bộ giỏ hàng']);
    }
}
