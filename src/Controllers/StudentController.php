<?php
// src/Controllers/StudentController.php
declare(strict_types=1);

namespace App\Controllers;

use App\Builders\ApiResponseBuilder;
use App\Services\StudentService;
use App\Exceptions\ValidationException;
use App\Exceptions\NotFoundException;

class StudentController
{
    public function __construct(
        private StudentService $studentService,
        private ApiResponseBuilder $response
    ) {}

    // GET /students
    public function list(): void
    {
        try {
            $students = $this->studentService->getAllStudents();
            $this->response->success($students, 'Students retrieved successfully');
        } catch (\Exception $e) {
            $this->response->error('Failed to retrieve students', 500);
        }
    }

    // GET /students/:id
    public function get(string $id): void
    {
        try {
            $studentId = (int)$id;
            $student = $this->studentService->getStudentById($studentId);
            
            if (!$student) {
                $this->response->notFound('Student');
                return;
            }
            
            $this->response->success($student, 'Student retrieved successfully');
        } catch (\Exception $e) {
            $this->response->error('Failed to retrieve student', 500);
        }
    }

    // POST /students
    public function create(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true) ?? [];
            
            $student = $this->studentService->createStudent($input);
            $this->response->success($student, 'Student created successfully', 201);
            
        } catch (ValidationException $e) {
            $this->response->validationError($e->getErrors());
        } catch (\Exception $e) {
            $this->response->error('Failed to create student', 500);
        }
    }
}
?>