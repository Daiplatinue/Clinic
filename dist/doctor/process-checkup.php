<?php
session_start();
require_once '../doctor/CheckupModal.php';

header('Content-Type: application/json');

try {
    // Check authentication
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Unauthorized access');
    }

    // Validate request
    if (!isset($_POST['checkup_id']) || !isset($_POST['health_status'])) {
        throw new Exception('Missing required fields');
    }

    // Sanitize and validate inputs
    $checkupId = filter_var($_POST['checkup_id'], FILTER_VALIDATE_INT);
    $healthStatus = trim(filter_var($_POST['health_status'], FILTER_SANITIZE_STRING));
    $allergies = trim(filter_var($_POST['allergies'] ?? '', FILTER_SANITIZE_STRING));

    if ($checkupId === false) {
        throw new Exception('Invalid checkup ID');
    }

    if (empty($healthStatus)) {
        throw new Exception('Health status cannot be empty');
    }

    // Update checkup
    $checkupModel = new CheckupModel();
    if ($checkupModel->updateCheckup($healthStatus, $allergies, $checkupId)) {
        echo json_encode([
            'success' => true,
            'message' => 'Checkup updated successfully'
        ]);
    } else {
        throw new Exception('Failed to update checkup');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>