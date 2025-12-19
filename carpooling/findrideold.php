<?php
session_start();
include('includes/db_connect.php');

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$full_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : '';
$department = isset($_SESSION['department']) ? $_SESSION['department'] : '';

$search_results = [];
$error = "";

// Handle search
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $departure = mysqli_real_escape_string($conn, trim($_POST['departure']));
    $destination = mysqli_real_escape_string($conn, trim($_POST['destination']));

    $sql = "SELECT rides.*, users.full_name, users.department 
            FROM rides 
            JOIN users ON rides.user_id = users.user_id
            WHERE 1=1";

    if ($departure !== "") {
        $sql .= " AND rides.departure LIKE '%$departure%'";
    }
    if ($destination !== "") {
        $sql .= " AND rides.destination LIKE '%$destination%'";
    }

    $sql .= " ORDER BY rides.departure_date ASC, rides.departure_time ASC";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $search_results[] = $row;
        }
    } else {
        $error = "No rides found matching your search.";
    }
} else {
    // Default: show all upcoming rides
    $today = date("Y-m-d");
    $sql = "SELECT rides.*, users.full_name, users.department 
            FROM rides 
            JOIN users ON rides.user_id = users.user_id 
            WHERE rides.departure_date >= '$today'
            ORDER BY rides.departure_date ASC, rides.departure_time ASC";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $search_results[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Find a Ride | Car Pooling System</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    :root {
      --sidebar-width: 240px;
    }

    body {
      background-color: #f8f9fa;
      overflow-x: hidden;
    }

    .main-content {
      margin-left: var(--sidebar-width);
      padding: 28px;
      transition: margin-left 0.25s ease;
    }

    .menu-toggle {
      display: none;
      font-size: 1.35rem;
      cursor: pointer;
    }

    @media (max-width: 768px) {
      .main-content {
        margin-left: 0 !important;
        width: 100%;
        padding: 18px;
      }
      .menu-toggle {
        display: inline-block;
      }
    }

    .ride-card {
      border-radius: 12px;
      margin-bottom: 15px;
    }

    .ride-card h6 {
      color: #0d6efd;
      font-weight: 600;
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <?php include('includes/sidebar.php'); ?>

  <!-- Main content -->
  <div class="main-content">
    <div class="container-fluid">

      <div class="d-flex align-items-center mb-4">
        <span class="menu-toggle d-md-none me-3" onclick="toggleSidebar()" title="Menu">
          <i class="bi bi-list"></i>
        </span>
        <div>
          <h4 class="mb-0">Find a Ride</h4>
          <small class="text-muted">Welcome, <?php echo htmlspecialchars($full_name); ?> — <?php echo htmlspecialchars($department); ?></small>
        </div>
      </div>

      <!-- Search form -->
      <div class="card shadow-sm mb-4">
        <div class="card-body">
          <form method="POST" class="row g-3">
            <div class="col-md-5">
              <label class="form-label">Departure</label>
              <input type="text" name="departure" class="form-control" placeholder="e.g. Ede North">
            </div>
            <div class="col-md-5">
              <label class="form-label">Destination</label>
              <input type="text" name="destination" class="form-control" placeholder="e.g. Campus Main Gate">
            </div>
            <div class="col-md-2 d-flex align-items-end">
              <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Search</button>
            </div>
          </form>
        </div>
      </div>

      <!-- Search results -->
      <?php if ($error): ?>
        <div class="alert alert-warning text-center"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <div class="row">
        <?php foreach ($search_results as $ride): ?>
          <div class="col-md-6">
            <div class="card ride-card shadow-sm">
              <div class="card-body">
                <h6><i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($ride['departure']); ?> → <?php echo htmlspecialchars($ride['destination']); ?></h6>
                <p class="mb-1"><i class="bi bi-calendar-event"></i> <?php echo htmlspecialchars($ride['departure_date']); ?> @ <?php echo htmlspecialchars($ride['departure_time']); ?></p>
                <p class="mb-1"><i class="bi bi-person-circle"></i> Driver: <?php echo htmlspecialchars($ride['full_name']); ?> (<?php echo htmlspecialchars($ride['department']); ?>)</p>
                <p class="mb-1"><i class="bi bi-car-front"></i> Car: <?php echo htmlspecialchars($ride['car_model'] ?: 'N/A'); ?></p>
                <p class="mb-1"><i class="bi bi-people"></i> Available Seats: <?php echo htmlspecialchars($ride['available_seats']); ?></p>
                <?php if (!empty($ride['additional_info'])): ?>
                  <p class="text-muted small"><i class="bi bi-info-circle"></i> <?php echo htmlspecialchars($ride['additional_info']); ?></p>
                <?php endif; ?>
                <button class="btn btn-outline-success btn-sm mt-2 w-100"><i class="bi bi-chat-left-text"></i> Contact Driver</button>
             
            
                    <!-- HHJHGJHJHJH -->
             <h5 class="card-title text-primary">
                    <i class="bi bi-geo-alt"></i>
                    <?= htmlspecialchars($ride['departure']); ?> → <?= htmlspecialchars($ride['destination']); ?>
                  </h5>
                  <p class="mb-1"><i class="bi bi-calendar-event"></i> <?= htmlspecialchars($ride['departure_date']); ?></p>
                  <p class="mb-1"><i class="bi bi-clock"></i> <?= htmlspecialchars($ride['departure_time']); ?></p>
                  <p class="mb-1"><i class="bi bi-person"></i> Driver: <?= htmlspecialchars($ride['driver_name']); ?></p>
                  <p class="mb-1"><i class="bi bi-telephone"></i> <?= htmlspecialchars($ride['driver_phone']); ?></p>
                  <p class="mb-1"><i class="bi bi-people"></i> Seats left: <?= htmlspecialchars($ride['available_seats']); ?></p>
                  <?php if (!empty($ride['additional_info'])): ?>
                    <p class="text-muted"><i class="bi bi-info-circle"></i> <?= htmlspecialchars($ride['additional_info']); ?></p>
                  <?php endif; ?>

                  <form method="POST" onsubmit="return confirm('Do you want to join this ride?');">
                    <input type="hidden" name="ride_id" value="<?= $ride['ride_id']; ?>">
                    <button type="submit" name="join_ride" class="btn btn-sm btn-success w-100 mt-2">
                      <i class="bi bi-plus-circle"></i> Join Ride
                    </button>
                  </form>
            
            </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    function toggleSidebar() {
      const sb = document.querySelector('.sidebar');
      if (!sb) return;
      sb.classList.toggle('active');

      if (window.innerWidth <= 768) {
        document.body.style.overflow = sb.classList.contains('active') ? 'hidden' : '';
      }
    }

    window.addEventListener('resize', function() {
      const sb = document.querySelector('.sidebar');
      if (!sb) return;
      if (window.innerWidth > 768) {
        sb.classList.remove('active');
        document.body.style.overflow = '';
      }
    });
  </script>

</body>
</html>
