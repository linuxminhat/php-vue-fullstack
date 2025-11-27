<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Auth;
use App\Models\User;

class AuthController extends BaseController
{

    public function showLogin(): void
    {
        \App\Core\View::render('auth.login', [
            'title' => 'Login'
        ], 'auth');
    }

    public function showRegister(): void
    {
        \App\Core\View::render('auth.register', [
            'title' => 'Sign up'
        ], 'auth');
    }

    public function login():void{
        $rawBody = file_get_contents("php://input");
        $data = json_decode($rawBody, true);

        $isJson = json_last_error() === JSON_ERROR_NONE && is_array($data);
        if($isJson){
            $email = $data["email"] ?? "";
            $password = $data["password"] ?? "";
        }else {
            $email = $_POST["email"] ?? "";
            $password = $_POST["password"] ?? "";
        }
        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if(!$user || !password_verify($password, $user->password)){
            if($isJson){
                $this->json([
                    "success" => false,
                    "message" => "Invalid Credentials",
                ],401);
            }else {
                \App\Core\View::render('auth.login',[
                    'title' => "Login",
                    'error' => "Invalid Credentials",
                ], 'auth');
            }
            return;
        }

        if(!$user->is_active){
            if($isJson){
                $this->json([
                    "success"=>false,
                    "message"=>"Account is inactive",
                ],403);
            }else {
                \App\Core\View::render('auth.login', [
                    'title' => "Login",
                    "error" => "Account being locked",
                ],'auth');
            }
            return;
        }

        Auth::login($user);
        if($isJson){
            $this->json([
                "success"=>true,
                "message"=>"Login successfully",
                "data" => [
                'id'        => $user->id,
                'email'     => $user->email,
                'full_name' => $user->full_name,
                'role'      => $user->role,
                ],
            ]);
        }else {
            header('Location: /');
            exit;
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
                'id'        => $user->id,
                'email'     => $user->email,
                'full_name' => $user->full_name,
                'role'      => $user->role,
            ],
        ]);
    }
    
    public function createUser(): void{

        $rawBody = file_get_contents('php://input');
        $data = json_decode($rawBody, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            $data = $_POST;
        }

        $email     = trim($data['email'] ?? '');
        $password  = $data['password'] ?? '';
        $fullName  = $data['full_name'] ?? null;
        $role      = $data['role'] ?? 'customer';
        
        if ($email === '' || $password === '') {
            $this->json([
                'success' => false,
                'message' => 'Email and password are required',
            ], 422);
            return;
        }

        $userModel = new User();

        if ($userModel->findByEmail($email)) {
            $this->json([
                'success' => false,
                'message' => 'User email already exists',
            ], 409);
            return;
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $id = $userModel->create([
            'email'     => $email,
            'password'  => $hashedPassword,
            'full_name' => $fullName,
            'role'      => $role,
            'is_active' => 1,
        ]);

        $user = $userModel->findById($id);
        $this->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => [
                'id'        => $user->id,
                'email'     => $user->email,
                'full_name' => $user->full_name,
                'role'      => $user->role,
            ],
        ], 201);
    }
}
