<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Auth;
use App\Services\OrderService;
use App\Services\CartService;
use App\Services\UserService;
use RuntimeException;

class OrderController extends BaseController
{
    private OrderService $orderService;
    private CartService $cartService;
    private UserService $userService;

    public function __construct()
    {
        $this->orderService = new OrderService();
        $this->cartService = new CartService();
        $this->userService = new UserService();
    }

    public function showCheckout(): void
    {
        if (!Auth::isLoggedIn()) {
            header('Location: /auth/login');
            exit;
        }

        $userId = (int)Auth::id();

        $cartId = $this->cartService->getOrCreateCart($userId);
        $items = $this->cartService->getCartItems($cartId);
        $total = $this->cartService->getCartTotal($cartId);
        $user = $this->userService->getUserById($userId);

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

        $cartId = $this->cartService->getOrCreateCart($userId);
        $cartItems = $this->cartService->getCartItems($cartId);

        if (empty($cartItems)) {
            $this->json([
                'success' => false,
                'message' => 'Giỏ hàng trống'
            ], 400);
            return;
        }
        $orderItems = [];
        foreach ($cartItems as $item) {
            $orderItems[] = [
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
            ];
        }

        try {
            $orderId = $this->orderService->createOrder($userId, $orderItems, $phone, $address);
            $this->cartService->clearCart($cartId);
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

    public function createForCustomer(): void
    {
        $user = $this->requireRole(['customer']);
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            $data = $_POST;
        }

        $items = $data['items'] ?? [];

        try {
            $orderId = $this->orderService->createOrder($user->id, $items);

            $this->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => [
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

        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            $data = $_POST;
        }
        $customerId = (int)($data['customer_id'] ?? 0);
        $items = $data['items'] ?? [];

        if ($customerId <= 0) {
            $this->json([
                'success' => false,
                'message' => 'customer_id is required',
            ], 422);
        }

        try {
            $orderId = $this->orderService->createOrder($customerId, $items, null, null, $staff->id);

            $this->json([
                'success' => true,
                'message' => 'Order created by staff',
                'data' => [
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
        $orders = $this->orderService->getCustomerOrders($userId);

        \App\Core\View::render('orders.my-orders', [
            'title' => 'My Orders - HTPLUS Book Store',
            'orders' => $orders,
        ], 'main');
    }

    public function showOrderDetail($id): void
    {
        if (!Auth::isLoggedIn()) {
            header('Location: /auth/login');
            exit;
        }

        $orderId = (int)$id;
        $userId = (int)Auth::id();

        try {
            $order = $this->orderService->getCustomerOrder($orderId, $userId);
            
            if (!$order) {
                http_response_code(404);
                echo "Đơn hàng không tồn tại hoặc không thuộc về bạn.";
                return;
            }
            $items = $this->orderService->getOrderItems($orderId);

            \App\Core\View::render('orders.order-detail', [
                'title' => 'Chi tiết đơn hàng #' . str_pad((string)$orderId, 6, '0', STR_PAD_LEFT),
                'order' => $order,
                'items' => $items,
            ], 'main');

        } catch (RuntimeException $e) {
            http_response_code(404);
            echo $e->getMessage();
        }
    }

    public function myOrdersApi(): void
    {
        $user = $this->requireRole(['customer']);

        $orders = $this->orderService->getCustomerOrders($user->id);

        $this->json([
            'success' => true,
            'data' => $orders,
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

        try {
            $order = $this->orderService->getCustomerOrder($id, $user->id);
            if (!$order) {
                $this->json([
                    'success' => false,
                    'message' => 'Order not found',
                ], 404);
                return;
            }

            $items = $this->orderService->getOrderItems($order->id);

            $this->json([
                'success' => true,
                'data' => [
                    'order' => $order,
                    'items' => $items,
                ],
            ]);
        } catch (RuntimeException $e) {
            $this->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    public function adminListOrders(): void
    {
        $this->requireRole(['admin', 'staff']);

        $orders = $this->orderService->getAllOrders();

        $this->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    public function adminOrderDetail(): void
    {
        $this->requireRole(['admin', 'staff']);

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            $this->json([
                'success' => false,
                'message' => 'Invalid order id',
            ], 422);
        }

        try {
            $orderDetails = $this->orderService->getOrderDetails($id);

            $this->json([
                'success' => true,
                'data' => $orderDetails,
            ]);
        } catch (RuntimeException $e) {
            $this->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    public function adminUpdateStatus(): void
    {
        $this->requireRole(['admin', 'staff']);

        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            $data = $_POST;
        }

        $id = (int)($data['id'] ?? 0);
        $status = $data['status'] ?? '';

        $allowed = ['pending', 'confirmed', 'shipped', 'delivered', 'completed', 'cancelled'];

        if ($id <= 0 || !in_array($status, $allowed, true)) {
            $this->json([
                'success' => false,
                'message' => 'Mã đơn hàng hoặc trạng thái không hợp lệ',
            ], 422);
            return;
        }

        try {
            $this->orderService->updateOrderStatus($id, $status);

            $this->json([
                'success' => true,
                'message' => 'Đã cập nhật trạng thái đơn hàng thành công!',
            ]);
        } catch (RuntimeException $e) {
            $this->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }
}
