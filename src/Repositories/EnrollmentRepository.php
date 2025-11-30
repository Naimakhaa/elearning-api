<?php

namespace Repositories;

use Core\Database;
use Models\Enrollment;
use PDO;

class EnrollmentRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function find(int $id): ?Enrollment
    {
        return Enrollment::find($id);
    }

    public function findActiveByStudentAndCourse(int $studentId, int $courseId): ?Enrollment
    {
        $sql = "SELECT * FROM enrollments 
                WHERE student_id = :sid 
                  AND course_id = :cid 
                  AND status = 'active'
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':sid' => $studentId,
            ':cid' => $courseId,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? new Enrollment($row) : null;
    }

    public function save(Enrollment $enrollment): bool
    {
        return $enrollment->save();
    }

    public function complete(Enrollment $enrollment, ?float $grade = null): bool
    {
        return $enrollment->complete($grade);
    }

    public function cancel(Enrollment $enrollment): bool
    {
        return $enrollment->cancel();
    }

    public function countActiveByStudent(int $studentId): int
    {
        $sql = "SELECT COUNT(*) AS total
                FROM enrollments
                WHERE student_id = :sid AND status = 'active'";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':sid' => $studentId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int)($row['total'] ?? 0);
    }

    public function countActiveByCourse(int $courseId): int
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
