<?php
namespace Models;
//
//use Core\Database;
//use PDO;
//
//class Instructor extends User
//{
//    public string $instructor_code;
//    public string $expertise;
//
//    public function __construct(array $data = [])
//    {
//        parent::__construct($data);
//        $this->instructor_code = $data['instructor_code'] ?? '';
//        $this->expertise       = $data['expertise'] ?? '';
//    }
//
//    public function validate(): array
//    {
//        $errors = $this->validateBasic();
//
//        if (empty($this->instructor_code)) {
//            $errors[] = 'Instructor code wajib diisi.';
//        }
//
//        if (empty($this->expertise)) {
//            $errors[] = 'Expertise wajib diisi.';
//        }
//
//        return $errors;
//    }
//
//    public function save(): bool
//    {
//        $db = Database::getConnection();
//
//        if ($this->id === null) {
//            $sql = "INSERT INTO instructors (instructor_code, email, password, name, phone, expertise)
//                    VALUES (:instructor_code, :email, :password, :name, :phone, :expertise)";
//            $stmt = $db->prepare($sql);
//            $ok = $stmt->execute([
//                ':instructor_code' => $this->instructor_code,
//                ':email' => $this->email,
//                ':password' => $this->password,
//                ':name' => $this->name,
//                ':phone' => $this->phone,
//                ':expertise' => $this->expertise
//            ]);
//            if ($ok) $this->id = (int)$db->lastInsertId();
//            return (bool)$ok;
//        } else {
//            $sql = "UPDATE instructors SET instructor_code=:instructor_code, email=:email, password=:password, name=:name, phone=:phone, expertise=:expertise WHERE id=:id";
//            $stmt = $db->prepare($sql);
//            return $stmt->execute([
//                ':instructor_code' => $this->instructor_code,
//                ':email' => $this->email,
//                ':password' => $this->password,
//                ':name' => $this->name,
//                ':phone' => $this->phone,
//                ':expertise' => $this->expertise,
//                ':id' => $this->id
//            ]);
//        }
//    }
//
//    public static function find(int $id): ?Instructor
//    {
//        $db = Database::getConnection();
//        $stmt = $db->prepare("SELECT * FROM instructors WHERE id = :id LIMIT 1");
//        $stmt->execute([':id' => $id]);
//        $row = $stmt->fetch(PDO::FETCH_ASSOC);
//        return $row ? new Instructor($row) : null;
//    }
//
//    public static function findByEmail(string $email): ?Instructor
//    {
//        $db = Database::getConnection();
//        $stmt = $db->prepare("SELECT * FROM instructors WHERE email = :email LIMIT 1");
//        $stmt->execute([':email' => $email]);
//        $row = $stmt->fetch(PDO::FETCH_ASSOC);
//        return $row ? new Instructor($row) : null;
//    }
//}
//
use Core\Database;
use PDO;

class Instructor extends User
{
    protected string $instructor_code;
    protected string $expertise;

    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->instructor_code = $data['instructor_code'] ?? '';
        $this->expertise       = $data['expertise'] ?? '';
    }

    public function validate(): array
    {
        $errors = $this->validateBasic();

        if (empty($this->instructor_code)) {
            $errors[] = 'Instructor code wajib diisi.';
        }

        if (empty($this->expertise)) {
            $errors[] = 'Expertise wajib diisi.';
        }

        return $errors;
    }

    public function save(): bool
    {
        $db = Database::getConnection();

        if ($this->id === null) {
            $sql = "INSERT INTO instructors (instructor_code, email, password, name, phone, expertise)
                    VALUES (:instructor_code, :email, :password, :name, :phone, :expertise)";
        } else {
            $sql = "UPDATE instructors SET
                        instructor_code=:instructor_code,
                        email=:email,
                        password=:password,
                        name=:name,
                        phone=:phone,
                        expertise=:expertise
                    WHERE id = :id";
        }

        $stmt = $db->prepare($sql);
        $ok = $stmt->execute([
            ':instructor_code' => $this->instructor_code,
            ':email' => $this->email,
            ':password' => $this->password,
            ':name' => $this->name,
            ':phone' => $this->phone,
            ':expertise' => $this->expertise,
            ':id' => $this->id
        ]);

        if ($this->id === null && $ok) {
            $this->id = (int) $db->lastInsertId();
        }

        return (bool)$ok;
    }

    public static function find(int $id): ?Instructor
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM instructors WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? new Instructor($row) : null;
    }

    public static function findByEmail(string $email): ?Instructor
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM instructors WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? new Instructor($row) : null;
    }
}