<?php
// src/Controllers/CourseController.php
declare(strict_types=1);

namespace App\Controllers;

use App\Builders\ApiResponseBuilder;
use App\Services\CourseService;
use App\Exceptions\ValidationException;
use App\Exceptions\NotFoundException;

class CourseController
{
    public function __construct(
        private CourseService $courseService,
        private ApiResponseBuilder $response
    ) {}

    // GET /courses
    public function list(): void
    {
        try {
            $courses = $this->courseService->getAllCourses();
            $this->response->success($courses, 'Courses retrieved successfully');
        } catch (\Exception $e) {
            $this->response->error('Failed to retrieve courses', 500);
        }
    }

    // GET /courses/:id
    public function get(string $id): void
    {
        try {
            $courseId = (int)$id;
            $course = $this->courseService->getCourseById($courseId);
            
            if (!$course) {
                $this->response->notFound('Course');
                return;
            }
            
            $this->response->success($course, 'Course retrieved successfully');
        } catch (\Exception $e) {
            $this->response->error('Failed to retrieve course', 500);
        }
    }

    // POST /courses
    public function create(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true) ?? [];
            
            $course = $this->courseService->createCourse($input);
            $this->response->success($course, 'Course created successfully', 201);
            
        } catch (ValidationException $e) {
            $this->response->validationError($e->getErrors());
        } catch (\Exception $e) {
            $this->response->error('Failed to create course', 500);
        }
    }

    // PUT /courses/:id
    public function update(string $id): void
    {
        try {
            $courseId = (int)$id;
            $input = json_decode(file_get_contents('php://input'), true) ?? [];
            
            $course = $this->courseService->updateCourse($courseId, $input);
            $this->response->success($course, 'Course updated successfully');
            
        } catch (ValidationException $e) {
            $this->response->validationError($e->getErrors());
        } catch (NotFoundException $e) {
            $this->response->notFound('Course');
        } catch (\Exception $e) {
            $this->response->error('Failed to update course', 500);
        }
    }

    // DELETE /courses/:id
    public function delete(string $id): void
    {
        try {
            $courseId = (int)$id;
            $this->courseService->deleteCourse($courseId);
            $this->response->success(null, 'Course deleted successfully');
            
        } catch (NotFoundException $e) {
            $this->response->notFound('Course');
        } catch (\Exception $e) {
            $this->response->error('Failed to delete course', 500);
        }
    }

    // PUT /courses/:id/publish
    public function publish(string $id): void
    {
        try {
            $courseId = (int)$id;
            $course = $this->courseService->publishCourse($courseId);
            $this->response->success($course, 'Course published successfully');
            
        } catch (NotFoundException $e) {
            $this->response->notFound('Course');
        } catch (\Exception $e) {
            $this->response->error($e->getMessage(), 400);
        }
    }
}
?>