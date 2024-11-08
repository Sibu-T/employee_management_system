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
    $fullName = $data['fullName'];
    $gender = $data['gender'];
    $phone = $data['phone'];
    $nationality = $data['nationality'];
    $jobTitle = $data['jobTitle'];
    $departmentId = $data['department'];
    $branchId = $data['branch'];

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO Employees (EmployeeID, FullName, Gender, Phone, Nationality, JobTitle, DepartmentID, BranchID) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssii", $employeeId, $fullName, $gender, $phone, $nationality, $jobTitle, $departmentId, $branchId);

    // Execute the statement
    if ($stmt->execute()) {
        // Set session message
        $_SESSION['message'] = "$fullName added successfully";
        // Send success message via socket
        sendSocketMessage("$fullName added successfully");
        // Respond with success status in JSON
        echo json_encode(['status' => 'success']);
    } else {
        // Set session error message
        $_SESSION['error'] = "Error adding employee: " . $stmt->error;
        // Send error message via socket
        sendSocketMessage("Error adding employee '$fullName': " . $stmt->error);
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
}
?>
