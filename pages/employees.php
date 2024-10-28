<?php
session_start(); // Start the session
include '../includes/db.php';

// Check if the user is logged in and has the 'admin' role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html"); // Redirect to login page
    exit();
}

// Fetch employee data from the database
$query = "SELECT e.id, e.EmployeeID, e.FullName, e.Gender, e.Phone, e.Nationality, e.JobTitle, d.DepartmentName, b.BranchName 
          FROM Employees e 
          LEFT JOIN Departments d ON e.DepartmentID = d.DepartmentID 
          LEFT JOIN Branch b ON e.BranchID = b.BranchID";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employees</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script src="../js/employees.js" defer></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">


</head>
<body>

<div class="sidebar">
    <ul class="list-unstyled">
        <li><a href="profile.php"><i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['username']); ?></a></li>
        <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="employees.php" class="active"><i class="fas fa-users"></i> Employees</a></li>
        <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
        <li><a href="departments.php"><i class="fas fa-building"></i> Departments</a></li>
        <li><a href="branches.php"><i class="fas fa-code-branch"></i> Branches</a></li>
        <li><a href="overtime.php"><i class="fas fa-clock"></i> Overtime</a></li>
        <li><a href="vacation.php"><i class="fas fa-plane"></i> Vacation</a></li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>


    <div class="content" style="margin-left: 270px; padding: 20px;">
    
        <h2 class="text-center">Employees</h2>
        <?php
if (isset($_SESSION['message'])) {
    echo '<div class="alert alert-success">' . $_SESSION['message'] . '</div>';
    unset($_SESSION['message']); // Clear the message after displaying
}

if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']); // Clear the error after displaying
}
?>

<button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addEmployeeModal">Add Employee</button>
        
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Employee ID</th>
                    <th>Full Name</th>
                    <th>Gender</th>
                    <th>Phone</th>
                    <th>Nationality</th>
                    <th>Job Title</th>
                    <th>Department</th>
                    <th>Branch</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['EmployeeID']; ?></td>
                    <td><?php echo $row['FullName']; ?></td>
                    <td><?php echo $row['Gender']; ?></td>
                    <td><?php echo $row['Phone']; ?></td>
                    <td><?php echo $row['Nationality']; ?></td>
                    <td><?php echo $row['JobTitle']; ?></td>
                    <td><?php echo $row['DepartmentName']; ?></td>
                    <td><?php echo $row['BranchName']; ?></td>
                    <td>
                    <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#updateEmployeeModal" 
                            onclick="fillUpdateModal(<?php echo $row['id']; ?>, '<?php echo $row['EmployeeID']; ?>', '<?php echo $row['FullName']; ?>', '<?php echo $row['Gender']; ?>', '<?php echo $row['Phone']; ?>', '<?php echo $row['Nationality']; ?>', '<?php echo $row['JobTitle']; ?>', '<?php echo $row['DepartmentName']; ?>', '<?php echo $row['BranchName']; ?>')">Update</button>
                    <a href="delete_employee.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this employee?');">Delete</a>
                </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Add Employee Modal -->
    <div class="modal fade" id="addEmployeeModal" tabindex="-1" role="dialog" aria-labelledby="addEmployeeModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addEmployeeModalLabel">Add New Employee</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addEmployeeForm" method="POST" action="add_employee.php">
                        <div class="form-group">
                            <label for="employeeId">Employee ID</label>
                            <input type="text" class="form-control" id="employeeId" name="employeeId" required>
                        </div>
                        <div class="form-group">
                            <label for="fullName">Full Name</label>
                            <input type="text" class="form-control" id="fullName" name="fullName" required>
                        </div>
                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <select class="form-control" id="gender" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone" required>
                        </div>
                        <div class="form-group">
                            <label for="nationality">Nationality</label>
                            <input type="text" class="form-control" id="nationality" name="nationality" required>
                        </div>
                        <div class="form-group">
                            <label for="jobTitle">Job Title</label>
                            <input type="text" class="form-control" id="jobTitle" name="jobTitle" required>
                        </div>
                        <div class="form-group">
                            <label for="department">Department</label>
                            <select class="form-control" id="department" name="department" required>
                                <?php
                                // Fetch departments for the dropdown
                                $departments = $conn->query("SELECT DepartmentID, DepartmentName FROM Departments");
                                while ($dept = $departments->fetch_assoc()):
                                ?>
                                <option value="<?php echo $dept['DepartmentID']; ?>"><?php echo $dept['DepartmentName']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="branch">Branch</label>
                            <select class="form-control" id="branch" name="branch" required>
                                <?php
                                // Fetch branches for the dropdown
                                $branches = $conn->query("SELECT BranchID, BranchName FROM Branch");
                                while ($branch = $branches->fetch_assoc()):
                                ?>
                                <option value="<?php echo $branch['BranchID']; ?>"><?php echo $branch['BranchName']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" form="addEmployeeForm" class="btn btn-primary">Add Employee</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Employee Modal -->
