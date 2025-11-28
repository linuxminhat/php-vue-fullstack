<?php

declare(strict_types=1);

namespace App\Models;

/**
 * User Entity
 * 
 * Represents a user in the system.
 * This is a plain data object (entity) without any database logic.
 */
class User
{
    public int $id;
    public string $email;
    public string $password;
    public ?string $full_name;
    public string $role;
    public bool $is_active;
    public string $created_at;
}
