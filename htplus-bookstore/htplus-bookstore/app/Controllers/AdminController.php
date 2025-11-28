<?php

namespace App\Controllers;

use App\Core\View;
use App\Core\Auth;
use App\Services\UserService;
use App\Services\ProductService;
use App\Services\CategoryService;
use App\Services\OrderService;

class AdminController
{
    private UserService $userService;
    private ProductService $productService;
    private CategoryService $categoryService;
    private OrderService $orderService;

    public function __construct()
    {
        $this->userService = new UserService();
        $this->productService = new ProductService();
        $this->categoryService = new CategoryService();
        $this->orderService = new OrderService();
    }

    public function index()
    {
        if (!Auth::isAdmin()) {
            http_response_code(403);
            die("Access Denied");
        }

        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $limit = 10;

        $result = $this->userService->getPaginatedUsers($page, $limit);

        View::render('admin.users.index', [
            'users' => $result['users'],
            'page' => $result['pagination']['page'],
            'totalPages' => $result['pagination']['total_pages'],
            'title' => 'Admin Dashboard'
        ]);
    }

    public function products()
    {
        if (!Auth::isAdmin()) {
            http_response_code(403);
            die("Access Denied");
        }

        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $products = $this->productService->getAllProducts();
        $totalProducts = count($products);
        $totalPages = ceil($totalProducts / $limit);
        $paginatedProducts = array_slice($products, $offset, $limit);
        $categories = $this->categoryService->getAllCategories();

        View::render('admin.products.index', [
            'products' => $paginatedProducts,
            'categories' => $categories,
            'page' => $page,
            'totalPages' => $totalPages,
            'title' => 'Products Management'
        ]);
    }

    public function orders()
    {
        if (!Auth::isAdmin()) {
            http_response_code(403);
            die("Access Denied");
        }

        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $limit = 15;
        $offset = ($page - 1) * $limit;
        $allOrders = $this->orderService->getAllOrders();
        $totalOrders = count($allOrders);
        $totalPages = ceil($totalOrders / $limit);

        $orders = array_slice($allOrders, $offset, $limit);
        foreach ($orders as $order) {
            $user = $this->userService->getUserById($order->customer_id);
            $order->customer_name = $user ? $user->full_name : 'Unknown';
        }

        View::render('admin.orders.index', [
            'orders' => $orders,
            'page' => $page,
            'totalPages' => $totalPages,
            'title' => 'Orders Management'
        ]);
    }

    public function update()
    {
        if (!Auth::isAdmin()) {
            http_response_code(403);
            die("Access Denied");
        }

        $id = $_POST["id"] ?? null;

        if (!$id) {
            die("User not found");
        }

        $user = $this->userService->getUserById($id);
        $full_name = !empty($_POST["full_name"]) ? $_POST["full_name"] : $user->full_name;
        $role = !empty($_POST["role"]) ? $_POST["role"] : $user->role;
        $is_active = isset($_POST["is_active"]) ? $_POST["is_active"] : $user->is_active;
        $password = !empty($_POST["password"]) ? $_POST["password"] : null;

        if (!empty($_POST["password"])) {
            $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
        } else {
            $password = $user->password;
        }

        $email = $user->email;

        $this->userService->updateUser($id, [
            'email' => $email,
            'password' => $password,
            'full_name' => $full_name,
            'role' => $role,
            'is_active' => $is_active
        ]);

        header("Location: /admin/users");
        exit;
    }

    public function create()
    {
        if (!Auth::isAdmin()) {
            http_response_code(403);
            die("Access Denied");
        }

        $email = $_POST['email'] ?? null;
        $password = $_POST['password'] ?? null;
        $full_name = $_POST['full_name'] ?? null;
        $role = $_POST['role'] ?? 'customer';
        $is_active = $_POST['is_active'] ?? 1;

        if (empty($email) || empty($password)) {
            die("Email and password are required");
        }

        $this->userService->createUser([
            'email' => $email,
            'password' => $password,
            'full_name' => $full_name,
            'role' => $role,
            'is_active' => $is_active
        ]);

        header("Location: /admin/users");
        exit;
    }

    public function edit()
    {
        if (!Auth::isAdmin()) {
            http_response_code(403);
            die("Access Denied");
        }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($id <= 0) {
            header("Location: /admin/users");
            exit;
        }

        $user = $this->userService->getUserById($id);
        
        if (!$user) {
            header("Location: /admin/users");
            exit;
        }

        View::render('admin.users.edit', [
            'user' => $user,
            'title' => 'Edit User'
        ]);
    }

    public function delete()
    {
        if (!Auth::isAdmin()) {
            http_response_code(403);
            die("Access Denied");
        }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($id <= 0) {
            header("Location: /admin/users");
            exit;
        }
        if ($id === (int)Auth::id()) {
            header("Location: /admin/users?error=" . urlencode("Cannot delete your own account"));
            exit;
        }

        try {
            header("Location: /admin/users?success=" . urlencode("User deleted successfully"));
        } catch (\Exception $e) {
            header("Location: /admin/users?error=" . urlencode($e->getMessage()));
        }
        exit;
    }
}
