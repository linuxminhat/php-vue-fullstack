<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\View;
use App\Models\Product;

class HomeController extends BaseController
{
    public function index(): void {

        $productModel = new Product();

        $perPage = 8;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if($page < 1) {
            $page = 1;
        }
        
        $totalProducts = $productModel->countAll();
        $totalPages = (int) ceil ($totalProducts/$perPage);

        if($totalPages<1){
            $totalPages = 1;
        }

        if($page>$totalPages){
            $page = $totalPages;
        }
        $offset = ($page-1) * $perPage;
        $products = $productModel->getPaged($perPage, $offset);

        View::render('home.index', [
            'title' => 'HTPLUS Book Store - Trang chủ',
            'products' => $products,
            'page' => $page,
            'total_pages' => $totalPages,
            'total_products' => $totalProducts,
        ], 'main');
    }
}
?>
 