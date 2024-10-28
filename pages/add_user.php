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
    $employeeId = $data['employeeId'];
    $username = $data['username']; // Corrected the extra space
    $password = $data['password'];
    $role = $data['role'];
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO Users (EmployeeID, Username, Password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $employeeId, $username, $hashedPassword, $role);

    // Execute the statement
    if ($stmt->execute()) {
        // Set session message
        $_SESSION['message'] = "User added successfully!";
        // Respond with success status in JSON
        echo json_encode(['status' => 'success']);
    } else {
        // Set session error message
        $_SESSION['error'] = "Error adding user: " . $stmt->error;
        // Respond with error status in JSON
        echo json_encode(['status' => 'error']);
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    // Respond with error if JSON data is not found
    echo json_encode(['status' => 'error', 'message' => 'Invalid input data.']);
}
?>
