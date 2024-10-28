<?php
session_start();
include '../includes/db.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    $departmentId = $data['departmentId'];
    $departmentName = $data['departmentName'];

    $stmt = $conn->prepare("UPDATE Departments SET DepartmentName = ? WHERE DepartmentID = ?");
    $stmt->bind_param("ss", $departmentName, $departmentId);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
        $_SESSION['message'] = 'Department updated successfully.';
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error updating department.']);
        $_SESSION['message'] = 'Error updating department: ' . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input data.']);
    $_SESSION['message'] = 'Invalid input data.';
}
?>
