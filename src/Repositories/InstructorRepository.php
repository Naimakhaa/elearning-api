<?php

namespace Repositories;

use Core\Database;
use Models\Instructor;
use PDO;

class InstructorRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function find(int $id): ?Instructor
    {
        return Instructor::find($id);
    }

    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM instructors");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => new Instructor($row), $rows);
    }

    public function findByEmail(string $email): ?Instructor
    {
        return Instructor::findByEmail($email);
    }

    public function save(Instructor $instructor): bool
    {
        return $instructor->save();
    }
}
