<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Auth;
use App\Services\UserService;
use App\Services\OrderService;
use RuntimeException;

class AccountController extends BaseController
{
    private UserService $userService;
    private OrderService $orderService;

    public function __construct()
    {
        $this->userService = new UserService();
        $this->orderService = new OrderService();
    }

    public function index(): void
    {
        if (!Auth::isLoggedIn()) {
            header('Location:/auth/login');
            exit();
        }
        $user = Auth::user();
        $orders = $this->orderService->getCustomerOrders($user->id);

        \App\Core\View::render('account/index', [
            'title' => 'My Account',
            'user' => $user,
            'orders' => $orders,
        ], 'main');
    }

    public function updateProfile(): void
    {
        if (!Auth::isLoggedIn()) {
            http_response_code(401);
            echo json_encode([
                "success" => false,
                "message" => "Bạn chưa đăng nhập",
            ]);
        }

        header('Content-Type: application/json');
        $full_name = trim($_POST["full_name"] ?? "");
        if ($full_name === '') {
            echo json_encode([
                "success" => false,
                "message" => "Tên không được để trống",
            ]);
            return;
        }

        $authUser = Auth::User();
        
        try {
            $affected = $this->userService->updateProfile($authUser->id, $full_name);
            if (!$affected) {
                $error = urlencode("Không có thay đổi nào được lưu");
                header("Location: /account?tab=details&error={$error}");
                exit;
            } else {
                $success = urlencode('Tên tài khoản đã được cập nhật, vui lòng đăng nhập lại.');
                header("Location: /auth/login?success={$success}");
            }
        } catch (RuntimeException $e) {
            $error = urlencode($e->getMessage());
            header("Location: /account?tab=details&error={$error}");
            exit;
        }
    }

    public function changePassword(): void
    {
        if (!Auth::isLoggedIn()) {
            header('Location: /auth/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location:/ account?tab=details');
            exit();
        }

        $currentPassword = $_POST["current_password"] ?? '';
        $newPassword = $_POST["new_password"] ?? '';
        $confirmedPassword = $_POST["confirmed_password"] ?? '';
        
        if ($currentPassword === '' || $newPassword === '' || $confirmedPassword === '') {
            $error = urlencode('Nhập đầy đủ các trường mật khẩu');
            header("Location: /account?tab=details&error={$error}");
            exit;
        }

        if (strlen($newPassword) < 6) {
            $error = urlencode("Mật khẩu phải có đủ 6 kí tự");
            header("Location: /account?tab=details&error={$error}");
            exit;
        }

        if ($newPassword !== $confirmedPassword) {
            $error = urlencode("Mật khẩu xác nhận phải trùng với mật khẩu mới");
            header("Location :/account?tab=details&eror={$error}");
        }

        $authUser = Auth::user();

        try {
            $this->userService->changePassword($authUser->id, $currentPassword, $newPassword);
            $success = urlencode("Mật khẩu đã được cập nhật, vui lòng đăng nhập lại");
            header("Location: /auth/login?success={$success}");
            exit;
        } catch (RuntimeException $e) {
            $error = urlencode($e->getMessage());
            header("Location: /account?tab=details&error={$error}");
            exit;
        }
    }
}
