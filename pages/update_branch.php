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
        $_SESSION['message'] = 'Branch updated successfully';
        sendSocketMessage("Branch updated successfully"); // Notify via socket
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error updating branch.']);
        $_SESSION['message'] = 'Error updating branch: ' . $stmt->error;
        sendSocketMessage("Error updating branch: " . $stmt->error); // Notify via socket
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input data.']);
    $_SESSION['message'] = 'Invalid input data.';
    sendSocketMessage("Invalid input data."); // Notify via socket
    
}

// Function to send messages via socket
function sendSocketMessage($message) {
    $host = '127.0.0.1';
    $port = 80;
    $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

    if ($socket === false) {
        error_log("Socket creation failed: " . socket_strerror(socket_last_error()));
        return false;
    }

    if (!socket_connect($socket, $host, $port)) {
        error_log("Socket connection failed: " . socket_strerror(socket_last_error()));
        socket_close($socket);
        return false;
    }

    $notification = htmlspecialchars($message, ENT_QUOTES);
    socket_write($socket, $notification, strlen($notification));
    socket_close($socket);
}
?>
