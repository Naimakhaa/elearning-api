<?php
// src/Exceptions/BusinessException.php
declare(strict_types=1);

namespace App\Exceptions;

/**
 * Exception untuk pelanggaran business rules
 * Lebih general daripada EnrollmentException, bisa digunakan untuk berbagai business rules
 */
class BusinessException extends \Exception
{
    private string $errorCode;
    private array $context;
    private string $domain;

    public function __construct(
        string $message = "Business rule violation", 
        string $errorCode = "BUSINESS_RULE_VIOLATION",
        array $context = [],
        string $domain = "general"
    ) {
        $this->errorCode = $errorCode;
        $this->context = $context;
        $this->domain = $domain;
        
        parent::__construct($message, 422); // 422 Unprocessable Entity
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function toArray(): array
    {
        return [
            'error_code' => $this->errorCode,
            'message' => $this->getMessage(),
            'domain' => $this->domain,
            'context' => $this->context
        ];
    }

    // ==================== FACTORY METHODS ====================

    // Course-related business exceptions
    public static function courseNotPublished(int $courseId): self
    {
        return new self(
            "Course is not published", 
            "COURSE_NOT_PUBLISHED",
            ['course_id' => $courseId],
            'course'
        );
    }

    public static function courseFull(int $courseId, int $current, int $max): self
    {
        return new self(
            "Course is full ($current/$max students enrolled)", 
            "COURSE_FULL",
            [
                'course_id' => $courseId,
                'current_enrolled' => $current,
                'max_students' => $max
            ],
            'course'
        );
    }

    public static function courseInDraft(int $courseId): self
    {
        return new self(
            "Course is in draft status and cannot be enrolled", 
            "COURSE_IN_DRAFT",
            ['course_id' => $courseId],
            'course'
        );
    }

    public static function courseArchived(int $courseId): self
    {
        return new self(
            "Course is archived and cannot be modified", 
            "COURSE_ARCHIVED",
            ['course_id' => $courseId],
            'course'
        );
    }

    // Enrollment-related business exceptions
    public static function alreadyEnrolled(int $courseId, int $studentId): self
    {
        return new self(
            "Student is already enrolled in this course", 
            "ALREADY_ENROLLED",
            [
                'course_id' => $courseId,
                'student_id' => $studentId
            ],
            'enrollment'
        );
    }

    public static function enrollmentLimitReached(int $studentId, int $current, int $limit): self
    {
        return new self(
            "Student has reached enrollment limit ($current/$limit courses)", 
            "ENROLLMENT_LIMIT_REACHED",
            [
                'student_id' => $studentId,
                'current_enrollments' => $current,
                'enrollment_limit' => $limit
            ],
            'enrollment'
        );
    }

    public static function enrollmentNotActive(int $enrollmentId): self
    {
        return new self(
            "Enrollment is not active", 
            "ENROLLMENT_NOT_ACTIVE",
            ['enrollment_id' => $enrollmentId],
            'enrollment'
        );
    }

    public static function enrollmentAlreadyCompleted(int $enrollmentId): self
    {
        return new self(
            "Enrollment is already completed", 
            "ENROLLMENT_ALREADY_COMPLETED",
            ['enrollment_id' => $enrollmentId],
            'enrollment'
        );
    }

    // Student-related business exceptions
    public static function studentInactive(int $studentId): self
    {
        return new self(
            "Student account is inactive", 
            "STUDENT_INACTIVE",
            ['student_id' => $studentId],
            'student'
        );
    }

    // Instructor-related business exceptions
    public static function instructorNotOwner(int $instructorId, int $courseId): self
    {
        return new self(
            "Instructor is not the owner of this course", 
            "INSTRUCTOR_NOT_OWNER",
            [
                'instructor_id' => $instructorId,
                'course_id' => $courseId
            ],
            'instructor'
        );
    }

    public static function instructorCourseLimit(int $instructorId, int $current, int $limit): self
    {
        return new self(
            "Instructor has reached course creation limit ($current/$limit courses)", 
            "INSTRUCTOR_COURSE_LIMIT",
            [
                'instructor_id' => $instructorId,
                'current_courses' => $current,
                'course_limit' => $limit
            ],
            'instructor'
        );
    }

    // System-wide business exceptions
    public static function operationNotAllowed(string $operation, string $reason = ""): self
    {
        $message = "Operation '$operation' is not allowed";
        if ($reason) {
            $message .= ": $reason";
        }

        return new self(
            $message, 
            "OPERATION_NOT_ALLOWED",
            ['operation' => $operation, 'reason' => $reason],
            'system'
        );
    }

    public static function resourceInUse(string $resourceType, int $resourceId, string $usedBy): self
    {
        return new self(
            "$resourceType is currently in use and cannot be modified", 
            "RESOURCE_IN_USE",
            [
                'resource_type' => $resourceType,
                'resource_id' => $resourceId,
                'used_by' => $usedBy
            ],
            'system'
        );
    }
}
?>