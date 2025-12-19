<?php
session_start();
include('includes/db_connect.php');

// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\Exception;

// require 'PHPMailer/src/Exception.php';
// require 'PHPMailer/src/PHPMailer.php';
// require 'PHPMailer/src/SMTP.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$driver_id = $_SESSION['user_id'];
$message = "";

// Handle booking status update
if (isset($_POST['update_status'])) {
    $booking_id = intval($_POST['booking_id']);
    $status = $_POST['status'];

    $booking_check = mysqli_query($conn, "SELECT bookings.*, rides.user_id AS driver_id, rides.departure, rides.destination, rides.departure_date, rides.departure_time, users.full_name AS rider_name, users.email AS rider_email FROM bookings 
        INNER JOIN rides ON bookings.ride_id = rides.ride_id 
        INNER JOIN users ON bookings.rider_id = users.user_id 
        WHERE bookings.booking_id='$booking_id' AND rides.user_id='$driver_id'");

    if (mysqli_num_rows($booking_check) > 0) {
        $booking = mysqli_fetch_assoc($booking_check);

        mysqli_query($conn, "UPDATE bookings SET status='$status' WHERE booking_id='$booking_id'");

        if ($status == 'approved') {
            mysqli_query($conn, "UPDATE rides SET available_seats = available_seats - 1 WHERE ride_id='".$booking['ride_id']."' AND available_seats > 0");
        }

        // Notify rider via PHPMailer
        $subject = "Booking Status Update for your ride request";
        $body = "
            Hello ".$booking['rider_name'].",<br><br>
            Your booking request for the ride:<br>
            <b>From:</b> ".$booking['departure']."<br>
            <b>To:</b> ".$booking['destination']."<br>
            <b>Date:</b> ".$booking['departure_date']." at ".$booking['departure_time']."<br><br>
            <b>Status:</b> ".ucfirst($status)."<br><br>
            Thank you for using Federal Polytechnic Ede Car Pooling System.
        ";

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'your_email@gmail.com'; 
            $mail->Password = 'your_app_password';    
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('your_email@gmail.com', 'Car Pooling System');
            $mail->addAddress($booking['rider_email'], $booking['rider_name']);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;

            $mail->send();
            $message = "<div class='alert alert-success'>Booking status updated and rider notified.</div>";
        } catch (Exception $e) {
            $message = "<div class='alert alert-warning'>Booking updated but email not sent. Mailer Error: {$mail->ErrorInfo}</div>";
        }
    } else {
        $message = "<div class='alert alert-danger'>Booking not found or unauthorized.</div>";
    }
}

// Optional search
$search_rider = isset($_GET['rider_name']) ? mysqli_real_escape_string($conn, $_GET['rider_name']) : '';

$where = "rides.user_id='$driver_id'";
if ($search_rider) $where .= " AND users.full_name LIKE '%$search_rider%'";

$bookings_query = "
    SELECT bookings.*, rides.departure, rides.destination, rides.departure_date, rides.departure_time, users.full_name AS rider_name, users.email AS rider_email
    FROM bookings
    INNER JOIN rides ON bookings.ride_id = rides.ride_id
    INNER JOIN users ON bookings.rider_id = users.user_id
    WHERE $where
    ORDER BY bookings.booking_time DESC
";
$bookings_result = mysqli_query($conn, $bookings_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Bookings | Car Pooling System</title>
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
        <h3 class="mb-4">Manage Bookings</h3>
        <?= $message ?>

        <!-- Search Card -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <form class="row g-3" method="GET" action="">
                    <div class="col-md-6">
                        <input type="text" name="rider_name" class="form-control" placeholder="Search by Rider Name" value="<?= htmlspecialchars($search_rider); ?>">
                    </div>
                    <div class="col-md-6 d-grid">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Search</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bookings Table -->
        <?php if(mysqli_num_rows($bookings_result) > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Rider Name</th>
                        <th>Email</th>
                        <th>Ride</th>
                        <th>Date & Time</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i=1; while($booking = mysqli_fetch_assoc($bookings_result)): ?>
                    <tr>
                        <td><?= $i++; ?></td>
                        <td><?= htmlspecialchars($booking['rider_name']); ?></td>
                        <td><?= htmlspecialchars($booking['rider_email']); ?></td>
                        <td><?= htmlspecialchars($booking['departure'] . " → " . $booking['destination']); ?></td>
                        <td><?= htmlspecialchars($booking['departure_date'] . " " . $booking['departure_time']); ?></td>
                        <td><?= ucfirst($booking['status']); ?></td>
                        <td>
                            <form method="POST" class="d-flex gap-1">
                                <input type="hidden" name="booking_id" value="<?= $booking['booking_id']; ?>">
                                <?php if($booking['status'] == 'pending'): ?>
                                    <button type="submit" name="update_status" value="approved" class="btn btn-success btn-sm">Approve</button>
                                    <button type="submit" name="update_status" value="rejected" class="btn btn-danger btn-sm">Reject</button>
                                <?php elseif($booking['status'] == 'approved'): ?>
                                    <button type="submit" name="update_status" value="completed" class="btn btn-primary btn-sm">Complete</button>
                                    <button type="submit" name="update_status" value="cancelled" class="btn btn-warning btn-sm">Cancel</button>
                                <?php else: ?>
                                    <span class="text-muted">No actions</span>
                                <?php endif; ?>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <div class="alert alert-info">No bookings found.</div>
        <?php endif; ?>
    </div>
</div>

<script>
function toggleSidebar(){document.getElementById('sidebar').classList.toggle('active');}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
