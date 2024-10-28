<?php
session_start();
include '../includes/db.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    $BranchId = $data['BranchId'];

    $stmt = $conn->prepare("DELETE FROM Branch WHERE BranchID = ?");
    $stmt->bind_param("s", $BranchId);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
        $_SESSION['message'] = 'Branch deleted successfully.';
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error deleting Branch.']);
        $_SESSION['message'] = 'Error deleting branch: ' . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input data.']);
    $_SESSION['message'] = 'Invalid input data.';
}
?>
