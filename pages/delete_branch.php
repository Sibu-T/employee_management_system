<?php
session_start();
include '../includes/db.php';

header('Content-Type: application/json');

// Get the JSON input
$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    // Sanitize input
    $BranchId = filter_var($data['BranchId'], FILTER_SANITIZE_STRING);

    // Prepare and bind
    $stmt = $conn->prepare("DELETE FROM Branch WHERE BranchID = ?");
    $stmt->bind_param("s", $BranchId);

    // Execute the statement
    if ($stmt->execute()) {
        // Set session message
        $_SESSION['message'] = 'Branch deleted successfully';
        // Send success message via socket
        sendSocketMessage("Branch '$BranchId' deleted successfully");
        echo json_encode(['status' => 'success']);
    } else {
        // Set session error message
        $_SESSION['message'] = 'Error deleting branch: ' . $stmt->error;
        // Send error message via socket
        sendSocketMessage("Error deleting branch '$BranchId': " . $stmt->error);
        echo json_encode(['status' => 'error', 'message' => 'Error deleting Branch.']);
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    // Respond with error if JSON data is not found
    $_SESSION['message'] = 'Invalid input data.';
    echo json_encode(['status' => 'error', 'message' => 'Invalid input data.']);
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
