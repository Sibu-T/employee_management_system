<?php
session_start();
include '../includes/db.php';

header('Content-Type: application/json');

// Check if the id is provided via URL parameter
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Convert to integer for safety
    var_dump($id);  // Debugging: Check if id is set

    // Prepare the statement
    $stmt = $conn->prepare("DELETE FROM Users WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode(['status' => 'success']);
                // Send success message via socket
                sendSocketMessage("User deleted successfully.");
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No user found with that ID.']);
                sendSocketMessage("No user found with that ID.");
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error deleting user: ' . $stmt->error]);
            // Send error message via socket
            sendSocketMessage("Error deleting user with ID '$id': " . $stmt->error);
        }

        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to prepare the SQL statement.']);
        $_SESSION['message'] = 'Failed to prepare the SQL statement.';
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input data.']);
    $_SESSION['message'] = 'Invalid input data.';
}

$conn->close();

// Function to send messages via socket
function sendSocketMessage($message) {
    $host = '';
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

// Redirect to Users.php
header('Location: users.php');
exit();
?>
