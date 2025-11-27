<?php

namespace App\Core;

use App\Models\User;

class BaseController {

    protected function json(array $data, int $statusCode = 200): void{
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data,  JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    protected function view(string $view, array $data = []): void{
        extract($data);
        require __DIR__ . "/../Views/{$view}.php";
    }

    protected function redirect(string $url): void
    {
        header("Location {$url}");
        exit;
    }

    //get current user
    protected function currentUser(): ?User{
        return Auth::user();
    }

    //require user login
    protected function requireLogin(): User{
        $user = Auth::user();
        if (!$user) {
            $this->json([
                "success" => false,
                "message" => "Unauthenticated",
            ], 401);
        }
        return $user;
    }
    
    protected function requireRole(array $roles): User
    {
        $user = $this->requireLogin();
        if (!in_array($user->role, $roles, true)) {
            $this->json([
                "success" => false,
                "message" => "Forbidden",
            ], 403);
        }
        return $user;
    }

    //require admin
    protected function requireAdmin(): User{
        return $this->requireRole(["admin"]);
    }

    //require staff 
    protected function requireStaff(): User{
        return $this->requireRole(["staff"]);
    }

    //require customer 
    protected function requireCustomer(): User{
        return $this->requireCustomer(["customer"]);
    }
}
