<?php

declare(strict_types=1);

namespace App\Models;
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
