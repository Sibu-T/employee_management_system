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
                $_SESSION['message'] = 'Vacation deleted successfully.';
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No vacation found with that ID.']);
                $_SESSION['message'] = 'No vacation found with that ID.';
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error deleting vacation: ' . $stmt->error]);
            $_SESSION['message'] = 'Error deleting vacation: ' . $stmt->error;
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

// Redirect to vacation.php
header('Location: vacation.php');
exit();
?>
