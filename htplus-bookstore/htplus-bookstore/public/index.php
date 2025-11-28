<?php

declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Core\Config;
use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Controllers\CategoryController;
use App\Controllers\ProductController;
use App\Controllers\OrderController;
use App\Controllers\AboutController;
use App\Controllers\AccountController;
use App\Controllers\AdminController;
use App\Controllers\CartController;


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

Config::loadEnv(__DIR__ . '/../env');
$router = new Router();

$router->get('/', [HomeController::class, 'index']);
$router->get('/products', [ProductController::class, 'shop']);

//Auth page
$router->get('/auth/login', [AuthController::class, 'showLogin']);
$router->get('/auth/register', [AuthController::class, 'showRegister']);
$router->get('/auth/logout', [AuthController::class, 'logout']);

//Auth API 
$router->post('/auth/login', [AuthController::class, 'login']); 
$router->post('/auth/logout', [AuthController::class, 'logout']);
$router->get('/auth/getAuthMe', [AuthController::class, 'getAuthMe']);
$router->post('/auth/createUser', [AuthController::class, 'createUser']);

//Admin Page 
$router->get('/admin', [AdminController::class, 'index']);

// Admin - Products Management (HTML View)
$router->get('/admin/products', [AdminController::class, 'products']);

// Admin - Orders Management (HTML View)
$router->get('/admin/orders', [AdminController::class, 'orders']);

//Category API (Admin)
$router->get('/admin/categories', [CategoryController::class, 'listAllCategory']);
$router->post('/admin/categories/create', [CategoryController::class, 'createNewCategory']);
$router->post('/admin/categories/update', [CategoryController::class, 'updateCategory']);
$router->post('/admin/categories/delete', [CategoryController::class, 'deleteCategory']);

//Product API (Admin/Staff)
$router->get('/admin/products/list', [ProductController::class, 'listAllProduct']);
$router->post('/admin/products/create', [ProductController::class, 'createNewProduct']);
$router->post('/admin/products/update', [ProductController::class, 'updateProduct']);
$router->post('/admin/products/delete', [ProductController::class, 'deleteProduct']);

//Admin Management 
$router->get('/admin/users', [AdminController::class, 'index']);
$router->post('/admin/users/create', [AdminController::class, 'create']);
$router->get('/admin/users/edit', [AdminController::class, 'edit']);
$router->get('/admin/users/delete', [AdminController::class, 'delete']);
$router->post('/admin/users/update', [AdminController::class, 'update']);

//Order API - Customer
$router->post('/orders', [OrderController::class, 'createForCustomer']);
$router->get('/orders/my', [OrderController::class, 'myOrders']);
$router->get('/orders/my/detail', [OrderController::class, 'myOrderDetail']);

//Order API - Admin/Staff
$router->post('/admin/orders/create', [OrderController::class, 'createForCustomerByStaff']);
$router->get('/admin/orders/list', [OrderController::class, 'adminListOrders']);
$router->get('/admin/orders/detail', [OrderController::class, 'adminOrderDetail']);
$router->post('/admin/orders/update-status', [OrderController::class, 'adminUpdateStatus']);

//About 
$router->get('/about', [AboutController::class, 'index']);

//Account 
$router->get('/account', [AccountController::class, 'index']);
$router->post('/account/update-profile', [AccountController::class, 'updateProfile']);
$router->post('/account/change-password', [AccountController::class, 'changePassword']);

//Product
$router->get('/product/{id}',[ProductController::class, 'detail']);

// Cart - Customer
$router->get('/cart', [CartController::class, 'index']);           
$router->post('/cart/add', [CartController::class, 'addToCart']);   
$router->post('/cart/update-qty', [CartController::class, 'updateQuantity']);  
$router->post('/cart/remove-item', [CartController::class, 'removeItem']);    

// Checkout
$router->get('/checkout', [OrderController::class, 'showCheckout']);
$router->post('/checkout', [OrderController::class, 'processCheckout']);

//Do routes 
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
