<?php

namespace Services;

use Models\Course;
use Repositories\CourseRepository;

class CourseService
{
    public function __construct(
        private CourseRepository $courses
    ) {}

    public function listAll(): array
    {
        return $this->courses->findAll();
    }

    public function getById(int $id): ?Course
    {
        return $this->courses->find($id);
    }

    public function getByCode(string $code): ?Course
    {
        return $this->courses->findByCode($code);
    }

    /**
     * @throws \RuntimeException jika data tidak valid
     */
    public function create(array $data): Course
    {
        $course = new Course($data);

        if (method_exists($course, 'validate')) {
            $isValid = $course->validate();
            // kalau validate() mengembalikan array error:
            if (is_array($isValid) && !empty($isValid)) {
                throw new \RuntimeException('Course data invalid: ' . implode(', ', $isValid));
            }
            // kalau mengembalikan bool:
            if (is_bool($isValid) && !$isValid) {
                throw new \RuntimeException('Course data invalid');
            }
        }

        $this->courses->save($course);

        return $course;
    }
}
