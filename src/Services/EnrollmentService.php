<?php

namespace Services;

use Models\Enrollment;
use Repositories\CourseRepository;
use Repositories\EnrollmentRepository;
use Repositories\StudentRepository;

class EnrollmentService
{
    public function __construct(
        private StudentRepository $students,
        private CourseRepository $courses,
        private EnrollmentRepository $enrollments
    ) {}

    /**
     * Proses pendaftaran student ke course
     *
     * @throws \RuntimeException kalau ada aturan bisnis yang dilanggar
     */
    public function enroll(int $studentId, int $courseId): Enrollment
    {
        $student = $this->students->find($studentId);
        if (!$student) {
            throw new \RuntimeException('Student tidak ditemukan.');
        }

        $course = $this->courses->find($courseId);
        if (!$course) {
            throw new \RuntimeException('Course tidak ditemukan.');
        }

        // Cek duplikat enrollment aktif
        if ($this->enrollments->findActiveByStudentAndCourse($studentId, $courseId)) {
            throw new \RuntimeException('Student sudah terdaftar aktif pada course ini.');
        }

        // Cek limit enroll student
        $currentStudentEnrolls = $this->enrollments->countActiveByStudent($studentId);
        if (method_exists($student, 'isLimitReached') && $student->isLimitReached($currentStudentEnrolls)) {
            throw new \RuntimeException('Enroll limit mahasiswa telah tercapai.');
        }

        // Cek kapasitas course (jika ada max_students)
        $currentCourseEnrolls = $this->enrollments->countActiveByCourse($courseId);
        if (property_exists($course, 'max_students')) {
            $max = (int)$course->max_students;
            if ($max > 0 && $currentCourseEnrolls >= $max) {
                throw new \RuntimeException('Course sudah penuh.');
            }
        }

        $enrollment = new Enrollment([
            'student_id' => $studentId,
            'course_id'  => $courseId,
            'status'     => 'active',
        ]);

        $this->enrollments->save($enrollment);

        return $enrollment;
    }

    public function complete(int $enrollmentId, ?float $grade = null): bool
    {
        $enrollment = $this->enrollments->find($enrollmentId);
        if (!$enrollment) {
            throw new \RuntimeException('Enrollment tidak ditemukan.');
        }

        return $this->enrollments->complete($enrollment, $grade);
    }

    public function cancel(int $enrollmentId): bool
    {
        $enrollment = $this->enrollments->find($enrollmentId);
        if (!$enrollment) {
            throw new \RuntimeException('Enrollment tidak ditemukan.');
        }

        return $this->enrollments->cancel($enrollment);
    }
}
