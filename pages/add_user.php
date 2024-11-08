<?php
session_start(); // Start the session
include '../includes/db.php'; // Include database connection file

// Check if the user is logged in and has the 'admin' role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html"); // Redirect to login page
    exit();
}

// Set the content type to JSON
header('Content-Type: application/json');

// Get the JSON input
$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    // Retrieve form data from the JSON
    $employeeId = filter_var($data['employeeId'], FILTER_SANITIZE_STRING);
    $username = filter_var($data['username'], FILTER_SANITIZE_STRING); // Corrected the extra space
    $password = $data['password'];
    $role = filter_var($data['role'], FILTER_SANITIZE_STRING);
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO Users (EmployeeID, Username, Password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $employeeId, $username, $hashedPassword, $role);

    // Execute the statement
    if ($stmt->execute()) {
        // Set session message
        $_SESSION['message'] = "$username added successfully";
        // Send success message via socket
        sendSocketMessage("$username added successfully");
        // Respond with success status in JSON
        echo json_encode(['status' => 'success']);
    } else {
        // Set session error message
        $_SESSION['error'] = "Error adding user: " . $stmt->error;
        // Send error message via socket
        sendSocketMessage("Error adding user '$username': " . $stmt->error);
        // Respond with error status in JSON
        echo json_encode(['status' => 'error']);
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    // Respond with error if JSON data is not found
    $_SESSION['error'] = 'Invalid input data.';
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
