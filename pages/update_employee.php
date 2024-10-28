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
    $fullName = $_POST['fullName'];
    $gender = $_POST['gender'];
    $phone = $_POST['phone'];
    $nationality = $_POST['nationality'];
    $jobTitle = $_POST['jobTitle'];
    $department = $_POST['department'];
    $branch = $_POST['branch'];

    // Prepare the SQL statement
    $stmt = $conn->prepare("UPDATE Employees SET FullName=?, Gender=?, Phone=?, Nationality=?, JobTitle=?, DepartmentID=?, BranchID=? WHERE id=?");
    $stmt->bind_param("sssssiii", $fullName, $gender, $phone, $nationality, $jobTitle, $department, $branch, $employeeId);

    // Execute the statement and check for success
    if ($stmt->execute()) {
        $_SESSION['message'] = "Employee updated successfully.";
    } else {
        $_SESSION['error'] = "Error updating employee: " . $conn->error;
    }

    // Close the statement and redirect back to the employee page
    $stmt->close();
    header("Location: employees.php");
    exit();
}
?>
