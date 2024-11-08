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
    $numberOfDays = $_POST['numberOfDays'];
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];
    $vacationType = $_POST['vacationType'];

    // Prepare the SQL statement
    $stmt = $conn->prepare("UPDATE Vacation SET NumberOfDays=?, StartDate=?, EndDate=?, VacationType=? WHERE VacationID=?");
    $stmt->bind_param("ssssi", $numberOfDays, $startDate, $endDate, $vacationType, $employeeId);

    // Execute the statement and check for success
    if ($stmt->execute()) {
        // Send a socket notification about the updated vacation
        sendSocketMessage("Vacation updated successfully");
    } else {
        sendSocketMessage("Error updating Vacation: " . $stmt->error);
    }

    // Close the statement and redirect back to the Vacation page
    $stmt->close();
    header("Location: vacation.php");
    exit();
}

// Function to send messages via socket
function sendSocketMessage($message) {
    $host = '127.0.0.1'; // Socket server address
    $port = 80; // Socket server port
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
    header("Location: vacation.php?message=" . urlencode($message));
    exit();
}
?>
