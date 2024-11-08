<?php
session_start(); // Start the session
include '../includes/db.php'; // Include database connection file

// Check if the user is logged in and has the 'admin' role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html"); // Redirect to login page
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $employeeId = $_POST['employeeId'];
    $username = $_POST['username'];

    // Prepare the SQL statement
    $stmt = $conn->prepare("UPDATE Users SET Username=? WHERE id=?");
    $stmt->bind_param("si", $username, $employeeId);

    // Execute the statement and check for success
    if ($stmt->execute()) {
        // Send success message via socket
        sendSocketMessage("User updated successfully");
    } else {
        // Send error message via socket
        sendSocketMessage("Error updating user: " . $stmt->error);
    }

    // Close the statement
    $stmt->close();
    // Redirect back to the employee page
    header("Location: users.php");
    exit();
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

    // Redirect with message
    header("Location: users.php?message=" . urlencode($message));
    exit();
}
?>
