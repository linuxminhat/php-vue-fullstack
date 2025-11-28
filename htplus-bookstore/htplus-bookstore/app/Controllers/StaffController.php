<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Core\Auth;
use App\Services\ProductService;
use App\Services\CategoryService;
use App\Services\OrderService;
use App\Services\UserService;

class StaffController
{
    private ProductService $productService;
    private CategoryService $categoryService;
    private OrderService $orderService;
    private UserService $userService;

    public function __construct()
    {
        $this->productService = new ProductService();
        $this->categoryService = new CategoryService();
        $this->orderService = new OrderService();
        $this->userService = new UserService();
    }

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

        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $products = $this->productService->getAllProducts();
        $totalProducts = count($products);
        $totalPages = ceil($totalProducts / $limit);
        
        // Paginate manually
        $paginatedProducts = array_slice($products, $offset, $limit);
        $categories = $this->categoryService->getAllCategories();

        View::render('staff.products.index', [
            'products' => $paginatedProducts,
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

        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $limit = 15;
        $offset = ($page - 1) * $limit;

        // Get all orders
        $allOrders = $this->orderService->getAllOrders();
        $totalOrders = count($allOrders);
        $totalPages = ceil($totalOrders / $limit);

        // Paginate
        $orders = array_slice($allOrders, $offset, $limit);

        // Attach customer names to orders
        foreach ($orders as $order) {
            $user = $this->userService->getUserById($order->customer_id);
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

