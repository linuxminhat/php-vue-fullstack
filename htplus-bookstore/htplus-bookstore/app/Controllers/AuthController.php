<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Auth;
use App\Services\UserService;
use RuntimeException;

class AuthController extends BaseController
{
    private UserService $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    //view form login 
    public function showLogin(): void
    {
        \App\Core\View::render('auth.login', [
            'title' => 'Login'
        ], 'auth');
    }

    //view form register 
    public function showRegister(): void
    {
        \App\Core\View::render('auth.register', [
            'title' => 'Sign up'
        ], 'auth');
    }

    public function login(): void
    {
        $rawBody = file_get_contents("php://input");
        $data = json_decode($rawBody, true);

        $isJson = json_last_error() === JSON_ERROR_NONE && is_array($data);
        if ($isJson) {
            $email = $data["email"] ?? "";
            $password = $data["password"] ?? "";
            $remember = (bool)($data["remember"] ?? false);
        } else {
            $email = $_POST["email"] ?? "";
            $password = $_POST["password"] ?? "";
            $remember = isset($_POST["remember"]);
        }

        try {
            $user = $this->userService->authenticate($email, $password);

            if (!$user) {
                if ($isJson) {
                    $this->json([
                        "success" => false,
                        "message" => "Email hoặc mật khẩu không đúng!",
                    ], 401);
                } else {
                    \App\Core\View::render('auth.login', [
                        'title' => "Login",
                        'error' => "⚠️ Email hoặc mật khẩu không đúng! Vui lòng thử lại.",
                        'email' => $email,  
                    ], 'auth');
                }
                return;
            }
            Auth::login($user, $remember);
            
            if ($isJson) {
                $this->json([
                    "success" => true,
                    "message" => "Đăng nhập thành công!",
                    "data" => [
                        'id' => $user->id,
                        'email' => $user->email,
                        'full_name' => $user->full_name,
                        'role' => $user->role,
                    ],
                ]);
            } else {
                header('Location: /');
                exit;
            }
        } catch (RuntimeException $e) {
            if ($isJson) {
                $this->json([
                    "success" => false,
                    "message" => $e->getMessage(),
                ], 403);
            } else {
                \App\Core\View::render('auth.login', [
                    'title' => "Login",
                    "error" => "⚠️ " . $e->getMessage(),
                    'email' => $email,  
                ], 'auth');
            }
        }
    }

    public function logout(): void
    {
        Auth::logout();
        header('Location: /auth/login');
    }

    public function getAuthMe(): void
    {
        $user = Auth::user();
        if (!$user) {
            $this->json([
                "success" => false,
                "message" => "User null",
            ], 401);
            return;
        }
        $this->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'email' => $user->email,
                'full_name' => $user->full_name,
                'role' => $user->role,
            ],
        ]);
    }

    public function createUser(): void
    {
        $rawBody = file_get_contents('php://input');
        $data = json_decode($rawBody, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            $data = $_POST;
        }

        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';
        $fullName = $data['full_name'] ?? null;
        $role = $data['role'] ?? 'customer';

        if ($email === '' || $password === '') {
            $this->json([
                'success' => false,
                'message' => 'Email and password are required',
            ], 422);
            return;
        }

        try {
            $id = $this->userService->createUser([
                'email' => $email,
                'password' => $password,
                'full_name' => $fullName,
                'role' => $role,
                'is_active' => 1,
            ]);

            $user = $this->userService->getUserById($id);
            $this->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'full_name' => $user->full_name,
                    'role' => $user->role,
                ],
            ], 201);
        } catch (RuntimeException $e) {
            $this->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 409);
        }
    }
}
