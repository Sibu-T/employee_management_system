<?php
session_start();
include '../includes/db.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    $departmentId = $data['departmentId'];

    $stmt = $conn->prepare("DELETE FROM Departments WHERE DepartmentID = ?");
    $stmt->bind_param("s", $departmentId);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
        $_SESSION['message'] = 'Department deleted successfully.';
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error deleting department.']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input data.']);
}
?>
