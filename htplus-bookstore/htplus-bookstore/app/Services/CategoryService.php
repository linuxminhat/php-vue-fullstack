<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\CategoryRepository;
use App\Models\Category;
use RuntimeException;
class CategoryService
{
    private CategoryRepository $repository;

    public function __construct()
    {
        $this->repository = new CategoryRepository();
    }
    public function getAllCategories(): array
    {
        return $this->repository->findAll();
    }
    public function getCategoryById(int $id): ?Category
    {
        return $this->repository->findById($id);
    }
    public function createCategory(string $name): int
    {
        $name = trim($name);
        
        if (empty($name)) {
            throw new RuntimeException("Category name is required");
        }

        // Check for duplicate
        $existing = $this->repository->findByName($name);
        if ($existing) {
            throw new RuntimeException("Category name already exists");
        }

        return $this->repository->create($name);
    }
    public function updateCategory(int $id, string $name): bool
    {
        $name = trim($name);
        
        if (empty($name)) {
            throw new RuntimeException("Category name is required");
        }

        $category = $this->repository->findById($id);
        if (!$category) {
            throw new RuntimeException("Category not found");
        }

        // Check for duplicate (excluding current category)
        $existing = $this->repository->findByName($name);
        if ($existing && $existing->id !== $id) {
            throw new RuntimeException("Category name already exists");
        }

        $rowsAffected = $this->repository->update($id, $name);
        return $rowsAffected > 0;
    }
    public function deleteCategory(int $id): bool
    {
        $category = $this->repository->findById($id);
        if (!$category) {
            throw new RuntimeException("Category not found");
        }

        $rowsAffected = $this->repository->delete($id);
        return $rowsAffected > 0;
    }
}

