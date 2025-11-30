<?php
// src/Services/CourseService.php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\CourseRepository;
use App\Exceptions\ValidationException;
use App\Exceptions\NotFoundException;
use App\Exceptions\DatabaseException;

class CourseService
{
    public function __construct(
        private CourseRepository $courseRepository
    ) {}

    public function getAllCourses(): array
    {
        try {
            return $this->courseRepository->findAll();
        } catch (\PDOException $e) {
            throw new DatabaseException('Failed to retrieve courses: ' . $e->getMessage());
        }
    }

    public function getCourseById(int $id): ?array
    {
        try {
            $course = $this->courseRepository->findById($id);
        } catch (\PDOException $e) {
            throw new DatabaseException('Failed to retrieve course: ' . $e->getMessage());
        }
        
        if (!$course) {
            throw new NotFoundException('Course', $id);
        }
        
        return $course;
    }

    public function createCourse(array $data): array
    {
        $this->validateCourseData($data);
        
        $courseData = [
            'course_code' => $data['course_code'],
            'title' => $data['title'],
            'description' => $data['description'],
            'category' => $data['category'],
            'max_students' => $data['max_students'] ?? 0,
            'current_enrolled' => 0,
            'status' => 'draft'
        ];
        
        try {
            return $this->courseRepository->save($courseData);
        } catch (\PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                throw new ValidationException([
                    'course_code' => 'Course code already exists'
                ]);
            }
            throw new DatabaseException('Failed to create course: ' . $e->getMessage());
        }
    }

    public function updateCourse(int $id, array $data): array
    {
        // Cek apakah course exists
        $existingCourse = $this->courseRepository->findById($id);
        if (!$existingCourse) {
            throw new NotFoundException('Course', $id);
        }
        
        $this->validateCourseData($data, false);
        
        $updateData = [];
        if (isset($data['title'])) $updateData['title'] = $data['title'];
        if (isset($data['description'])) $updateData['description'] = $data['description'];
        if (isset($data['category'])) $updateData['category'] = $data['category'];
        if (isset($data['max_students'])) $updateData['max_students'] = (int)$data['max_students'];
        
        try {
            $this->courseRepository->update($id, $updateData);
            return $this->courseRepository->findById($id);
        } catch (\PDOException $e) {
            throw new DatabaseException('Failed to update course: ' . $e->getMessage());
        }
    }

    public function deleteCourse(int $id): void
    {
        $existingCourse = $this->courseRepository->findById($id);
        if (!$existingCourse) {
            throw new NotFoundException('Course', $id);
        }
        
        try {
            $this->courseRepository->delete($id);
        } catch (\PDOException $e) {
            throw new DatabaseException('Failed to delete course: ' . $e->getMessage());
        }
    }

    public function publishCourse(int $id): array
    {
        $existingCourse = $this->courseRepository->findById($id);
        if (!$existingCourse) {
            throw new NotFoundException('Course', $id);
        }
        
        try {
            $this->courseRepository->update($id, ['status' => 'published']);
            return $this->courseRepository->findById($id);
        } catch (\PDOException $e) {
            throw new DatabaseException('Failed to publish course: ' . $e->getMessage());
        }
    }

    private function validateCourseData(array $data, bool $isCreate = true): void
    {
        $errors = [];
        
        if ($isCreate) {
            if (empty($data['course_code'])) {
                $errors['course_code'] = 'Course code is required';
            } elseif (strlen($data['course_code']) > 20) {
                $errors['course_code'] = 'Course code must not exceed 20 characters';
            }
            
            if (empty($data['title'])) {
                $errors['title'] = 'Title is required';
            } elseif (strlen($data['title']) > 255) {
                $errors['title'] = 'Title must not exceed 255 characters';
            }
            
            if (empty($data['description'])) {
                $errors['description'] = 'Description is required';
            }
            
            if (empty($data['category'])) {
                $errors['category'] = 'Category is required';
            } elseif (strlen($data['category']) > 100) {
                $errors['category'] = 'Category must not exceed 100 characters';
            }
        }
        
        if (isset($data['max_students']) && $data['max_students'] < 0) {
            $errors['max_students'] = 'Max students must be greater than or equal to 0';
        }
        
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }
}
?>