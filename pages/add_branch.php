<?php
session_start(); // Start the session
include '../includes/db.php'; // Include database connection file

// Check if the user is logged in and has the 'admin' role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit();
}

// Set the content type to JSON
header('Content-Type: application/json');

// Get the JSON input
$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    // Sanitize input
    $BranchId = filter_var($data['BranchId'], FILTER_SANITIZE_STRING);
    $BranchName = filter_var($data['BranchName'], FILTER_SANITIZE_STRING);

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO Branch (BranchID, BranchName) VALUES (?, ?)");
    $stmt->bind_param("ss", $BranchId, $BranchName);

    // Attempt to execute the statement
    if ($stmt->execute()) {
        $message = "$BranchName added successfully";
        $_SESSION['message'] = $message; // Set session message for success
        sendSocketMessage($message); // Send message via socket
        http_response_code(200);
        echo json_encode(['status' => 'success']);
    } else {
        $errorMessage = "Error adding Branch: " . $stmt->error;
        $_SESSION['error'] = $errorMessage; // Set session message for error
        sendSocketMessage($errorMessage); // Send error message via socket
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Database error.']);
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    http_response_code(400);
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
