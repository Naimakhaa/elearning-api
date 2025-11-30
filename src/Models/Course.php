<?php
declare(strict_types=1);

namespace Models;

use Core\Database;
use Interfaces\Publishable;
use Interfaces\EnrollAble;
use Traits\Validatable;
use PDO;

/**
 * Course Model
 */
class Course implements Publishable, EnrollAble
{
    use Validatable;

    public ?int $id;
    public string $course_code;
    public string $title;
    public string $description;
    public string $category;
    public int $max_students;
    public int $current_enrolled;
    public string $status; // draft, published, archived
    public ?string $created_at;
    public ?string $updated_at;

    public function __construct(array $data = [])
    {
        $this->id               = isset($data['id']) ? (int)$data['id'] : null;
        $this->course_code      = $data['course_code']      ?? '';
        $this->title            = $data['title']            ?? '';
        $this->description      = $data['description']      ?? '';
        $this->category         = $data['category']         ?? '';
        $this->max_students     = isset($data['max_students']) ? (int)$data['max_students'] : 0;
        $this->current_enrolled = isset($data['current_enrolled']) ? (int)$data['current_enrolled'] : 0;
        $this->status           = $data['status']           ?? 'draft';
        $this->created_at       = $data['created_at']       ?? null;
        $this->updated_at       = $data['updated_at']       ?? null;
    }

    /**
     * Validasi, mengembalikan array error (kosong jika valid)
     */
    public function validate(): array
    {
        $this->clearErrors();

        $this->validateRequired('course_code', $this->course_code, 'Course code');
        $this->validateRequired('title', $this->title, 'Title');
        $this->validateRequired('description', $this->description, 'Description');
        $this->validateRequired('category', $this->category, 'Category');

        if ($this->max_students < 0) {
            $this->addError('max_students', 'max_students must be >= 0');
        }

        if ($this->current_enrolled < 0) {
            $this->addError('current_enrolled', 'current_enrolled must be >= 0');
        }

        if ($this->current_enrolled > $this->max_students && $this->max_students > 0) {
            $this->addError('current_enrolled', 'current_enrolled cannot exceed max_students');
        }

        return $this->getErrors();
    }

    // Publishable
    public function publish(): void
    {
        $this->status = 'published';
    }

    public function unpublish(): void
    {
        $this->status = 'draft';
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    // EnrollAble
    public function canEnroll(int $currentEnrolledCount): bool
    {
        if (!$this->isPublished()) {
            return false;
        }

        if ($this->max_students === 0) {
            // 0 → unlimited
            return true;
        }

        return $currentEnrolledCount < $this->max_students;
    }

    public function onEnroll(): void
    {
        $this->current_enrolled++;
    }

    public function onCancelEnrollment(): void
    {
        if ($this->current_enrolled > 0) {
            $this->current_enrolled--;
        }
    }

    // Persistence (mirip pola Student & Enrollment)
    public function save(): bool
    {
        $db = Database::getConnection();

        if ($this->id === null) {
            $sql = "INSERT INTO courses
                    (course_code, title, description, category, max_students, current_enrolled, status, created_at)
                    VALUES (:course_code, :title, :description, :category, :max_students, :current_enrolled, :status, :created_at)";
            $stmt = $db->prepare($sql);
            $ok = $stmt->execute([
                ':course_code'      => $this->course_code,
                ':title'            => $this->title,
                ':description'      => $this->description,
                ':category'         => $this->category,
                ':max_students'     => $this->max_students,
                ':current_enrolled' => $this->current_enrolled,
                ':status'           => $this->status,
                ':created_at'       => $this->created_at ?? date('Y-m-d H:i:s'),
            ]);
            if ($ok) {
                $this->id = (int)$db->lastInsertId();
            }
            return (bool)$ok;
        }

        $sql = "UPDATE courses
                SET title = :title,
                    description = :description,
                    category = :category,
                    max_students = :max_students,
                    current_enrolled = :current_enrolled,
                    status = :status,
                    updated_at = :updated_at
                WHERE id = :id";

        $stmt = $db->prepare($sql);

        return $stmt->execute([
            ':title'            => $this->title,
            ':description'      => $this->description,
            ':category'         => $this->category,
            ':max_students'     => $this->max_students,
            ':current_enrolled' => $this->current_enrolled,
            ':status'           => $this->status,
            ':updated_at'       => $this->updated_at ?? date('Y-m-d H:i:s'),
            ':id'               => $this->id,
        ]);
    }

    public function delete(): bool
    {
        if ($this->id === null) {
            return false;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare('DELETE FROM courses WHERE id = :id');
        return $stmt->execute([':id' => $this->id]);
    }

    public static function find(int $id): ?Course
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM courses WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new Course($row) : null;
    }

    public static function findByCode(string $courseCode): ?Course
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM courses WHERE course_code = :code LIMIT 1");
        $stmt->execute([':code' => $courseCode]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new Course($row) : null;
    }

    public static function all(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM courses");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => new Course($row), $rows);
    }

    public function toArray(): array
    {
        return [
            'id'               => $this->id,
            'course_code'      => $this->course_code,
            'title'            => $this->title,
            'description'      => $this->description,
            'category'         => $this->category,
            'max_students'     => $this->max_students,
            'current_enrolled' => $this->current_enrolled,
            'status'           => $this->status,
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,
        ];
    }
}
