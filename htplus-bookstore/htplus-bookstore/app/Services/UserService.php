<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\UserRepository;
use App\Models\User;
use RuntimeException;

class UserService
{
    private UserRepository $repository;

    public function __construct()
    {
        $this->repository = new UserRepository();
    }
    public function getAllUsers(): array
    {
        return $this->repository->findAll();
    }
    public function getUserById(int $id): ?User
    {
        return $this->repository->findById($id);
    }
    public function getUserByEmail(string $email): ?User
    {
        return $this->repository->findByEmail($email);
    }
    public function createUser(array $data): int
    {
        // Validate email
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException("Invalid email address");
        }

        // Check if email already exists
        $existing = $this->repository->findByEmail($data['email']);
        if ($existing) {
            throw new RuntimeException("Email already exists");
        }

        // Validate password
        if (empty($data['password']) || strlen($data['password']) < 6) {
            throw new RuntimeException("Password must be at least 6 characters");
        }

        // Hash password before storing
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        return $this->repository->create($data);
    }
    public function updateUser(int $id, array $data): bool
    {
        $user = $this->repository->findById($id);
        if (!$user) {
            throw new RuntimeException("User not found");
        }

        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException("Invalid email address");
        }
        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            $data['password'] = $user->password;
        }

        $rowsAffected = $this->repository->update($id, $data);
        return $rowsAffected > 0;
    }
    public function updateProfile(int $id, string $fullName): bool
    {
        if (empty(trim($fullName))) {
            throw new RuntimeException("Full name is required");
        }

        $rowsAffected = $this->repository->updateProfile($id, $fullName);
        return $rowsAffected > 0;
    }
    public function changePassword(int $id, string $currentPassword, string $newPassword): bool
    {
        $user = $this->repository->findById($id);
        if (!$user) {
            throw new RuntimeException("User not found");
        }

        if (!password_verify($currentPassword, $user->password)) {
            throw new RuntimeException("Current password is incorrect");
        }
        if (strlen($newPassword) < 6) {
            throw new RuntimeException("New password must be at least 6 characters");
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $rowsAffected = $this->repository->changePassword($id, $hashedPassword);
        return $rowsAffected > 0;
    }
    public function authenticate(string $email, string $password): ?User
    {
        $user = $this->repository->findByEmail($email);
        
        if (!$user) {
            return null;
        }

        if (!password_verify($password, $user->password)) {
            return null;
        }

        if (!$user->is_active) {
            throw new RuntimeException("Account is inactive");
        }

        return $user;
    }
    public function getPaginatedUsers(int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;
        $users = $this->repository->getPaginated($perPage, $offset);
        $total = $this->repository->countAll();
        $totalPages = (int)ceil($total / $perPage);

        return [
            'users' => $users,
            'pagination' => [
                'page' => $page,
                'total_pages' => $totalPages,
                'total_users' => $total,
            ]
        ];
    }
}

