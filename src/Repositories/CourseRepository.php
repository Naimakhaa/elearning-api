<?php

interface CourseRepository {
    public function findAll(): array;
    public function findById(int $id): ?array;
    public function save(array $data): array;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}

namespace Repositories;

use Core\Database;
use Models\Course;
use PDO;

class CourseRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function find(int $id): ?Course
    {
        $stmt = $this->db->prepare("SELECT * FROM courses WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new Course($row) : null;
    }

    public function findByCode(string $courseCode): ?Course
    {
        $stmt = $this->db->prepare("SELECT * FROM courses WHERE course_code = :code LIMIT 1");
        $stmt->execute([':code' => $courseCode]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new Course($row) : null;
    }

    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM courses");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => new Course($row), $rows);
    }

    public function save(Course $course): bool
    {
        // Mengandalkan method save() di model kalau ada
        if (method_exists($course, 'save')) {
            return $course->save();
        }

        // fallback sederhana: insert only
        $data = $course->toArray();

        $sql = "INSERT INTO courses 
                (course_code, title, description, category, max_students, status)
                VALUES (:course_code, :title, :description, :category, :max_students, :status)";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':course_code'  => $data['course_code'] ?? null,
            ':title'        => $data['title'] ?? null,
            ':description'  => $data['description'] ?? null,
            ':category'     => $data['category'] ?? null,
            ':max_students' => $data['max_students'] ?? 0,
            ':status'       => $data['status'] ?? 'draft',
        ]);
    }

    /**
     * Hitung jumlah enrollment aktif pada sebuah course
     */
    public function countActiveEnrollments(int $courseId): int
    {
        $sql = "SELECT COUNT(*) AS total 
                FROM enrollments 
                WHERE course_id = :cid AND status = 'active'";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':cid' => $courseId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int)($row['total'] ?? 0);
    }
}
>>>>>>> 9f6d09eebd4a37d0189543c193f67479261836bf
