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

// Handle Join Ride action
if (isset($_POST['join_ride'])) {
    $ride_id = intval($_POST['ride_id']);
    $ride_check = mysqli_query($conn, "SELECT * FROM rides WHERE ride_id='$ride_id' AND available_seats > 0");
    if (mysqli_num_rows($ride_check) > 0) {
        $ride = mysqli_fetch_assoc($ride_check);
        if ($ride['user_id'] == $user_id) {
            $message = "<div class='alert alert-warning'>You cannot join your own ride.</div>";
        } else {
            $check_existing = mysqli_query($conn, "SELECT * FROM bookings WHERE ride_id='$ride_id' AND rider_id='$user_id'");
            if (mysqli_num_rows($check_existing) > 0) {
                $message = "<div class='alert alert-info'>You have already joined this ride.</div>";
            } else {
                $insert = mysqli_query($conn, "INSERT INTO bookings (ride_id, rider_id, status, booking_time) VALUES ('$ride_id', '$user_id', 'pending', NOW())");
                if ($insert) {
                    mysqli_query($conn, "UPDATE rides SET available_seats = available_seats - 1 WHERE ride_id='$ride_id'");
                    // Notify driver via email
                    $driver_query = mysqli_query($conn, "SELECT full_name, email FROM users WHERE user_id='".$ride['user_id']."'");
                    $driver = mysqli_fetch_assoc($driver_query);
                    $rider_query = mysqli_query($conn, "SELECT full_name, email FROM users WHERE user_id='$user_id'");
                    $rider = mysqli_fetch_assoc($rider_query);
                    $to = $driver['email'];
                    $subject = "New Ride Booking Request from ".$rider['full_name'];
                    $body = "
                        Hello ".$driver['full_name'].",<br><br>
                        A staff member has requested to join your ride:<br>
                        <b>From:</b> ".$ride['departure']."<br>
                        <b>To:</b> ".$ride['destination']."<br>
                        <b>Date:</b> ".$ride['departure_date']."<br><br>
                        <b>Rider Name:</b> ".$rider['full_name']."<br>
                        <b>Email:</b> ".$rider['email']."<br><br>
                        Please log in to approve or decline the request.<br><br>
                        <i>Federal Polytechnic Ede Car Pooling System</i>
                    ";
                    $headers = "MIME-Version: 1.0" . "\r\n";
                    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                    $headers .= "From: noreply@carpooling.com\r\n";
                    @mail($to, $subject, $body, $headers);
                    $message = "<div class='alert alert-success'>Ride joined successfully! The driver has been notified by email.</div>";
                } else {
                    $message = "<div class='alert alert-danger'>Error joining ride. Please try again.</div>";
                }
            }
        }
    } else {
        $message = "<div class='alert alert-danger'>Ride not found or no available seats left.</div>";
    }
}

// Handle search/filter
$search_departure = isset($_GET['departure']) ? mysqli_real_escape_string($conn, $_GET['departure']) : '';
$search_destination = isset($_GET['destination']) ? mysqli_real_escape_string($conn, $_GET['destination']) : '';

$where = "rides.available_seats > 0";
if ($search_departure) $where .= " AND rides.departure LIKE '%$search_departure%'";
if ($search_destination) $where .= " AND rides.destination LIKE '%$search_destination%'";

// Fetch filtered rides
$query = "
    SELECT rides.*, users.full_name AS driver_name, users.email AS driver_email, users.phone AS driver_phone
    FROM rides
    INNER JOIN users ON rides.user_id = users.user_id
    WHERE $where
    ORDER BY rides.departure_date ASC
";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Find Ride | Car Pooling System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background-color: #f8f9fa; overflow-x: hidden; }
    .main-content { margin-left: 240px; padding: 40px; transition: all 0.3s; }
    .toggle-btn { display: none; font-size: 24px; cursor: pointer; margin-bottom: 15px; color: #212529; }
    @media (max-width:768px){.main-content{margin-left:0;width:100%;}.toggle-btn{display:block;}}
  </style>
</head>
<body>

  <!-- Include Sidebar -->
  <?php include('includes/sidebar.php'); ?>

  <div class="main-content">
    <span class="toggle-btn" onclick="toggleSidebar()"><i class="bi bi-list"></i></span>
    <div class="container">
      <h3 class="mb-4">Find a Ride</h3>
      <?= $message ?>

      <!-- Search Card -->
      <div class="card mb-4 shadow-sm">
        <div class="card-body">
          <form class="row g-3" method="GET" action="">
            <div class="col-md-6">
              <input type="text" name="departure" class="form-control" placeholder="Departure" value="<?= htmlspecialchars($search_departure); ?>">
            </div>
            <div class="col-md-6">
              <input type="text" name="destination" class="form-control" placeholder="Destination" value="<?= htmlspecialchars($search_destination); ?>">
            </div>
            <div class="col-md-12 d-grid mt-2">
              <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Search</button>
            </div>
          </form>
        </div>
      </div>

      <!-- Display Rides -->
      <?php if(mysqli_num_rows($result)>0): ?>
        <div class="row">
        <?php while($ride = mysqli_fetch_assoc($result)): ?>
          <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
              <div class="card-body">
                <h5 class="card-title text-primary">
                  <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($ride['departure']); ?> → <?= htmlspecialchars($ride['destination']); ?>
                </h5>
                <p class="mb-1"><i class="bi bi-calendar-event"></i> <?= htmlspecialchars($ride['departure_date']); ?></p>
                <p class="mb-1"><i class="bi bi-clock"></i> <?= htmlspecialchars($ride['departure_time']); ?></p>
                <p class="mb-1"><i class="bi bi-person"></i> Driver: <?= htmlspecialchars($ride['driver_name']); ?></p>
                <p class="mb-1"><i class="bi bi-telephone"></i> Phone: <?= htmlspecialchars($ride['driver_phone']); ?></p>
                <p class="mb-1"><i class="bi bi-car-front"></i> Car Model: <?= htmlspecialchars($ride['car_model']); ?></p>
                <p class="mb-1"><i class="bi bi-people"></i> Seats left: <?= htmlspecialchars($ride['available_seats']); ?></p>
                <?php if(!empty($ride['additional_info'])): ?>
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
        <?php endwhile; ?>
        </div>
      <?php else: ?>
        <div class="alert alert-info">No rides available matching your search.</div>
      <?php endif; ?>
    </div>
  </div>

<script>
function toggleSidebar(){document.getElementById('sidebar').classList.toggle('active');}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
