<?php
// src/Services/CourseService.php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\CourseRepository;
use App\Exceptions\ValidationException;
use App\Exceptions\NotFoundException;

class CourseService
{
    public function __construct(
        private CourseRepository $courseRepository
    ) {}

    public function getAllCourses(): array
    {
        return $this->courseRepository->findAll();
    }

    public function getCourseById(int $id): ?array
    {
        $course = $this->courseRepository->findById($id);
        
        if (!$course) {
            throw new NotFoundException('Course');
        }
        
        return $course;
    }

    public function createCourse(array $data): array
    {
        // Validasi input
        $this->validateCourseData($data);
        
        // Set default values
        $courseData = [
            'course_code' => $data['course_code'],
            'title' => $data['title'],
            'description' => $data['description'],
            'category' => $data['category'],
            'max_students' => $data['max_students'] ?? 0,
            'current_enrolled' => 0,
            'status' => 'draft'
        ];
        
        return $this->courseRepository->save($courseData);
    }

    public function updateCourse(int $id, array $data): array
    {
        // Cek apakah course exists
        $existingCourse = $this->courseRepository->findById($id);
        if (!$existingCourse) {
            throw new NotFoundException('Course');
        }
        
        // Validasi input
        $this->validateCourseData($data, false); // false untuk update (tidak semua field required)
        
        // Update data
        $updateData = [];
        if (isset($data['title'])) $updateData['title'] = $data['title'];
        if (isset($data['description'])) $updateData['description'] = $data['description'];
        if (isset($data['category'])) $updateData['category'] = $data['category'];
        if (isset($data['max_students'])) $updateData['max_students'] = (int)$data['max_students'];
        
        $this->courseRepository->update($id, $updateData);
        
        // Return updated course
        return $this->courseRepository->findById($id);
    }

    public function deleteCourse(int $id): void
    {
        // Cek apakah course exists
        $existingCourse = $this->courseRepository->findById($id);
        if (!$existingCourse) {
            throw new NotFoundException('Course');
        }
        
        $this->courseRepository->delete($id);
    }

    public function publishCourse(int $id): array
    {
        // Cek apakah course exists
        $existingCourse = $this->courseRepository->findById($id);
        if (!$existingCourse) {
            throw new NotFoundException('Course');
        }
        
        // Update status to published
        $this->courseRepository->update($id, ['status' => 'published']);
        
        // Return updated course
        return $this->courseRepository->findById($id);
    }

    private function validateCourseData(array $data, bool $isCreate = true): void
    {
        $errors = [];
        
        if ($isCreate) {
            // Validation for create
            if (empty($data['course_code'])) {
                $errors['course_code'] = 'Course code is required';
            }
            
            if (empty($data['title'])) {
                $errors['title'] = 'Title is required';
            }
            
            if (empty($data['description'])) {
                $errors['description'] = 'Description is required';
            }
            
            if (empty($data['category'])) {
                $errors['category'] = 'Category is required';
            }
        } else {
            // Validation for update (optional fields)
            if (isset($data['course_code']) && empty($data['course_code'])) {
                $errors['course_code'] = 'Course code cannot be empty';
            }
            
            if (isset($data['title']) && empty($data['title'])) {
                $errors['title'] = 'Title cannot be empty';
            }
            
            if (isset($data['description']) && empty($data['description'])) {
                $errors['description'] = 'Description cannot be empty';
            }
            
            if (isset($data['category']) && empty($data['category'])) {
                $errors['category'] = 'Category cannot be empty';
            }
        }
        
        // Validate max_students if provided
        if (isset($data['max_students']) && $data['max_students'] < 0) {
            $errors['max_students'] = 'Max students must be greater than or equal to 0';
        }
        
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }
}
?>