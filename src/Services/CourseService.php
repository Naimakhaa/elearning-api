<?php

interface CourseService {
    public function getAllCourses(): array;
    public function getCourseById(int $id): ?array;
    public function createCourse(array $data): array;
    public function updateCourse(int $id, array $data): array;
    public function deleteCourse(int $id): void;
    public function publishCourse(int $id): array;
}