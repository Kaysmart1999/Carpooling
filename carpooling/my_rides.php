<?php
session_start();
include('includes/db_connect.php');

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

// Handle Ride Deletion
if (isset($_POST['delete_ride'])) {
    $ride_id = intval($_POST['ride_id']);

    // Ensure only the creator of the ride can delete it
    $check = mysqli_query($conn, "SELECT * FROM rides WHERE ride_id='$ride_id' AND user_id='$user_id'");
    if (mysqli_num_rows($check) > 0) {
        mysqli_query($conn, "DELETE FROM rides WHERE ride_id='$ride_id'");
        $message = "<div class='alert alert-success'>Ride deleted successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Unauthorized action or ride not found.</div>";
    }
}

// Handle Booking Cancellation
if (isset($_POST['cancel_booking'])) {
    $booking_id = intval($_POST['booking_id']);
    $check = mysqli_query($conn, "SELECT * FROM bookings WHERE booking_id='$booking_id' AND rider_id='$user_id'");
    if (mysqli_num_rows($check) > 0) {
        mysqli_query($conn, "UPDATE bookings SET status='cancelled' WHERE booking_id='$booking_id'");
        $message = "<div class='alert alert-warning'>Booking cancelled successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Unauthorized action or booking not found.</div>";
    }
}

// Fetch rides offered by user
$sql_offered = "
    SELECT rides.*, users.full_name AS driver_name, users.phone AS driver_phone
    FROM rides
    INNER JOIN users ON rides.user_id = users.user_id
    WHERE rides.user_id = '$user_id'
    ORDER BY rides.departure_date DESC
";
$result_offered = mysqli_query($conn, $sql_offered);

// Fetch rides joined/booked by user
$sql_joined = "
    SELECT rides.*, bookings.booking_id, bookings.status AS booking_status, u.full_name AS driver_name, u.phone AS driver_phone
    FROM bookings
    INNER JOIN rides ON bookings.ride_id = rides.ride_id
    INNER JOIN users AS u ON rides.user_id = u.user_id
    WHERE bookings.rider_id = '$user_id'
    ORDER BY bookings.booking_time DESC
";
$result_joined = mysqli_query($conn, $sql_joined);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Rides | Car Pooling System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
      overflow-x: hidden;
    }
    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      height: 100vh;
      width: 240px;
      background-color: #212529;
      color: white;
      padding-top: 20px;
      transition: all 0.3s ease;
    }
    .sidebar a {
      display: block;
      color: #adb5bd;
      text-decoration: none;
      padding: 10px 20px;
    }
    .sidebar a:hover, .sidebar a.active {
      background-color: #343a40;
      color: #fff;
    }
    .main-content {
      margin-left: 240px;
      padding: 40px;
      transition: all 0.3s ease;
    }
    .toggle-btn {
      display: none;
      font-size: 24px;
      cursor: pointer;
      margin-bottom: 15px;
      color: #212529;
    }
    @media (max-width: 768px) {
      .sidebar {
        left: -240px;
        position: fixed;
        z-index: 1000;
      }
      .sidebar.active {
        left: 0;
      }
      .main-content {
        margin-left: 0;
        width: 100%;
      }
      .toggle-btn {
        display: block;
      }
    }
  </style>
