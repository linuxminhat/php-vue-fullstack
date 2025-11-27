<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\Order;
use App\Models\OrderItem;

class OrderController extends BaseController{
   
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

        $allowed = ['pending', 'completed', 'cancelled'];

        if ($id <= 0 || !in_array($status, $allowed, true)) {
            $this->json([
                'success' => false,
                'message' => 'Invalid id or status',
            ], 422);
        }

        $orderModel = new Order();
        $order = $orderModel->findById($id);

        if (!$order) {
            $this->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        }

        $orderModel->updateStatus($id, $status);

        $this->json([
            'success' => true,
            'message' => 'Order status updated',
        ]);
    }
}
