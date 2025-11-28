<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Auth;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;

class AdminController { 
    public function index() {

    if (!Auth::isAdmin()) {
        http_response_code(403);
        die("Access Denied");
    }

    $userModel = new User();

    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;
    $totalUsers = $userModel->countUsers();
    $totalPages = ceil($totalUsers / $limit);

    $users = $userModel->getPaginated($limit, $offset);

    View::render('admin.users.index', [
        'users' => $users,
        'page' => $page,
        'totalPages' => $totalPages,
        'title' => 'Admin Dashboard'
    ]);

}

    // === PRODUCTS MANAGEMENT ===
    public function products() {
        if (!Auth::isAdmin()) {
            http_response_code(403);
            die("Access Denied");
        }

        $productModel = new Product();
        $categoryModel = new Category();

        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        $totalProducts = $productModel->countAll();
        $totalPages = ceil($totalProducts / $limit);

        $products = $productModel->getPaged($limit, $offset);
        $categories = $categoryModel->listAllCategory();

        View::render('admin.products.index', [
            'products' => $products,
            'categories' => $categories,
            'page' => $page,
            'totalPages' => $totalPages,
            'title' => 'Products Management'
        ]);
    }

    // === ORDERS MANAGEMENT ===
    public function orders() {
        if (!Auth::isAdmin()) {
            http_response_code(403);
            die("Access Denied");
        }

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

        View::render('admin.orders.index', [
            'orders' => $orders,
            'page' => $page,
            'totalPages' => $totalPages,
            'title' => 'Orders Management'
        ]);
    }

    public function update() { 
    if(!Auth::isAdmin()){
        http_response_code(403);
        die("Access Denied");
    }

    $id = $_POST["id"] ?? null;

    if(!$id){
        die("User not found");
    }

    $userModel = new User();
    $user = $userModel->findById($id);
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

    $userModel->update($id, [
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

    $email      = $_POST['email'] ?? null;
    $password   = $_POST['password'] ?? null;
    $full_name  = $_POST['full_name'] ?? null;
    $role       = $_POST['role'] ?? 'customer';
    $is_active  = $_POST['is_active'] ?? 1;

    if (empty($email) || empty($password)) {
        die("Email and password are required");
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    $userModel = new User();

    $userId = $userModel->create([
        'email'      => $email,
        'password'   => $hashedPassword,
        'full_name'  => $full_name,
        'role'       => $role,
        'is_active'  => $is_active
    ]);

    header("Location: /admin/users");
    exit;
}
}
?>
