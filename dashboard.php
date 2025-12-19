<?php
session_start();

// Redirect if user not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$full_name = $_SESSION['full_name'];
$department = $_SESSION['department'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard | Car Pooling System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color:#f8f9fa; overflow-x:hidden;">

  <!-- Include Sidebar -->
  <?php include('includes/sidebar.php'); ?>

  <!-- Main Content -->
  <div class="main-content" style="margin-left:240px; padding:30px;">
    <div class="container">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <span class="menu-toggle d-md-none" onclick="toggleSidebar()">
            <i class="bi bi-list"></i>
          </span>
          <h3 class="d-inline-block ms-2">Welcome, <?php echo htmlspecialchars($full_name); ?> 👋</h3>
        </div>
        <span class="text-muted">Department: <?php echo htmlspecialchars($department); ?></span>
      </div>

      <div class="row">
        <div class="col-md-6 mb-4">
          <a href="offer_ride.php" class="btn btn-success w-100 p-4 fs-5 shadow">
            <i class="bi bi-car-front-fill"></i> Offer a Ride
          </a>
        </div>
        <div class="col-md-6 mb-4">
          <a href="find_ride.php" class="btn btn-primary w-100 p-4 fs-5 shadow">
            <i class="bi bi-search"></i> Find a Ride
          </a>
        </div>
      </div>

      <div class="card mt-4 border-0 shadow-sm">
        <div class="card-body">
          <h5 class="card-title">System Overview</h5>
          <p class="card-text">
            This platform enables verified staff members of Federal Polytechnic Ede to share or request rides for daily commuting.
            Use the sidebar to offer or find rides, update your profile, and manage your ride history.
          </p>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
