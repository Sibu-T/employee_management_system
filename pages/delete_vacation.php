<?php
session_start();
include '../includes/db.php';

header('Content-Type: application/json');

// Check if the id is provided via URL parameter
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Convert to integer for safety
    var_dump($id);  // Debugging: Check if id is set

    // Prepare the statement
    $stmt = $conn->prepare("DELETE FROM Vacation WHERE VacationID = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode(['status' => 'success']);
                sendSocketMessage("Vacation deleted successfully"); // Send notification
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No vacation found with that ID.']);
                sendSocketMessage("No vacation found with that ID."); // Send notification
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error deleting vacation: ' . $stmt->error]);
            sendSocketMessage("Error deleting vacation: " . $stmt->error); // Send notification
        }

        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to prepare the SQL statement.']);
        sendSocketMessage("Failed to prepare the SQL statement."); // Send notification
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input data.']);
    sendSocketMessage("Invalid input data."); // Send notification
}

$conn->close();

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

// Redirect to vacation.php
header('Location: vacation.php');
exit();
?>
