<?php

namespace Repositories;

use Core\Database;
use Models\Student;
use PDO;

class StudentRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function find(int $id): ?Student
    {
        return Student::find($id);
    }

    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM students");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => new Student($row), $rows);
    }

    public function findByEmail(string $email): ?Student
    {
        return Student::findByEmail($email);
    }

    public function findByStudentNumber(string $studentNumber): ?Student
    {
        return Student::findByStudentNumber($studentNumber);
    }

    public function save(Student $student): bool
    {
        return $student->save();
    }

    public function delete(Student $student): bool
    {
        return $student->delete();
    }

    /**
     * Hitung jumlah enrollment aktif milik student tertentu
     */
    public function countActiveEnrollments(int $studentId): int
    {
        $sql = "SELECT COUNT(*) AS total 
                FROM enrollments 
                WHERE student_id = :sid AND status = 'active'";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':sid' => $studentId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int)($row['total'] ?? 0);
    }
}
