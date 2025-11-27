<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Auth;
use App\Models\Cart;
use App\Models\CartItem;

class CartController extends BaseController
{
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

        $cartModel     = new Cart();
        $cartItemModel = new CartItem();

        $cartId = $cartModel->getOrCreateCart($userId);
        $items  = $cartItemModel->listByCartId($cartId);
        $total  = $cartModel->getTotal($cartId);

        \App\Core\View::render('cart.index', [
            'items' => $items,
            'total' => $total
        ]);
    }

    public function myCart(): void
    {
        $userId = $this->authUserId();

        $cartModel     = new Cart();
        $cartItemModel = new CartItem();

        $cartId = $cartModel->getOrCreateCart($userId);
        $items  = $cartItemModel->listByCartId($cartId);

        $this->json([
            'success' => true,
            'data'    => [
                'cart_id' => $cartId,
                'items'   => $items
            ]
        ]);
    }

    public function addToCart(): void
    {
        $userId = $this->authUserId();

        $data      = json_decode(file_get_contents("php://input"), true) ?? $_POST;
        $productId = (int)($data['product_id'] ?? 0);
        $quantity  = (int)($data
        ['quantity'] ?? 1);
        $price     = (float)($data['price'] ?? 0);

        if ($productId <= 0 || $quantity <= 0 || $price <= 0) {
            $this->json(['success' => false, 'message' => 'Dữ liệu không hợp lệ'], 422);
            return;
        }
        $cartModel = new Cart();
        $cartItemModel = new CartItem();

        if (!$cartItemModel->verifyProductPrice($productId, $price)) {
            $this->json(['success' => false, 'message' => 'Giá sản phẩm không đúng'], 400);
            return;
        }

        $cartId = $cartModel->getOrCreateCart($userId);
        $cartItemModel->addItem($cartId, $productId, $quantity, $price);
        $cartModel->syncTotalAmount($cartId);

        $_SESSION['cart_count'] = $cartItemModel->countItems($cartId);

        $this->json([
            'success'    => true,
            'message'    => 'Đã thêm vào giỏ hàng',
            'cart_count' => $_SESSION['cart_count']
        ], 201);
    }

    public function updateQuantity(): void
    {
        $userId = $this->authUserId();

        $data       = json_decode(file_get_contents("php://input"), true) ?? $_POST;
        $cartItemId = (int)($data['cart_item_id'] ?? 0);
        $quantity   = (int)($data['quantity'] ?? 0);

        if ($cartItemId <= 0 || $quantity < 0) {
            $this->json(['success' => false, 'message' => 'Dữ liệu không hợp lệ'], 422);
            return;
        }

        $cartItemModel = new CartItem();
        if (!$cartItemModel->isOwner($cartItemId, $userId)) {
            $this->json(['success' => false, 'message' => 'Không có quyền'], 403);
            return;
        }

        $cartId = $cartItemModel->getCartIdByItemId($cartItemId);
        if (!$cartId) {
            $this->json(['success' => false, 'message' => 'Item không tồn tại'], 404);
            return;
        }

        $cartItemModel->updateQuantity($cartItemId, $quantity);
        (new Cart())->syncTotalAmount($cartId);

        $_SESSION['cart_count'] = $cartItemModel->countItems($cartId);

        $this->json(['success' => true, 'message' => 'Đã cập nhật']);
    }

    public function removeItem(): void
    {
        $userId = $this->authUserId();

        $data       = json_decode(file_get_contents("php://input"), true) ?? $_POST;
        $cartItemId = (int)($data['cart_item_id'] ?? 0);

        if ($cartItemId <= 0) {
            $this->json(['success' => false, 'message' => 'ID không hợp lệ'], 422);
            return;
        }

        $cartItemModel = new CartItem();

        if (!$cartItemModel->isOwner($cartItemId, $userId)) {
            $this->json(['success' => false, 'message' => 'Không có quyền'], 403);
            return;
        }

        $cartId = $cartItemModel->getCartIdByItemId($cartItemId);
        if (!$cartId) {
            $this->json(['success' => false, 'message' => 'Item không tồn tại'], 404);
            return;
        }

        $cartItemModel->removeItem($cartItemId);
        (new Cart())->syncTotalAmount($cartId);

        $_SESSION['cart_count'] = $cartItemModel->countItems($cartId);

        $this->json(['success' => true, 'message' => 'Đã xóa sản phẩm']);
    }

    public function clearCart(): void
    {
        $userId = $this->authUserId();

        $cartModel     = new Cart();
        $cartItemModel = new CartItem();

        $cartId = $cartModel->getOrCreateCart($userId);
        $cartItemModel->clear($cartId);
        $cartModel->syncTotalAmount($cartId);
        $_SESSION['cart_count'] = 0;

        $this->json(['success' => true, 'message' => 'Đã xóa toàn bộ giỏ hàng']);
    }
    
    public function adminListCarts(): void
    {
        $this->requireRole(['admin', 'staff']);
        $this->json(['success' => true, 'data' => (new Cart())->listAll()]);
    }

    public function adminCartDetail(): void
    {
        $this->requireRole(['admin', 'staff']);
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            $this->json(['success' => false, 'message' => 'Invalid id'], 422);
        }

        $cartModel     = new Cart();
        $cartItemModel = new CartItem();
        $cart          = $cartModel->findById($id);

        if (!$cart) {
            $this->json(['success' => false, 'message' => 'Not found'], 404);
        }

        $this->json([
            'success' => true,
            'data'    => [
                'cart'  => $cart,
                'items' => $cartItemModel->listByCartId($id)
            ]
        ]);
    }
}