<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\BaseRepository;
use App\Models\User;
use PDO;

class UserRepository extends BaseRepository
{

    private function mapRow(array $row): User
    {
        $user = new User();
        $user->id = (int) $row['id'];
        $user->email = $row['email'];
        $user->password = $row['password'];
        $user->full_name = $row['full_name'] ?? null;
        $user->role = $row['role'];
        $user->is_active = (bool) $row['is_active'];
        $user->created_at = $row['created_at'];
        return $user;
    }

    public function findAll(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM users ORDER BY id ASC");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($row) => $this->mapRow($row), $rows);
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email=:email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->mapRow($row) : null;
    }

    public function findById(int $id): ?User
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id=:id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->mapRow($row) : null;
    }
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users(email, password, full_name, role, is_active)
            VALUES (:email, :password, :full_name, :role, :is_active)'
        );
        
        $stmt->execute([
            'email' => $data['email'],
            'password' => $data['password'],
            'full_name' => $data['full_name'] ?? null,
            'role' => $data['role'] ?? 'customer',
            'is_active' => $data['is_active'] ?? 1,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): int
    {
        $stmt = $this->db->prepare(
            'UPDATE users 
           SET email=:email, 
           password=:password, 
           full_name=:full_name,
           role=:role,
           is_active=:is_active
           WHERE id=:id'
        );
        $stmt->execute([
            'id' => $id,
            'email' => $data['email'],
            'password' => $data['password'],
            'full_name' => $data['full_name'] ?? null,
            'role' => $data['role'] ?? 'customer',
            'is_active' => $data['is_active'] ?? 1,
        ]);
        return $stmt->rowCount();
    }

    public function updateProfile(int $id, string $full_name): int
    {
        $stmt = $this->db->prepare(
            'UPDATE users SET full_name = :full_name WHERE id = :id'
        );
        $stmt->execute([
            'id' => $id,
            'full_name' => $full_name,
        ]);
        return $stmt->rowCount();
    }

    public function changePassword(int $id, string $password): int
    {
        $stmt = $this->db->prepare(
            'UPDATE users SET password = :password WHERE id=:id'
        );
        $stmt->execute([
            'id' => $id,
            'password' => $password,
        ]);
        return $stmt->rowCount();
    }

    public function getPaginated(int $limit, int $offset): array
    {
        $sql = "SELECT * FROM users ORDER BY id ASC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function countAll(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM users");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $row['total'];
    }
}

