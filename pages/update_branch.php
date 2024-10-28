<?php
session_start();
include '../includes/db.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    $BranchId = $data['BranchId'];
    $BranchName = $data['BranchName'];

    $stmt = $conn->prepare("UPDATE Branch SET BranchName = ? WHERE BranchID = ?");
    $stmt->bind_param("ss", $BranchName, $BranchId);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
        $_SESSION['message'] = 'Branch updated successfully.';
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error updating branch.']);
        $_SESSION['message'] = 'Error updating branch: ' . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input data.']);
    $_SESSION['message'] = 'Invalid input data.';
}
?>
