<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;

class AboutController extends BaseController {
    public function index(): void
    {
        \App\Core\View::render('about/index', [
            'title' => 'About Us - HTPLUS Book Store',
        ]);
    }
}
