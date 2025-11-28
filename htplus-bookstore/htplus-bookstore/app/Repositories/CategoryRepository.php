<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\BaseRepository;
use App\Models\Category;
use PDO;

/**
 * Category Repository
 * 
 * Handles all database operations for categories.
 */
class CategoryRepository extends BaseRepository
{
    /**
     * Map database row to Category entity
     */
    private function mapRow(array $row): Category
    {
        $category = new Category();
        $category->id = (int) $row["id"];
        $category->name = $row["name"];
        $category->created_at = $row["created_at"];
        return $category;
    }

    /**
     * Get all categories
     */
    public function findAll(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM categories ORDER BY id ASC");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($row) => $this->mapRow($row), $rows);
    }

    /**
     * Find category by ID
     */
    public function findById(int $id): ?Category
    {
        $stmt = $this->db->prepare('SELECT * FROM categories WHERE id=:id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->mapRow($row) : null;
    }

    /**
     * Find category by name
     */
    public function findByName(string $name): ?Category
    {
        $stmt = $this->db->prepare('SELECT * FROM categories WHERE name=:name LIMIT 1');
        $stmt->execute(['name' => $name]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->mapRow($row) : null;
    }

    /**
     * Create new category
     */
    public function create(string $name): int
    {
        $stmt = $this->db->prepare('INSERT INTO categories (name) VALUES (:name)');
        $stmt->execute(['name' => $name]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Update category
     */
    public function update(int $id, string $name): int
    {
        $stmt = $this->db->prepare('UPDATE categories SET name = :name WHERE id = :id');
        $stmt->execute([
            'id' => $id,
            'name' => $name,
        ]);
        return $stmt->rowCount();
    }

    /**
     * Delete category
     */
    public function delete(int $id): int
    {
        $stmt = $this->db->prepare('DELETE FROM categories WHERE id=:id');
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount();
    }
}