</head>
<body>

  <!-- Sidebar (modular include) -->
  <?php include('includes/sidebar.php'); ?>

  <!-- Main Content -->
  <div class="main-content">
    <span class="toggle-btn" onclick="toggleSidebar()"><i class="bi bi-list"></i></span>
    <div class="container">
      <h3 class="mb-4">My Rides</h3>

      <?= $message ?>

      <!-- Rides I Offered -->
      <h5 class="text-success mb-3"><i class="bi bi-car-front"></i> Rides I Offered</h5>
      <?php if (mysqli_num_rows($result_offered) > 0): ?>
        <div class="row">
          <?php while ($ride = mysqli_fetch_assoc($result_offered)): ?>
            <div class="col-md-6 mb-4">
              <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                  <h5 class="card-title text-success"><i class="bi bi-geo-alt"></i>
                    <?= htmlspecialchars($ride['departure']); ?> → <?= htmlspecialchars($ride['destination']); ?>
                  </h5>
                  <p class="mb-1"><i class="bi bi-calendar-event"></i> <?= htmlspecialchars($ride['departure_date']); ?></p>
                  <p class="mb-1"><i class="bi bi-clock"></i> <?= htmlspecialchars($ride['departure_time']); ?></p>
                  <p class="mb-1"><i class="bi bi-car-front"></i> Car: <?= htmlspecialchars($ride['car_model'] ?: 'Not specified'); ?></p>
                  <p class="mb-1"><i class="bi bi-people"></i> Seats: <?= htmlspecialchars($ride['available_seats']); ?></p>
                  <?php if (!empty($ride['additional_info'])): ?>
                    <p class="text-muted"><i class="bi bi-info-circle"></i> <?= htmlspecialchars($ride['additional_info']); ?></p>
                  <?php endif; ?>
                  <form method="POST" onsubmit="return confirm('Are you sure you want to delete this ride?');">
                    <input type="hidden" name="ride_id" value="<?= $ride['ride_id']; ?>">
                    <button type="submit" name="delete_ride" class="btn btn-sm btn-outline-danger w-100 mt-2">
                      <i class="bi bi-trash"></i> Delete Ride
                    </button>
                  </form>
                </div>
              </div>
            </div>
          <?php endwhile; ?>
        </div>
      <?php else: ?>
        <div class="alert alert-info">You haven’t offered any rides yet.</div>
      <?php endif; ?>

      <hr class="my-5">

      <!-- Rides I Joined -->
      <h5 class="text-primary mb-3"><i class="bi bi-person-check"></i> Rides I Joined</h5>
      <?php if (mysqli_num_rows($result_joined) > 0): ?>
        <div class="row">
          <?php while ($ride = mysqli_fetch_assoc($result_joined)): ?>
            <div class="col-md-6 mb-4">
              <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                  <h5 class="card-title text-primary"><i class="bi bi-geo-alt"></i>
                    <?= htmlspecialchars($ride['departure']); ?> → <?= htmlspecialchars($ride['destination']); ?>
                  </h5>
                  <p class="mb-1"><i class="bi bi-calendar-event"></i> <?= htmlspecialchars($ride['departure_date']); ?></p>
                  <p class="mb-1"><i class="bi bi-clock"></i> <?= htmlspecialchars($ride['departure_time']); ?></p>
                  <p class="mb-1"><i class="bi bi-person"></i> Driver: <?= htmlspecialchars($ride['driver_name']); ?></p>
                  <p class="mb-1"><i class="bi bi-telephone"></i> <?= htmlspecialchars($ride['driver_phone']); ?></p>
                  <p class="mb-1"><i class="bi bi-check-circle"></i> 
                    Status: <span class="badge bg-<?= ($ride['booking_status']=='approved'?'success':($ride['booking_status']=='pending'?'warning':'secondary')) ?>">
                      <?= ucfirst($ride['booking_status']); ?>
                    </span>
                  </p>
                  <?php if ($ride['booking_status'] != 'cancelled' && $ride['booking_status'] != 'completed'): ?>
                    <form method="POST" onsubmit="return confirm('Cancel this booking?');">
                      <input type="hidden" name="booking_id" value="<?= $ride['booking_id']; ?>">
                      <button type="submit" name="cancel_booking" class="btn btn-sm btn-outline-danger w-100 mt-2">
                        <i class="bi bi-x-circle"></i> Cancel Booking
                      </button>
                    </form>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php endwhile; ?>
        </div>
      <?php else: ?>
        <div class="alert alert-info">You haven’t joined any rides yet.</div>
      <?php endif; ?>
    </div>
  </div>

  <script>
    function toggleSidebar() {
      document.getElementById('sidebar').classList.toggle('active');
    }
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
