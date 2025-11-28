<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Core\Auth;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\User;

class StaffController
{
    // Check if user is staff or admin
    private function requireStaffAccess(): void
    {
        if (!Auth::isLoggedIn()) {
            header('Location: /auth/login');
            exit;
        }

        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'staff'])) {
            http_response_code(403);
            die("Access Denied - Staff only");
        }
    }

    // === PRODUCTS MANAGEMENT (Staff) ===
    public function products()
    {
        $this->requireStaffAccess();

        $productModel = new Product();
        $categoryModel = new Category();

        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $totalProducts = $productModel->countAll();
        $totalPages = ceil($totalProducts / $limit);

        $products = $productModel->getPaged($limit, $offset);
        $categories = $categoryModel->listAllCategory();

        View::render('staff.products.index', [
            'products' => $products,
            'categories' => $categories,
            'page' => $page,
            'totalPages' => $totalPages,
            'title' => 'Products Management (Staff)'
        ]);
    }

    // === ORDERS MANAGEMENT (Staff) ===
    public function orders()
    {
        $this->requireStaffAccess();

        $orderModel = new Order();
        $userModel = new User();

        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $limit = 15;
        $offset = ($page - 1) * $limit;

        // Get all orders
        $allOrders = $orderModel->listAll();
        $totalOrders = count($allOrders);
        $totalPages = ceil($totalOrders / $limit);

        // Paginate
        $orders = array_slice($allOrders, $offset, $limit);

        // Attach customer names to orders
        foreach ($orders as $order) {
            $user = $userModel->findById($order->customer_id);
            $order->customer_name = $user ? $user->full_name : 'Unknown';
        }

        View::render('staff.orders.index', [
            'orders' => $orders,
            'page' => $page,
            'totalPages' => $totalPages,
            'title' => 'Orders Management (Staff)'
        ]);
    }

    // Dashboard
    public function index()
    {
        $this->requireStaffAccess();

        header('Location: /staff/products');
        exit;
    }
}

