<?php
session_start(); // Start the session
include '../includes/db.php';

// Check if the user is logged in and has the 'admin' role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html"); // Redirect to login page
    exit();
}

// Fetch Overtime data from the database
$query = "SELECT o.OvertimeID, o.EmployeeID, o.NumberOfDays, o.StartDate, o.EndDate, e.FullName, b.BranchName, d.DepartmentName 
          FROM Overtime o
          LEFT JOIN Employees e  ON e.EmployeeID = o.EmployeeID
          LEFT JOIN Departments d ON e.DepartmentID = d.DepartmentID 
          LEFT JOIN Branch b ON e.BranchID = b.BranchID";
$result = $conn->query($query);

$message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Overtime</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script src="../js/overtime.js" defer></script>
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
        <li><a href="branches.php"><i class="fas fa-code-branch"></i> Branches</a></li>
        <li><a href="overtime.php" class="active"><i class="fas fa-clock"></i> Overtime</a></li>
        <li><a href="vacation.php"><i class="fas fa-plane"></i> Vacation</a></li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>


    <div class="content" style="margin-left: 270px; padding: 20px;">
    
        <h2 class="text-center">Overtime</h2>
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

        <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addOvertimeModal">Add Overtime</button>
        
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Overtime ID</th>
                    <th>Employee ID</th>
                    <th>Full Name</th>
                    <th>Department</th>
                    <th>Branch</th>
                    <th>Days</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['OvertimeID']; ?></td>
                    <td><?php echo $row['EmployeeID']; ?></td>
                    <td><?php echo $row['FullName']; ?></td>
                    <td><?php echo $row['DepartmentName']; ?></td>
                    <td><?php echo $row['BranchName']; ?></td>
                    <td><?php echo $row['NumberOfDays']; ?></td>
                    <td><?php echo $row['StartDate']; ?></td>
                    <td><?php echo $row['EndDate']; ?></td>
                    <td>
                    <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#updateOvertimeModal" 
                            onclick="fillUpdateModal(<?php echo $row['OvertimeID']; ?>, '<?php echo $row['EmployeeID']; ?>', '<?php echo $row['NumberOfDays']; ?>', '<?php echo $row['StartDate']; ?>', '<?php echo $row['EndDate']; ?>')">Update</button>
                    <a href="delete_overtime.php?id=<?php echo $row['OvertimeID']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this overtime?');">Delete</a>
                </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Add Overtime Modal -->
    <div class="modal fade" id="addOvertimeModal" tabindex="-1" role="dialog" aria-labelledby="addOvertimeModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addOvertimeModalLabel">Add New Overtime</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addOvertimeForm" method="POST" action="add_overtime.php">
                        <div class="form-group">
                            <label for="employeeId">Employee ID</label>
                            <input type="text" class="form-control" id="employeeId" name="employeeId" required>
                        </div>
                        <div class="form-group">
                            <label for="numberOfDays">Days</label>
                            <input type="number" class="form-control" id="numberOfDays" name="numberOfDays" required>
                        </div>
                        <div class="form-group">
                            <label for="startDate">Days</label>
                            <input type="date" class="form-control" id="startDate" name="startDate" required>
                        </div>
                        <div class="form-group">
                            <label for="endDate">Days</label>
                            <input type="date" class="form-control" id="endDate" name="endDate" required>
                        </div>
                        
                        
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" form="addOvertimeForm" class="btn btn-primary">Add Overtime </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Overtime Modal -->
<div class="modal fade" id="updateOvertimeModal" tabindex="-1" role="dialog" aria-labelledby="updateOvertimeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateOvertimeModalLabel">Update Overtime</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="updateOvertimeForm" method="POST" action="update_overtime.php">
                    <input type="hidden" id="updateEmployeeId" name="employeeId">
                    <div class="form-group">
                        <label for="updateEmployeeIdField">Employee ID</label>
                        <input type="text" class="form-control" id="updateEmployeeIdField" name="EmployeeIdField" readonly>
                    </div>
                    <div class="form-group">
                            <label for="updatenumberOfDays">Days</label>
                            <input type="number" class="form-control" id="updatenumberOfDays" name="numberOfDays" required>
                        </div>
                        <div class="form-group">
                            <label for="updatestartDate">Start Date</label>
                            <input type="date" class="form-control" id="updatestartDate" name="startDate" required>
                        </div>
                        <div class="form-group">
                            <label for="updateendDate">End Date</label>
                            <input type="date" class="form-control" id="updateendDate" name="endDate" required>
                        </div>
                       
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="document.getElementById('updateOvertimeForm').submit();">Update Overtime</button>
            </div>
        </div>
    </div>
</div>

<script>
function fillUpdateModal(id, employeeId, numberOfDays, startDate, endDate) {
    document.getElementById('updateEmployeeId').value = id;
    document.getElementById('updateEmployeeIdField').value = employeeId;
    document.getElementById('updatenumberOfDays').value = numberOfDays;
    document.getElementById('updatestartDate').value = startDate;
    document.getElementById('updateendDate').value = endDate;
}
</script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


</body>
</html>
