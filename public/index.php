<?php

// require __DIR__ . '/../src/Core/Database.php';

// use Core\Database;

// try {
//     $pdo = Database::getConnection();
//     echo "✓ Koneksi BERHASIL ke database elearning_db<br>";

//     $stmt = $pdo->query("SELECT COUNT(*) FROM courses");
//     echo "Jumlah data course: " . $stmt->fetchColumn();
// } 
// catch (Exception $e) {
//     echo "✗ GAGAL: " . $e->getMessage();
// }

// public/index.php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Core\Database;
use App\Builders\ApiResponseBuilder;
use App\Controllers\CourseController;
use App\Controllers\StudentController;
use App\Controllers\EnrollmentController;
use App\Services\CourseService;
use App\Services\StudentService;
use App\Services\EnrollmentService;
use App\Repositories\CourseRepository;
use App\Repositories\StudentRepository;
use App\Repositories\EnrollmentRepository;

try {
    // Database Connection
    $pdo = Database::getConnection();
    
    // Response Builder
    $responseBuilder = new ApiResponseBuilder();

    // Repositories (asumsinya sudah dibuat oleh anggota 3)
    $courseRepository = new CourseRepository($pdo);
    $studentRepository = new StudentRepository($pdo);
    $enrollmentRepository = new EnrollmentRepository($pdo);

    // Services (asumsinya sudah dibuat oleh anggota 3)
    $courseService = new CourseService($courseRepository);
    $studentService = new StudentService($studentRepository);
    $enrollmentService = new EnrollmentService($enrollmentRepository, $courseRepository, $studentRepository);

    // Controllers
    $courseController = new CourseController($courseService, $responseBuilder);
    $studentController = new StudentController($studentService, $responseBuilder);
    $enrollmentController = new EnrollmentController($enrollmentService, $responseBuilder);

    // Router Setup
    $router = new Router('/api');

    // ==================== COURSE ROUTES ====================
    $router->get('/courses', [$courseController, 'list']);
    $router->get('/courses/:id', [$courseController, 'get']);
    $router->post('/courses', [$courseController, 'create']);
    $router->put('/courses/:id', [$courseController, 'update']);
    $router->delete('/courses/:id', [$courseController, 'delete']);
    $router->put('/courses/:id/publish', [$courseController, 'publish']);

    // ==================== STUDENT ROUTES ====================
    $router->get('/students', [$studentController, 'list']);
    $router->get('/students/:id', [$studentController, 'get']);
    $router->post('/students', [$studentController, 'create']);

    // ==================== ENROLLMENT ROUTES ====================
    $router->post('/enrollments', [$enrollmentController, 'create']);
    $router->get('/students/:id/enrollments', [$enrollmentController, 'getStudentEnrollments']);
    $router->put('/enrollments/:id/complete', [$enrollmentController, 'complete']);
    $router->put('/enrollments/:id/cancel', [$enrollmentController, 'cancel']);

    // Dispatch Router
    $router->dispatch();

} catch (Exception $e) {
    // Global Error Handler
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'status_code' => 500,
        'message' => 'Internal Server Error',
        'data' => null,
        'errors' => [$e->getMessage()]
    ]);
}
?>