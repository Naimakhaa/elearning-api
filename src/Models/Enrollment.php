<?php
namespace Models;

use Core\Database;
use PDO;
use PDOException;

class Enrollment
{
    public ?int $id;
    public int $course_id;
    public int $student_id;
    public ?string $enrolled_at;
    public ?string $completed_at;
    public string $status; // active, completed, cancelled
    public ?float $grade;
    public ?string $created_at;
    public ?string $updated_at;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->course_id = isset($data['course_id']) ? (int)$data['course_id'] : 0;
        $this->student_id = isset($data['student_id']) ? (int)$data['student_id'] : 0;
        $this->enrolled_at = $data['enrolled_at'] ?? null;
        $this->completed_at = $data['completed_at'] ?? null;
        $this->status = $data['status'] ?? 'active';
        $this->grade = isset($data['grade']) ? (float)$data['grade'] : null;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
    }

    public function validate(): array
    {
        $errors = [];
        if ($this->course_id <= 0) $errors[] = 'course_id tidak valid.';
        if ($this->student_id <= 0) $errors[] = 'student_id tidak valid.';
        if (!in_array($this->status, ['active','completed','cancelled'])) $errors[] = 'status tidak valid.';
        if ($this->grade !== null && ($this->grade < 0 || $this->grade > 100)) $errors[] = 'grade harus antara 0 dan 100.';
        return $errors;
    }

    public function save(): bool
    {
        $db = Database::getConnection();

        // On insert, rely on DB triggers to update courses.current_enrolled (SQL already has triggers)
        if ($this->id === null) {
            $sql = "INSERT INTO enrollments (course_id, student_id, enrolled_at, completed_at, status, grade)
                    VALUES (:course_id, :student_id, :enrolled_at, :completed_at, :status, :grade)";
            $stmt = $db->prepare($sql);
            $ok = $stmt->execute([
                ':course_id' => $this->course_id,
                ':student_id' => $this->student_id,
                ':enrolled_at' => $this->enrolled_at ?? date('Y-m-d H:i:s'),
                ':completed_at' => $this->completed_at,
                ':status' => $this->status,
                ':grade' => $this->grade
            ]);
            if ($ok) $this->id = (int)$db->lastInsertId();
            return (bool)$ok;
        } else {
            $sql = "UPDATE enrollments SET course_id=:course_id, student_id=:student_id, enrolled_at=:enrolled_at, completed_at=:completed_at, status=:status, grade=:grade WHERE id=:id";
            $stmt = $db->prepare($sql);
            return $stmt->execute([
                ':course_id' => $this->course_id,
                ':student_id' => $this->student_id,
                ':enrolled_at' => $this->enrolled_at,
                ':completed_at' => $this->completed_at,
                ':status' => $this->status,
                ':grade' => $this->grade,
                ':id' => $this->id
            ]);
        }
    }

    public static function find(int $id): ?Enrollment
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM enrollments WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? new Enrollment($row) : null;
    }

    public function complete(float $grade = null): bool
    {
        $this->status = 'completed';
        $this->completed_at = date('Y-m-d H:i:s');
        if ($grade !== null) $this->grade = $grade;
        return $this->save();
    }

    public function cancel(): bool
    {
        $this->status = 'cancelled';
        return $this->save();
    }
}
