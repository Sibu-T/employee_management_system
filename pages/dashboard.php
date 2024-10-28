<?php
session_start(); // Start the session
include '../includes/db.php';

// Check if the user is logged in and has the 'admin' role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html"); // Redirect to login page
    exit();
}

// Fetch totals from the database
$totalEmployees = $conn->query("SELECT COUNT(*) AS total FROM Employees")->fetch_assoc()['total'];
$totalUsers = $conn->query("SELECT COUNT(*) AS total FROM Users")->fetch_assoc()['total'];
$totalDepartments = $conn->query("SELECT COUNT(*) AS total FROM Departments")->fetch_assoc()['total'];
$totalBranches = $conn->query("SELECT COUNT(*) AS total FROM Branch")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

</head>
<body>

<div class="sidebar">
    <ul class="list-unstyled">
        <li><a href="profile.php"><i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['username']); ?></a></li>
        <li><a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="employees.php"><i class="fas fa-users"></i> Employees</a></li>
        <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
        <li><a href="departments.php"><i class="fas fa-building"></i> Departments</a></li>
        <li><a href="branches.php"><i class="fas fa-code-branch"></i> Branches</a></li>
        <li><a href="overtime.php"><i class="fas fa-clock"></i> Overtime</a></li>
        <li><a href="vacation.php"><i class="fas fa-plane"></i> Vacation</a></li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>


    <div class="content" style="margin-left: 270px; padding: 20px;">
        <h2 class="text-center">Dashboard</h2>
        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-users"></i> <br><br>Total Employees</h5>
                        <h5 class="card-text"><?php echo $totalEmployees; ?></h5>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-users"></i> <br><br>Total Users</h5>
                        <h5 class="card-text"><?php echo $totalUsers; ?></h5>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-building"></i> <br><br>Total Departments</h5>
                        <h5 class="card-text"><?php echo $totalDepartments; ?></h5>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-code-branch"></i> <br><br>Total Branches</h5>
                        <h5 class="card-text"><?php echo $totalBranches; ?></h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
