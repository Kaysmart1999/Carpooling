<?php
include('includes/db_connect.php');

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $staff_id = mysqli_real_escape_string($conn, $_POST['staff_id']);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $department = mysqli_real_escape_string($conn, $_POST['department']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Check if email or staff ID already exists
        $check_query = "SELECT * FROM users WHERE email='$email' OR staff_id='$staff_id'";
        $check_result = mysqli_query($conn, $check_query);

        if (mysqli_num_rows($check_result) > 0) {
            $error = "Email or Staff ID already registered!";
        } else {
            $insert_query = "INSERT INTO users (staff_id, full_name, email, phone, department, password)
                             VALUES ('$staff_id', '$full_name', '$email', '$phone', '$department', '$hashed_password')";

            if (mysqli_query($conn, $insert_query)) {
                $success = "Registration successful! You can now log in.";
            } else {
                $error = "Database Error: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staff Registration | Car Pooling System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-6">

        <div class="card shadow-lg border-0">
          <div class="card-header bg-dark text-white text-center">
            <h4>Staff Registration</h4>
          </div>
          <div class="card-body">

            <?php if ($success): ?>
              <div class="alert alert-success"><?php echo $success; ?></div>
            <?php elseif ($error): ?>
              <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
              
              <div class="mb-3">
                <label class="form-label">Staff ID</label>
                <input type="text" name="staff_id" class="form-control" required>
              </div>

              <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="full_name" class="form-control" required>
              </div>

              <div class="mb-3">
                <label class="form-label">Institutional Email</label>
                <input type="email" name="email" class="form-control" placeholder="example@federalpolyede.edu.ng" required>
              </div>

              <div class="mb-3">
                <label class="form-label">Phone Number</label>
                <input type="text" name="phone" class="form-control" required>
              </div>

              <div class="mb-3">
                <label class="form-label">Department</label>
                <input type="text" name="department" class="form-control" required>
              </div>

              <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
              </div>

              <div class="mb-3">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" required>
              </div>

              <button type="submit" class="btn btn-primary w-100">Register</button>
            </form>

          </div>
          <div class="card-footer text-center">
            <p class="mb-0">Already have an account? <a href="login.php">Login</a></p>
          </div>
        </div>

      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
