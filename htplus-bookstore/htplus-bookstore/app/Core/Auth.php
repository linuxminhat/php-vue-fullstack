<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\User;

class Auth
{
    private const SESSION_KEY = 'user_id';

    public static function login(User $user): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
         
        $_SESSION[self::SESSION_KEY] = $user->id;
    }

    public static function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        unset($_SESSION[self::SESSION_KEY]);
    }

    public static function user(): ?User
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION[self::SESSION_KEY])) {
            return null;
        }

        $userModel = new User();
        return $userModel->findById((int) $_SESSION[self::SESSION_KEY]);
    }

    public static function isLoggedIn(): bool
    {
        return self::user() !== null;
    }

    //get id of login user 
    public static function id(): ?int
    {
        $user = self::user();
        return $user?->id;
    }
    public static function hasRole(array $roles): bool
    {
        $user = self::user();
        if (!$user) {
            return false;
        }
        return in_array($user->role, $roles, true);
    }

    //isAdmin
    public static function isAdmin(): bool
    {
        return self::hasRole(["admin"]);
    }

    //isStaff
    public static function isStaff(): bool
    {
        return self::hasRole(["staff"]);
    }
    
    //isCustomer
    public static function isCustomer(): bool
    {
        return self::hasRole(["customer"]);
    }
}
