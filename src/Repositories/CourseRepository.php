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
        return Course::find($id);
    }

    public function findByCode(string $courseCode): ?Course
    {
        return Course::findByCode($courseCode);
    }

    /**
     * @return Course[]
     */
    public function findAll(): array
    {
        return Course::all();
    }

    public function save(Course $course): bool
    {
        return $course->save();
    }

    public function delete(Course $course): bool
    {
        return $course->delete();
    }

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
