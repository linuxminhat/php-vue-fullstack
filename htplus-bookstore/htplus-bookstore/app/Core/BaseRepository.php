<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
abstract class BaseRepository
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }
}

