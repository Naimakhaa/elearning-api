<?php

namespace Repositories;

use Core\Database;
use Models\Course;
use PDO;

interface CourseRepositoryInterface
{
    public function find(int $id): ?Course;
    public function findByCode(string $courseCode): ?Course;

    /**
     * @return Course[]
     */
    public function findAll(): array;

    public function save(Course $course): bool;

    public function delete(Course $course): bool;

    public function countActiveEnrollments(int $courseId): int;
}

class CourseRepository implements CourseRepositoryInterface
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
