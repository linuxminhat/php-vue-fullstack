<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\OrderRepository;
use App\Repositories\OrderItemRepository;
use App\Models\Order;
use RuntimeException;
use Exception;

/**
 * Order Service
 * 
 * Handles business logic for orders.
 */
class OrderService
{
    private OrderRepository $orderRepository;
    private OrderItemRepository $orderItemRepository;

    public function __construct()
    {
        $this->orderRepository = new OrderRepository();
        $this->orderItemRepository = new OrderItemRepository();
    }

    /**
     * Get order by ID
     */
    public function getOrderById(int $id): ?Order
    {
        return $this->orderRepository->findById($id);
    }

    /**
     * Get order by ID for specific customer
     */
    public function getCustomerOrder(int $id, int $customerId): ?Order
    {
        return $this->orderRepository->findByCustomer($id, $customerId);
    }

    /**
     * Get all orders by customer
     */
    public function getCustomerOrders(int $customerId): array
    {
        return $this->orderRepository->listByCustomer($customerId);
    }

    /**
     * Get all orders (admin)
     */
    public function getAllOrders(): array
    {
        return $this->orderRepository->findAll();
    }

    /**
     * Get order items
     */
    public function getOrderItems(int $orderId): array
    {
        return $this->orderItemRepository->findByOrderId($orderId);
    }

    /**
     * Create order for customer
     */
    public function createOrder(int $customerId, array $items, ?string $phone = null, ?string $shippingAddress = null, ?int $createdBy = null): int
    {
        // Validate items
        if (empty($items)) {
            throw new RuntimeException("Order must contain at least one item");
        }

        // Validate phone and address if provided
        if (!empty($phone) && strlen($phone) < 10) {
            throw new RuntimeException("Invalid phone number");
        }

        try {
            return $this->orderRepository->createOrder($customerId, $createdBy, $items, $phone, $shippingAddress);
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    /**
     * Update order status
     */
    public function updateOrderStatus(int $id, string $status): bool
    {
        $validStatuses = ['pending', 'confirmed', 'shipping', 'shipped', 'delivered', 'completed', 'cancelled'];
        
        if (!in_array($status, $validStatuses)) {
            throw new RuntimeException("Trạng thái đơn hàng không hợp lệ");
        }

        $order = $this->orderRepository->findById($id);
        if (!$order) {
            throw new RuntimeException("Không tìm thấy đơn hàng");
        }

        $rowsAffected = $this->orderRepository->updateStatus($id, $status);
        return $rowsAffected > 0;
    }

    /**
     * Get order with items (combined data)
     */
    public function getOrderDetails(int $orderId): array
    {
        $order = $this->orderRepository->findById($orderId);
        if (!$order) {
            throw new RuntimeException("Order not found");
        }

        $items = $this->orderItemRepository->findByOrderId($orderId);

        return [
            'order' => $order,
            'items' => $items,
        ];
    }
}

