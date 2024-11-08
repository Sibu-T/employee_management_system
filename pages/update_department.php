<?php
session_start();
include '../includes/db.php';

header('Content-Type: application/json');

// Get the JSON input
$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    // Sanitize input
    $departmentId = filter_var($data['departmentId'], FILTER_SANITIZE_STRING);
    $departmentName = filter_var($data['departmentName'], FILTER_SANITIZE_STRING);

    // Prepare and bind
    $stmt = $conn->prepare("UPDATE Departments SET DepartmentName = ? WHERE DepartmentID = ?");
    $stmt->bind_param("ss", $departmentName, $departmentId);

    // Execute the statement
    if ($stmt->execute()) {
        // Set session message
        $_SESSION['message'] = 'Department updated successfully.';
        // Send success message via socket
        sendSocketMessage("Department '$departmentId' updated to '$departmentName'.");
        echo json_encode(['status' => 'success']);
    } else {
        // Set session error message
        $_SESSION['message'] = 'Error updating department: ' . $stmt->error;
        // Send error message via socket
        sendSocketMessage("Error updating department '$departmentId': " . $stmt->error);
        echo json_encode(['status' => 'error', 'message' => 'Error updating department.']);
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
