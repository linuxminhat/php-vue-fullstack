<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\BaseModel;
use PDO;

class Category extends BaseModel
{
    public int $id;
    public string $name;
    public string $created_at;

    private static function mapRow(array $row): self
    {
        $category = new self();
        $category->id = (int) $row["id"];
        $category->name = $row["name"];
        $category->created_at = $row["created_at"];
        return $category;
    }

    public function listAllCategory(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM categories ORDER BY id ASC ");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($row) => self::mapRow($row), $rows);
    }

    public function findById(int $id): ?self
    {
        $stmt = $this->db->prepare('SELECT * FROM categories WHERE id=:id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? self::mapRow($row) : null;
    }

    public function findByName(String $name): ?self
    {
        $stmt = $this->db->prepare('SELECT * FROM categories WHERE name=:name LIMIT 1');
        $stmt->execute(['name' => $name]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? self::mapRow($row) : null;
    }

    //Create 
    public function create(string $name): int
    {
        $stmt = $this->db->prepare('INSERT INTO categories (name) VALUES (:name)');
        $stmt->execute(['name' => $name]);
        return (int) $this->db->lastInsertId($name);
    }

    //Update
    public function update(int $id, string $name): int
    {
        $stmt = $this->db->prepare('UPDATE categories SET name = :name WHERE id = :id');
        $stmt->execute([
            'id' => $id,
            'name' => $name,
        ]);
        return $stmt->rowCount();
    }

    //Delete 
    public function delete(int $id): int
    {
        $stmt = $this->db->prepare('DELETE FROM categories WHERE id=:id');
        $stmt->execute([
            'id' => $id
        ]);
        return $stmt->rowCount();
    }
}
