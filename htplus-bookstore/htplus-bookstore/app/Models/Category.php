<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Category Entity
 * 
 * Represents a product category in the system.
 * This is a plain data object (entity) without any database logic.
 */
class Category
{
    public int $id;
    public string $name;
    public string $created_at;
}
