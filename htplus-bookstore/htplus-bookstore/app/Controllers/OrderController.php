<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Auth;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\User;

class OrderController extends BaseController{
    
    public function showCheckout(): void
    {
        if (!Auth::isLoggedIn()) {
            header('Location: /auth/login');
            exit;
        }

        $userId = (int)Auth::id();
        $cartModel = new Cart();
        $cartItemModel = new CartItem();
        $userModel = new User();

        $cartId = $cartModel->getOrCreateCart($userId);
        $items = $cartItemModel->listByCartId($cartId);
        $total = $cartModel->getTotal($cartId);
        $user = $userModel->findById($userId);

        if (empty($items)) {
            header('Location: /cart');
            exit;
        }

        \App\Core\View::render('checkout.index', [
            'title' => 'Checkout - HTPLUS Book Store',
            'items' => $items,
            'total' => $total,
            'user' => $user,
        ], 'main');
    }

    public function processCheckout(): void
    {
        if (!Auth::isLoggedIn()) {
            $this->json(['success' => false, 'message' => 'Vui lòng đăng nhập'], 401);
            exit;
        }

        $userId = (int)Auth::id();
        $data = json_decode(file_get_contents("php://input"), true) ?? $_POST;

        $phone = trim($data['phone'] ?? '');
        $address = trim($data['shipping_address'] ?? '');

        if (empty($phone) || empty($address)) {
            $this->json([
                'success' => false,
                'message' => 'Vui lòng điền đầy đủ thông tin giao hàng'
            ], 422);
            return;
        }

        $cartModel = new Cart();
        $cartItemModel = new CartItem();
        $orderModel = new Order();

        $cartId = $cartModel->getOrCreateCart($userId);
        $cartItems = $cartItemModel->listByCartId($cartId);

        if (empty($cartItems)) {
            $this->json([
                'success' => false,
                'message' => 'Giỏ hàng trống'
            ], 400);
            return;
        }

        // Convert cart items to order items format
        $orderItems = [];
        foreach ($cartItems as $item) {
            $orderItems[] = [
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
            ];
        }

        try {
            $orderId = $orderModel->createOrder($userId, null, $orderItems, $phone, $address);

            // Clear cart after successful order
            $cartItemModel->clear($cartId);
            $cartModel->syncTotalAmount($cartId);
            $_SESSION['cart_count'] = 0;

            $this->json([
                'success' => true,
                'message' => 'Đặt hàng thành công!',
                'data' => [
                    'order_id' => $orderId,
                ]
            ], 201);
        } catch (\Exception $e) {
            $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
   
    public function createForCustomer(): void {
        $user = $this->requireRole(['customer']);
        $raw  = file_get_contents('php://input');
        $data = json_decode($raw, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            $data = $_POST;
        }

        $items = $data['items'] ?? [];
        $orderModel = new Order();

        try {
            $orderId = $orderModel->createOrder($user->id, null, $items);

            $this->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data'    => [
                    'order_id' => $orderId,
                ],
            ], 201);
        } catch (\Exception $e) {
            $this->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
    
    public function createForCustomerByStaff(): void
    {
        $staff = $this->requireRole(['admin', 'staff']);

        $raw  = file_get_contents('php://input');
        $data = json_decode($raw, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            $data = $_POST;
        }
        $customerId = (int)($data['customer_id'] ?? 0);
        $items      = $data['items'] ?? [];

        if ($customerId <= 0) {
            $this->json([
                'success' => false,
                'message' => 'customer_id is required',
            ], 422);
        }

        $orderModel = new Order();

        try {
            $orderId = $orderModel->createOrder($customerId, $staff->id, $items);

            $this->json([
                'success' => true,
                'message' => 'Order created by staff',
                'data'    => [
                    'order_id' => $orderId,
                ],
            ], 201);
        } catch (\Exception $e) {
            $this->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function myOrders(): void
    {
        if (!Auth::isLoggedIn()) {
            header('Location: /auth/login');
            exit;
        }

        $userId = (int)Auth::id();
        $orderModel = new Order();
        $orders = $orderModel->listByCustomer($userId);

        \App\Core\View::render('orders.my-orders', [
            'title' => 'My Orders - HTPLUS Book Store',
            'orders' => $orders,
        ], 'main');
    }

    public function myOrdersApi(): void
    {
        $user = $this->requireRole(['customer']);

        $orderModel = new Order();
        $orders = $orderModel->listByCustomer($user->id);

        $this->json([
            'success' => true,
            'data'    => $orders,
        ]);
    }

    public function myOrderDetail(): void
    {
        $user = $this->requireRole(['customer']);

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            $this->json([
                'success' => false,
                'message' => 'Invalid order id',
            ], 422);
        }

        $orderModel = new Order();
        $itemModel  = new OrderItem();

        $order = $orderModel->findByCustomer($id, $user->id);
        if (!$order) {
            $this->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        }

        $items = $itemModel->findByOrderId($order->id);

        $this->json([
            'success' => true,
            'data'    => [
                'order' => $order,
                'items' => $items,
            ],
        ]);
    }

    public function adminListOrders(): void
    {
        $this->requireRole(['admin', 'staff']);

        $orderModel = new Order();
        $orders = $orderModel->listAll();

        $this->json([
            'success' => true,
            'data'    => $orders,
        ]);
    }

    public function adminOrderDetail(): void{
        $this->requireRole(['admin', 'staff']);

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            $this->json([
                'success' => false,
                'message' => 'Invalid order id',
            ], 422);
        }

        $orderModel = new Order();
        $itemModel  = new OrderItem();

        $order = $orderModel->findById($id);
        if (!$order) {
            $this->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        }

        $items = $itemModel->findByOrderId($order->id);

        $this->json([
            'success' => true,
            'data'    => [
                'order' => $order,
                'items' => $items,
            ],
        ]);
    }

    public function adminUpdateStatus(): void{
        $this->requireRole(['admin', 'staff']);

        $raw  = file_get_contents('php://input');
        $data = json_decode($raw, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            $data = $_POST;
        }

        $id     = (int)($data['id'] ?? 0);
        $status = $data['status'] ?? '';

        $allowed = ['pending', 'confirmed', 'shipped', 'delivered', 'completed', 'cancelled'];

        if ($id <= 0 || !in_array($status, $allowed, true)) {
            $this->json([
                'success' => false,
                'message' => 'Invalid id or status',
            ], 422);
            return;
        }

        $orderModel = new Order();
        $order = $orderModel->findById($id);

        if (!$order) {
            $this->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
            return;
        }

        $orderModel->updateStatus($id, $status);

        $this->json([
            'success' => true,
            'message' => 'Order status updated',
        ]);
    }
}
