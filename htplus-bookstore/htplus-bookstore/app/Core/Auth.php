<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\User;
use App\Repositories\UserRepository;

class Auth
{
    private const SESSION_KEY = 'user_id';
    private const COOKIE_NAME = 'remember_user';
    private const COOKIE_LIFETIME = 30 * 24 * 60 * 60; // 30 days

    public static function login(User $user, bool $remember = false): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
         
        $_SESSION[self::SESSION_KEY] = $user->id;

        // Remember Me: Set cookie if requested
        if ($remember) {
            $token = self::generateRememberToken($user->id);
            setcookie(
                self::COOKIE_NAME,
                $token,
                time() + self::COOKIE_LIFETIME,
                '/',
                '',
                false, // Set to true if using HTTPS
                true   // HttpOnly flag for security
            );
        }
    }

    public static function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        unset($_SESSION[self::SESSION_KEY]);

        // Remove remember cookie
        if (isset($_COOKIE[self::COOKIE_NAME])) {
            setcookie(self::COOKIE_NAME, '', time() - 3600, '/');
            unset($_COOKIE[self::COOKIE_NAME]);
        }
    }

    public static function user(): ?User
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check session first
        if (!empty($_SESSION[self::SESSION_KEY])) {
            $userRepository = new UserRepository();
            return $userRepository->findById((int) $_SESSION[self::SESSION_KEY]);
        }

        // If no session, check remember cookie
        if (isset($_COOKIE[self::COOKIE_NAME])) {
            $userId = self::validateRememberToken($_COOKIE[self::COOKIE_NAME]);
            if ($userId) {
                $userRepository = new UserRepository();
                $user = $userRepository->findById($userId);
                if ($user) {
                    // Restore session
                    $_SESSION[self::SESSION_KEY] = $user->id;
                    return $user;
                }
            }
            // Invalid cookie, remove it
            setcookie(self::COOKIE_NAME, '', time() - 3600, '/');
        }

        return null;
    }

    /**
     * Generate remember token
     */
    private static function generateRememberToken(int $userId): string
    {
        // Simple token: user_id|timestamp|hash
        $timestamp = time();
        $hash = hash_hmac('sha256', $userId . '|' . $timestamp, self::getSecretKey());
        return base64_encode($userId . '|' . $timestamp . '|' . $hash);
    }

    /**
     * Validate remember token and return user ID if valid
     */
    private static function validateRememberToken(string $token): ?int
    {
        $decoded = base64_decode($token);
        $parts = explode('|', $decoded);

        if (count($parts) !== 3) {
            return null;
        }

        [$userId, $timestamp, $hash] = $parts;

        // Check if token expired
        if (time() - (int)$timestamp > self::COOKIE_LIFETIME) {
            return null;
        }

        // Verify hash
        $expectedHash = hash_hmac('sha256', $userId . '|' . $timestamp, self::getSecretKey());
        if (!hash_equals($expectedHash, $hash)) {
            return null;
        }

        return (int)$userId;
    }

    /**
     * Get secret key for token generation
     */
    private static function getSecretKey(): string
    {
        // You should store this in .env file
        return 'htplus-secret-key-change-this-in-production';
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
