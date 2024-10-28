<?php
session_start(); // Start the session
include '../includes/db.php';

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
        $_SESSION['message'] = "User updated successfully.";
    } else {
        $_SESSION['error'] = "Error updating user: " . $conn->error;
    }

    // Close the statement and redirect back to the employee page
    $stmt->close();
    header("Location: users.php");
    exit();
}
?>
