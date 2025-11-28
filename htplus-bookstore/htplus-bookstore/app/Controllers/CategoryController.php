<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Services\CategoryService;
use App\Core\View;
use RuntimeException;

class CategoryController extends BaseController
{
    private CategoryService $categoryService;

    public function __construct()
    {
        $this->categoryService = new CategoryService();
    }

    public function listAllCategory(): void
    {
        $categories = $this->categoryService->getAllCategories();
        View::render('admin.category.index', [
            'categories' => $categories,
            'page' => $page ?? 1,
            'totalPages' => $totalPages ?? 1
        ], 'main');
    }

    public function createNewCategory(): void
    {
        $rawBody = file_get_contents("php://input");
        $data = json_decode($rawBody, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            $data = $_POST;
        }

        $name = trim($data['name'] ?? '');
        if ($name === '') {
            $this->json([
                "success" => false,
                "message" => "Category name is required",
            ], 422);
        }

        try {
            $id = $this->categoryService->createCategory($name);
            $category = $this->categoryService->getCategoryById($id);
            $this->json([
                "success" => true,
                "data" => [
                    "id" => $category->id,
                    "name" => $category->name,
                ],
            ]);
        } catch (RuntimeException $e) {
            $this->json([
                "success" => false,
                "message" => $e->getMessage(),
            ], 400);
        }
    }

    public function updateCategory(): void
    {
        $rawBody = file_get_contents("php://input");
        $data = json_decode($rawBody, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            $data = $_POST;
        }
        $id = (int)($data['id'] ?? 0);
        $name = trim($data["name"] ?? "");
        if ($id < 0 || $name === '') {
            $this->json([
                "success" => false,
                "message" => "Invalid id or name",
            ], 422);
        }

        try {
            $this->categoryService->updateCategory($id, $name);
            $category = $this->categoryService->getCategoryById($id);
            $this->json([
                "success" => "true",
                "message" => "Update Category Successfully",
                "data" => $category,
            ]);
        } catch (RuntimeException $e) {
            $this->json([
                "success" => "false",
                "message" => $e->getMessage(),
            ], 404);
        }
    }

    public function deleteCategory(): void
    {
        $rawBody = file_get_contents("php://input");
        $data = json_decode($rawBody, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            $data = $_POST;
        }
        $id = (int)($data['id'] ?? 0);
        if ($id <= 0) {
            $this->json([
                "success" => false,
                "message" => "Invalid ID",
            ], 422);
        }

        try {
            $this->categoryService->deleteCategory($id);
            $this->json([
                "success" => true,
                "message" => "Delete Category Successfully",
            ]);
        } catch (RuntimeException $e) {
            $this->json([
                "success" => false,
                "message" => $e->getMessage(),
            ], 404);
        }
    }
}
