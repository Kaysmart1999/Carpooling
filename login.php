<?php if (isset($_GET['logout'])): ?>
  <div class="alert alert-success text-center">You have been logged out successfully.</div>
<?php endif; ?>

<?php
session_start();
include('includes/db_connect.php');

$error = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $query = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {
            // Start session and store user info
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['staff_id'] = $user['staff_id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['department'] = $user['department'];

            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid password. Please try again.";
        }
    } else {
        $error = "No account found with that email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | Car Pooling System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
      height: 100vh;
    }
    .login-container {
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .card {
      border-radius: 12px;
    }
  </style>
</head>
<body>

  <div class="container login-container">
    <div class="col-md-5">
      <div class="card shadow-lg border-0">
        <div class="card-header bg-dark text-white text-center py-3">
          <h4 class="mb-0">Staff Login</h4>
        </div>
        <div class="card-body p-4">

          <?php if ($error): ?>
            <div class="alert alert-danger text-center"><?php echo $error; ?></div>
          <?php endif; ?>

          <form method="POST" action="">
            <div class="mb-3">
              <label class="form-label">Institutional Email</label>
              <input type="email" name="email" class="form-control" placeholder="example@federalpolyede.edu.ng" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Login</button>
          </form>
        </div>

        <div class="card-footer text-center py-3">
          <p class="mb-0">Don’t have an account? <a href="register.php">Register</a></p>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
