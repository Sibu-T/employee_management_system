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
    $vacationType = $_POST['vacationType'];

    // Prepare the SQL statement
    $stmt = $conn->prepare("UPDATE Vacation SET NumberOfDays=?, StartDate=?, EndDate=?, VacationType=? WHERE VacationID=?");
    $stmt->bind_param("ssssi", $numberOfDays, $startDate, $endDate, $vacationType, $employeeId);

    // Execute the statement and check for success
    if ($stmt->execute()) {
        $_SESSION['message'] = "Vacation updated successfully.";
    } else {
        $_SESSION['error'] = "Error updating Vacation: " . $conn->error;
    }

    // Close the statement and redirect back to the Vacation page
    $stmt->close();
    header("Location: vacation.php");
    exit();
}
?>
