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
    $numberOfDays = $_POST['numberOfDays'];
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];

    // Prepare the SQL statement (fixed syntax error)
    $stmt = $conn->prepare("UPDATE Overtime SET NumberOfDays=?, StartDate=?, EndDate=? WHERE OvertimeID=?");
    $stmt->bind_param("issi", $numberOfDays, $startDate, $endDate, $employeeId); // Ensure the data types match

    // Execute the statement and check for success
    if ($stmt->execute()) {
        $_SESSION['message'] = "Overtime updated successfully.";
    } else {
        $_SESSION['error'] = "Error updating Overtime: " . $stmt->error; // Use $stmt->error for better error context
    }

    // Close the statement and redirect back to the Vacation page
    $stmt->close();
    header("Location: overtime.php");
    exit();
}
?>
