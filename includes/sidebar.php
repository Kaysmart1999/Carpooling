<!-- includes/sidebar.php -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<div class="sidebar">
  <h4><i class="bi bi-car-front-fill"></i> Car Pooling</h4>

  <a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
    <i class="bi bi-house-door"></i> Dashboard
  </a>
  
  <a href="#ridesSubmenu" data-bs-toggle="collapse"><i class="bi bi-map"></i> Rides</a>
  <div class="collapse <?= in_array(basename($_SERVER['PHP_SELF']), ['offer_ride.php','find_ride.php','my_rides.php']) ? 'show' : '' ?>" id="ridesSubmenu">
    <a href="offer_ride.php" class="ps-5 <?= basename($_SERVER['PHP_SELF']) == 'offer_ride.php' ? 'active' : '' ?>"><i class="bi bi-car-front"></i> Offer a Ride</a>
    <a href="find_ride.php" class="ps-5 <?= basename($_SERVER['PHP_SELF']) == 'find_ride.php' ? 'active' : '' ?>"><i class="bi bi-search"></i> Find a Ride</a>
    <a href="my_rides.php" class="ps-5 <?= basename($_SERVER['PHP_SELF']) == 'my_rides.php' ? 'active' : '' ?>"><i class="bi bi-list-ul"></i> My Rides</a>
    <a href="manage_bookings.php" class="ps-5 <?= basename($_SERVER['PHP_SELF'])=='manage_bookings.php'?'active':''; ?>"><i class="bi bi-person-check"></i> Manage Bookings</a>
</div>

  <a href="#profileSubmenu" data-bs-toggle="collapse"><i class="bi bi-person-circle"></i> Profile</a>
  <div class="collapse <?= in_array(basename($_SERVER['PHP_SELF']), ['edit_profile.php','change_password.php']) ? 'show' : '' ?>" id="profileSubmenu">
    <a href="edit_profile.php" class="ps-5 <?= basename($_SERVER['PHP_SELF']) == 'edit_profile.php' ? 'active' : '' ?>"><i class="bi bi-pencil-square"></i> Edit Profile</a>
    <a href="change_password.php" class="ps-5 <?= basename($_SERVER['PHP_SELF']) == 'change_password.php' ? 'active' : '' ?>"><i class="bi bi-key"></i> Change Password</a>
  </div>

  <a href="logout.php" class="text-danger"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<style>
  .sidebar {
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    width: 240px;
    background-color: #212529;
    color: white;
    padding-top: 20px;
    transition: all 0.3s;
    z-index: 1000;
  }
  .sidebar h4 {
    text-align: center;
    margin-bottom: 20px;
    font-size: 1.3rem;
  }
  .sidebar a {
    display: block;
    color: #adb5bd;
    text-decoration: none;
    padding: 10px 20px;
    font-size: 0.95rem;
    border-left: 3px solid transparent;
  }
  .sidebar a:hover,
  .sidebar a.active {
    background-color: #343a40;
    color: #fff;
    border-left: 3px solid #0d6efd;
  }

  /* Mobile responsiveness */
  @media (max-width: 768px) {
    .sidebar {
      width: 200px;
      left: -200px;
      position: fixed;
    }
    .sidebar.active {
      left: 0;
    }
    .menu-toggle {
      display: inline-block !important;
      font-size: 1.6rem;
      cursor: pointer;
    }
  }
</style>

<script>
  function toggleSidebar() {
    document.querySelector('.sidebar').classList.toggle('active');
  }
</script>
