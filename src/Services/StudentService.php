<?php

namespace Services;

use Models\Student;
use Repositories\StudentRepository;
use RuntimeException;

class StudentService
{
    public function __construct(
        private StudentRepository $students
    ) {}

    /**
     * Ambil semua student
     *
     * @return Student[]
     */
    public function listAll(): array
    {
        return $this->students->findAll();
    }

    /**
     * Ambil student berdasarkan id
     */
    public function getById(int $id): ?Student
    {
        return $this->students->find($id);
    }

    /**
     * Ambil student berdasarkan email
     */
    public function getByEmail(string $email): ?Student
    {
        return $this->students->findByEmail($email);
    }

    /**
     * Registrasi student baru
     *
     * @throws RuntimeException jika data tidak valid
     */
    public function register(array $data): Student
    {
        $student = new Student($data);

        // Validasi pakai Student::validate()
        $errors = $student->validate();
        if (!empty($errors)) {
            throw new RuntimeException('Data student tidak valid: ' . implode(', ', $errors));
        }

        // Kalau kamu mau password di-hash di sini, bisa:
        // if (!empty($data['password'])) {
        //     $student->setPassword($data['password']); // method dari User
        // }

        $this->students->save($student);

        return $student;
    }

    /**
     * Update data student yang sudah ada
     *
     * @throws RuntimeException jika student tidak ditemukan atau data invalid
     */
    public function update(int $id, array $data): Student
    {
        $student = $this->students->find($id);
        if (!$student) {
            throw new RuntimeException('Student tidak ditemukan.');
        }

        // Update field dari data yang baru
        // (silakan sesuaikan jika kamu punya setter khusus)
        if (isset($data['email'])) {
            $student->email = $data['email'];
        }
        if (isset($data['name'])) {
            $student->name = $data['name'];
        }
        if (isset($data['phone'])) {
            $student->phone = $data['phone'];
        }
        if (isset($data['student_number'])) {
            $student->student_number = $data['student_number'];
        }
        if (isset($data['enroll_limit'])) {
            $student->enroll_limit = (int)$data['enroll_limit'];
        }
        if (isset($data['password']) && $data['password'] !== '') {
            // kalau mau di-hash:
            // $student->setPassword($data['password']);
            $student->password = $data['password'];
        }

        $errors = $student->validate();
        if (!empty($errors)) {
            throw new RuntimeException('Data student tidak valid: ' . implode(', ', $errors));
        }

        $this->students->save($student);

        return $student;
    }

    /**
     * Hapus student
     *
     * @throws RuntimeException jika student tidak ditemukan
     */
    public function delete(int $id): bool
    {
        $student = $this->students->find($id);
        if (!$student) {
            throw new RuntimeException('Student tidak ditemukan.');
        }

        return $this->students->delete($student);
    }
}
