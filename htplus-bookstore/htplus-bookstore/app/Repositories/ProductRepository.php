<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\BaseRepository;
use App\Models\Product;
use PDO;

class ProductRepository extends BaseRepository
{

    private function mapRow(array $row): Product
    {
        $product = new Product();
        $product->id          = (int) $row["id"];
        $product->category_id = $row["category_id"] !== null ? (int)$row["category_id"] : null;
        $product->sku         = $row["sku"];
        $product->name        = $row["name"];
        $product->author      = $row["author"];
        $product->publisher   = $row["publisher"];
        $product->isbn        = $row["isbn"];
        $product->price       = (float)$row["price"];
        $product->stock       = (int)$row["stock"];
        $product->image       = $row["image"];
        $product->created_at  = $row["created_at"];
        $product->updated_at  = $row["updated_at"] !== null ? $row["updated_at"] : null;
        $product->description = $row["description"];
        return $product;
    }

    public function findAll(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM products ORDER BY id DESC");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($row) => $this->mapRow($row), $rows);
    }

    public function findById(int $id): ?Product
    {
        $stmt = $this->db->prepare('SELECT * FROM products WHERE id=:id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->mapRow($row) : null;
    }

    public function findByName(string $name): ?Product
    {
        $stmt = $this->db->prepare('SELECT * FROM products WHERE name=:name LIMIT 1');
        $stmt->execute(['name' => $name]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->mapRow($row) : null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO products
            (category_id, sku, name, author, publisher, isbn, price, description, stock, image)
            VALUES (:category_id, :sku, :name, :author, :publisher, :isbn, :price, :description, :stock, :image)'
        );
        $stmt->execute([
            'category_id' => $data['category_id'] ?? null,
            'sku'         => $data['sku'],
            'name'        => $data['name'],
            'author'      => $data['author'] ?? null,
            'publisher'   => $data['publisher'] ?? null,
            'isbn'        => $data['isbn'] ?? null,
            'price'       => $data['price'],
            'description' => $data['description'],
            'stock'       => $data['stock'],
            'image'       => $data['image'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): int
    {
        $stmt = $this->db->prepare(
            'UPDATE products SET
                category_id = :category_id,
                sku         = :sku,
                name        = :name,
                author      = :author,
                publisher   = :publisher,
                isbn        = :isbn,
                price       = :price,
                description = :description,
                stock       = :stock,
                image       = :image
             WHERE id = :id'
        );

        $stmt->execute([
            'id'          => $id,
            'category_id' => $data['category_id'] ?? null,
            'sku'         => $data['sku'],
            'name'        => $data['name'],
            'author'      => $data['author'] ?? null,
            'publisher'   => $data['publisher'] ?? null,
            'isbn'        => $data['isbn'] ?? null,
            'price'       => $data['price'],
            'description' => $data['description'],
            'stock'       => $data['stock'],
            'image'       => $data['image'] ?? null,
        ]);

        return $stmt->rowCount();
    }

    public function delete(int $id): int
    {
        $stmt = $this->db->prepare('DELETE FROM products WHERE id=:id');
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount();
    }

    public function countAll(): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) AS total from products');
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) ($row['total'] ?? 0);
    }

    public function getPaged(int $limit, int $offset): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM products ORDER BY id DESC LIMIT :limit OFFSET :offset"
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($row) => $this->mapRow($row), $rows);
    }

    public function countFiltered(array $filters): int
    {
        $q = trim($filters['q'] ?? '');
        $category_id = (int)($filters['category_id'] ?? 0);
        $sql = 'SELECT COUNT(*) AS total FROM products WHERE 1=1';
        
        if ($q !== '') {
            $sql .= ' AND (name LIKE :q OR author LIKE :q)';
        }

        if ($category_id > 0) {
            $sql .= ' AND category_id = :category_id';
        }

        $stmt = $this->db->prepare($sql);
        if ($q !== '') {
            $stmt->bindValue(':q', '%' . $q . '%');
        }
        if ($category_id > 0) {
            $stmt->bindValue(':category_id', $category_id, PDO::PARAM_INT);
        }
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['total'] ?? 0);
    }
    
    public function getFiltered(array $filters, string $sort, int $limit, int $offset): array
    {
        $sql    = 'SELECT * FROM products WHERE 1=1';
        $params = [];

        // Search text
        if (!empty($filters['q'] ?? '')) {
            $sql .= ' AND (name LIKE :q OR author LIKE :q)';
            $params['q'] = '%' . $filters['q'] . '%';
        }

        // Filter category
        if (!empty($filters['category_id'])) {
            $sql .= ' AND category_id = :category_id';
            $params['category_id'] = (int)$filters['category_id'];
        }

        // Sort
        switch ($sort) {
            case 'price_low_high':
                $orderBy = 'price ASC';
                break;
            case 'price_high_low':
                $orderBy = 'price DESC';
                break;
            case 'newest':
                $orderBy = 'created_at DESC';
                break;
            case 'title_za':
                $orderBy = 'name DESC';
                break;
            case 'title_az':
            default:
                $orderBy = 'name ASC';
                break;
        }

        $sql .= " ORDER BY $orderBy LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => $this->mapRow($row), $rows);
    }

    public function getRelatedByCategory(int $categoryId, int $excludeProductId, int $limit = 4): array
    {
        if ($categoryId <= 0) {
            return [];
        }

        $stmt = $this->db->prepare(
            'SELECT * FROM products 
             WHERE category_id = :category_id 
             AND id != :exclude_id 
             ORDER BY RAND() 
             LIMIT :limit'
        );
        
        $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(':exclude_id', $excludeProductId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return array_map(fn($row) => $this->mapRow($row), $rows);
    }
}

