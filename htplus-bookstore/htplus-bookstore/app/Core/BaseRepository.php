<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

/**
 * Base Repository Class
 * 
 * All repositories should extend this class to get database connection.
 * Repositories handle all database operations (CRUD).
 */
abstract class BaseRepository
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }
}