<div class="modal fade" id="updateEmployeeModal" tabindex="-1" role="dialog" aria-labelledby="updateEmployeeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateEmployeeModalLabel">Update Employee</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="updateEmployeeForm" method="POST" action="update_employee.php">
                    <input type="hidden" id="updateEmployeeId" name="employeeId">
                    <div class="form-group">
                        <label for="updateEmployeeIdField">Employee ID</label>
                        <input type="text" class="form-control" id="updateEmployeeIdField" name="employeeIdField" readonly>
                    </div>
                    <div class="form-group">
                        <label for="updateFullName">Full Name</label>
                        <input type="text" class="form-control" id="updateFullName" name="fullName" required>
                    </div>
                    <div class="form-group">
                        <label for="updateGender">Gender</label>
                        <select class="form-control" id="updateGender" name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="updatePhone">Phone</label>
                        <input type="text" class="form-control" id="updatePhone" name="phone" required>
                    </div>
                    <div class="form-group">
                        <label for="updateNationality">Nationality</label>
                        <input type="text" class="form-control" id="updateNationality" name="nationality" required>
                    </div>
                    <div class="form-group">
                        <label for="updateJobTitle">Job Title</label>
                        <input type="text" class="form-control" id="updateJobTitle" name="jobTitle" required>
                    </div>
                    <div class="form-group">
                        <label for="updateDepartment">Department</label>
                        <select class="form-control" id="updateDepartment" name="department" required>
                            <?php
                            // Fetch departments for the dropdown
                            $departments = $conn->query("SELECT DepartmentID, DepartmentName FROM Departments");
                            while ($dept = $departments->fetch_assoc()):
                            ?>
                            <option value="<?php echo $dept['DepartmentID']; ?>"><?php echo $dept['DepartmentName']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="updateBranch">Branch</label>
                        <select class="form-control" id="updateBranch" name="branch" required>
                            <?php
                            // Fetch branches for the dropdown
                            $branches = $conn->query("SELECT BranchID, BranchName FROM Branch");
                            while ($branch = $branches->fetch_assoc()):
                            ?>
                            <option value="<?php echo $branch['BranchID']; ?>"><?php echo $branch['BranchName']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="document.getElementById('updateEmployeeForm').submit();">Update Employee</button>
            </div>
        </div>
    </div>
</div>

<script>
function fillUpdateModal(id, employeeId, fullName, gender, phone, nationality, jobTitle, department, branch) {
    document.getElementById('updateEmployeeId').value = id;
    document.getElementById('updateEmployeeIdField').value = employeeId;
    document.getElementById('updateFullName').value = fullName;
    document.getElementById('updateGender').value = gender;
    document.getElementById('updatePhone').value = phone;
    document.getElementById('updateNationality').value = nationality;
    document.getElementById('updateJobTitle').value = jobTitle;
    document.getElementById('updateDepartment').value = department;
    document.getElementById('updateBranch').value = branch;
}
</script>


    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


</body>
</html>
