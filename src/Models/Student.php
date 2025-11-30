<?php
namespace Models;

use Core\Database;
use PDO;
use PDOException;

class Student extends User
{
    public string $student_number;
    public int $enroll_limit;

    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->student_number = $data['student_number'] ?? '';
        $this->enroll_limit   = isset($data['enroll_limit']) ? (int)$data['enroll_limit'] : 5;
    }

    public function validate(): array
    {
        $errors = $this->validateBasic();

        if (empty($this->student_number)) {
            $errors[] = 'Student number wajib diisi.';
        }

        if (!is_int($this->enroll_limit) || $this->enroll_limit < 0) {
            $errors[] = 'Enroll limit harus berupa angka >= 0.';
        }

        return $errors;
    }

    // Save (insert or update)
    public function save(): bool
    {
        $db = Database::getConnection();

        if ($this->id === null) {
            // insert
            $sql = "INSERT INTO students (student_number, email, password, name, phone, enroll_limit) 
                    VALUES (:student_number, :email, :password, :name, :phone, :enroll_limit)";
            $stmt = $db->prepare($sql);
            $ok = $stmt->execute([
                ':student_number' => $this->student_number,
                ':email' => $this->email,
                ':password' => $this->password,
                ':name' => $this->name,
                ':phone' => $this->phone,
                ':enroll_limit' => $this->enroll_limit
            ]);
            if ($ok) $this->id = (int)$db->lastInsertId();
            return (bool)$ok;
        } else {
            // update
            $sql = "UPDATE students SET student_number=:student_number, email=:email, password=:password, name=:name, phone=:phone, enroll_limit=:enroll_limit WHERE id = :id";
            $stmt = $db->prepare($sql);
            return $stmt->execute([
                ':student_number' => $this->student_number,
                ':email' => $this->email,
                ':password' => $this->password,
                ':name' => $this->name,
                ':phone' => $this->phone,
                ':enroll_limit' => $this->enroll_limit,
                ':id' => $this->id
            ]);
        }
    }

    public static function find(int $id): ?Student
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM students WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? new Student($row) : null;
    }

    public static function findByStudentNumber(string $studentNumber): ?Student
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM students WHERE student_number = :sn LIMIT 1");
        $stmt->execute([':sn' => $studentNumber]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? new Student($row) : null;
    }

    public static function findByEmail(string $email): ?Student
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM students WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? new Student($row) : null;
    }

    public function delete(): bool
    {
        if ($this->id === null) return false;
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM students WHERE id = :id");
        return $stmt->execute([':id' => $this->id]);
    }

    // business logic helpers
    public function canEnroll(int $currentEnrollments): bool
    {
        return $currentEnrollments < $this->enroll_limit;
    }

    public function isLimitReached(int $currentEnrollments): bool
    {
        return $currentEnrollments >= $this->enroll_limit;
    }
}
