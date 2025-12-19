<?php
session_start();
include('includes/db_connect.php');

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$full_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : '';
$department = isset($_SESSION['department']) ? $_SESSION['department'] : '';

$success = "";
$error = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // sanitize inputs
    $departure = mysqli_real_escape_string($conn, trim($_POST['departure']));
    $destination = mysqli_real_escape_string($conn, trim($_POST['destination']));
    $departure_date = mysqli_real_escape_string($conn, trim($_POST['departure_date']));
    $departure_time = mysqli_real_escape_string($conn, trim($_POST['departure_time']));
    $available_seats = (int) $_POST['available_seats'];
    $car_model = mysqli_real_escape_string($conn, trim($_POST['car_model']));
    $additional_info = mysqli_real_escape_string($conn, trim($_POST['additional_info']));

    // basic validation
    if ($departure === "" || $destination === "" || $departure_date === "" || $departure_time === "" || $available_seats <= 0) {
        $error = "Please fill in all required fields.";
    } else {
        // insert into rides table (matches earlier DB structure)
        $sql = "INSERT INTO rides (user_id, departure, destination, departure_date, departure_time, available_seats, car_model, additional_info)
                VALUES ('$user_id', '$departure', '$destination', '$departure_date', '$departure_time', '$available_seats', '$car_model', '$additional_info')";

        if (mysqli_query($conn, $sql)) {
            $success = "Ride offer created successfully!";
        } else {
            $error = "Database error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Offer a Ride | Car Pooling System</title>

  <!-- Bootstrap & icons -->
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

    /* Ensure sidebar is provided by include; these are only safety styles */
    .main-content {
      margin-left: var(--sidebar-width);
      padding: 28px;
      transition: margin-left 0.25s ease;
    }

    .topbar {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 18px;
    }

    .menu-toggle {
      display: none;
      font-size: 1.35rem;
      cursor: pointer;
    }

    @media (max-width: 768px) {
      /* hide sidebar by default on small screens (sidebar include adds .active to show) */
      .main-content {
        margin-left: 0 !important;
        width: 100%;
        padding: 18px;
      }
      .menu-toggle {
        display: inline-block;
      }
    }

    /* Card tweaks */
    .card {
      border-radius: 12px;
    }
  </style>
</head>
<body>

  <!-- Sidebar (modular include) -->
  <?php include('includes/sidebar.php'); ?>

  <!-- Main content -->
  <div class="main-content">
    <div class="container-fluid">

      <div class="topbar">
        <span class="menu-toggle d-md-none" onclick="toggleSidebar()" title="Menu">
          <i class="bi bi-list"></i>
        </span>

        <div>
          <h4 class="mb-0">Offer a Ride</h4>
          <small class="text-muted">Welcome, <?php echo htmlspecialchars($full_name); ?> — <?php echo htmlspecialchars($department); ?></small>
        </div>
      </div>

      <!-- feedback -->
      <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
      <?php elseif ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <div class="card shadow-sm mb-4">
        <div class="card-body">
          <h5 class="card-title mb-3">Provide Ride Details</h5>

          <form method="POST" action="">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Departure <span class="text-danger">*</span></label>
                <input type="text" name="departure" class="form-control" placeholder="e.g. Ede North" required>
              </div>

              <div class="col-md-6">
                <label class="form-label">Destination <span class="text-danger">*</span></label>
                <input type="text" name="destination" class="form-control" placeholder="e.g. Campus Main Gate" required>
              </div>

              <div class="col-md-6">
                <label class="form-label">Departure Date <span class="text-danger">*</span></label>
                <input type="date" name="departure_date" class="form-control" required>
              </div>

              <div class="col-md-6">
                <label class="form-label">Departure Time <span class="text-danger">*</span></label>
                <input type="time" name="departure_time" class="form-control" required>
              </div>

              <div class="col-md-6">
                <label class="form-label">Available Seats <span class="text-danger">*</span></label>
                <input type="number" name="available_seats" class="form-control" min="1" max="12" value="1" required>
              </div>

              <div class="col-md-6">
                <label class="form-label">Car Model (optional)</label>
                <input type="text" name="car_model" class="form-control" placeholder="e.g. Toyota Corolla">
              </div>

              <div class="col-12">
                <label class="form-label">Additional Information (optional)</label>
                <textarea name="additional_info" class="form-control" rows="3" placeholder="e.g. No smoking, prefer quiet rides"></textarea>
              </div>

              <div class="col-12 d-grid">
                <button type="submit" class="btn btn-success"><i class="bi bi-send-check me-1"></i> Publish Ride</button>
              </div>
            </div>
          </form>

        </div>
      </div>

    </div>
  </div>

  <!-- Bootstrap JS bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Toggle sidebar for mobile (sidebar include should define .sidebar)
    function toggleSidebar() {
      const sb = document.querySelector('.sidebar');
      if (!sb) return;
      sb.classList.toggle('active');

      // if we open sidebar on mobile, prevent body scroll (optional)
      if (window.innerWidth <= 768) {
        document.body.style.overflow = sb.classList.contains('active') ? 'hidden' : '';
      }
    }

    // When viewport resizes to large screens, ensure sidebar visible and body scroll enabled
    window.addEventListener('resize', function() {
      const sb = document.querySelector('.sidebar');
      if (!sb) return;
      if (window.innerWidth > 768) {
        sb.classList.remove('active'); // ensure consistent desktop layout (sidebar is visible via include CSS)
        document.body.style.overflow = '';
      }
    });
  </script>
</body>
</html>
