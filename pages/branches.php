<?php
session_start(); // Start the session
include '../includes/db.php';

// Check if the user is logged in and has the 'admin' role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html"); // Redirect to login page
    exit();
}

// Fetch Branch data from the database
$query = "SELECT BranchID, BranchName 
          FROM Branch";
$result = $conn->query($query);

$message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Branches</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script src="../js/branches.js" defer></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">


</head>
<body>

<div class="sidebar">
    <ul class="list-unstyled">
        <li><a href="profile.php"><i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['username']); ?></a></li>
        <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="employees.php"><i class="fas fa-users"></i> Employees</a></li>
        <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
        <li><a href="departments.php"><i class="fas fa-building"></i> Departments</a></li>
        <li><a href="branches.php" class="active"><i class="fas fa-code-branch"></i> Branches</a></li>
        <li><a href="overtime.php"><i class="fas fa-clock"></i> Overtime</a></li>
        <li><a href="vacation.php"><i class="fas fa-plane"></i> Vacation</a></li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>


    <div class="content" style="margin-left: 270px; padding: 20px;">
    
        <h2 class="text-center">Branches</h2>
        <?php
// Check for session and socket messages and display them
if (isset($_SESSION['message'])) {
    echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['message']) . '</div>';
    unset($_SESSION['message']); // Clear the message after displaying
}

if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error']) . '</div>';
    unset($_SESSION['error']); // Clear the error after displaying
}
?>
<?php if (isset($message) && $message): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php unset($message); ?> <!-- Clear the message after displaying -->
<?php endif; ?>

        <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addBranchModal">Add Branch</button>
        
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Branch ID</th>
                    <th>Branch Name</th>
                    <th>Action</th>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['BranchID']; ?></td>
                    <td><?php echo $row['BranchName']; ?></td>
                    <td>
            <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#updateBranchModal" onclick="populateUpdateForm('<?php echo $row['BranchID']; ?>', '<?php echo $row['BranchName']; ?>')">Update</button>
            <button class="btn btn-danger btn-sm" onclick="deleteBranch('<?php echo $row['BranchID']; ?>')">Delete</button>
        </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Add Branch Modal -->
    <div class="modal fade" id="addBranchModal" tabindex="-1" role="dialog" aria-labelledby="addBranchModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBranchModalLabel">Add New Branch</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addBranchForm" method="POST" action="add_branch.php">
                        <div class="form-group">
                            <label for="BranchId">Branch ID</label>
                            <input type="text" class="form-control" id="BranchId" name="BranchId" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="BranchName">Branch Name</label>
                            <input type="text" class="form-control" id="BranchName" name="BranchName" required>
                        </div>
                        
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" form="addBranchForm" class="btn btn-primary">Add Branch</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Update Branch Modal -->
<div class="modal fade" id="updateBranchModal" tabindex="-1" role="dialog" aria-labelledby="updateBranchModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateBranchModalLabel">Update Branch</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="updateBranchForm">
                    <div class="form-group">
                        <label for="updateBranchId">Branch ID</label>
                        <input type="text" class="form-control" id="updateBranchId" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label for="updateBranchName">Branch Name</label>
                        <input type="text" class="form-control" id="updateBranchName" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="updateBranch()">Save changes</button>
            </div>
        </div>
    </div>
</div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


</body>
</html>
