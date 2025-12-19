<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Car Pooling System for Staff | Federal Polytechnic Ede</title>
  
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Custom CSS -->
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

  <!-- Navigation Bar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow">
    <div class="container">
      <a class="navbar-brand fw-bold" href="index.php">🚗 CarPool Ede</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
          <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link btn btn-primary text-white ms-2" href="register.php">Register</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="bg-light text-center text-dark py-5">
    <div class="container">
      <h1 class="display-4 fw-bold mb-3">Smart Ride-Sharing for Federal Polytechnic Ede Staff</h1>
      <p class="lead mb-4">
        Connect with colleagues, share rides, save fuel, and reduce traffic. 
        A convenient way to commute safely within the campus and city.
      </p>
      <a href="register.php" class="btn btn-success btn-lg me-2">Get Started</a>
      <a href="about.php" class="btn btn-outline-dark btn-lg">Learn More</a>
    </div>
  </section>

  <!-- Features Section -->
  <section class="py-5 bg-white">
    <div class="container text-center">
      <h2 class="mb-4 fw-bold">Why Use Car Pooling System?</h2>
      <div class="row g-4">
        <div class="col-md-4">
          <div class="card border-0 shadow-sm p-4 h-100">
            <i class="bi bi-people fs-1 text-primary mb-3"></i>
            <h5>Connect with Colleagues</h5>
            <p>Find staff members heading in the same direction and share a ride together.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card border-0 shadow-sm p-4 h-100">
            <i class="bi bi-currency-exchange fs-1 text-success mb-3"></i>
            <h5>Save Fuel & Cost</h5>
            <p>Reduce transportation expenses by sharing the cost of commuting.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card border-0 shadow-sm p-4 h-100">
            <i class="bi bi-shield-check fs-1 text-warning mb-3"></i>
            <h5>Secure & Staff-Only</h5>
            <p>Access is limited to verified staff using institutional credentials.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-dark text-center text-white py-3 mt-5">
    <p class="mb-0">&copy; <?php echo date("Y"); ?> CarPool Ede | Developed by Lukman Toheeb Alabi | HC20230101316 OF CS Department, Federal Polytechnic Ede</p>
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
