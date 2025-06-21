<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: index.html");
  exit();
}
require 'php/db_connect.php';

$user_id = $_SESSION['user_id'];

// Get user data
$stmt = $conn->prepare("SELECT name, email, membership FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $email, $membership);
$stmt->fetch();
$stmt->close();

// Get bookings
$bookings = [];
$bookingQuery = $conn->prepare("SELECT class_name, location, date, time FROM user_bookings WHERE user_id = ?");
$bookingQuery->bind_param("i", $user_id);
$bookingQuery->execute();
$result = $bookingQuery->get_result();
while ($row = $result->fetch_assoc()) {
  $bookings[] = $row;
}
$bookingQuery->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Apex Strength | Profile</title>
  <link rel="stylesheet" href="css/styles.css" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>

<!-- Navigation -->
<header class="navbar">
  <div class="logo-container">
    <img src="assets/logo.png" alt="Logo" />
    <div class="logo">Apex Strength</div>
  </div>
  <nav>
    <ul>
      <li><a href="index.html">Home</a></li>
      <li><a href="trainers.html">Trainers</a></li>
      <li><a href="classes.html">Classes</a></li>
      <li><a href="membership.html">Membership</a></li>
      <li><a href="locations.html">Locations</a></li>
      <li><a href="blog.html">Blog</a></li>
      <li><a href="contact.html">Contact Us</a></li>
      <li><a href="profile.php" class="profile-tab">Profile</a></li>
    </ul>
  </nav>
</header>

<!-- Banner -->
<section class="hero-banner trainers-page">
  <div class="hero-overlay">
    <h1 class="hero-title">
      <div class="hero-text-box">Your Profile</div>
    </h1>
    <img src="assets/profilebanner.jpg" alt="Profile Banner" class="hero-img" />
  </div>
</section>

<!-- Profile Info -->
<section class="feature-block">
  <h3 class="h2">Welcome, <?php echo htmlspecialchars($name); ?></h3>
  <p><strong>User ID:</strong> <?php echo htmlspecialchars($user_id); ?></p>
  <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>

  <p><strong>Membership:</strong>
    <?php
      function formatMembership($code) {
        $map = [
          "standard1" => "Standard Monthly Membership R299/Month",
          "standard2" => "Standard Yearly Membership R3 229/Year",
          "standard3" => "Standard Family Monthly Membership R999/Month",
          "standard4" => "Standard Family Yearly Membership R10 499/Year",
          "premium1" => "Premium Monthly Membership R699/Month",
          "premium2" => "Premium Yearly Membership R7 549/Year",
          "premium3" => "Premium Family Monthly Membership R2 399/Month",
          "premium4" => "Premium Family Yearly Membership R28 499/Year",
          "None"      => "None"
        ];
        return $map[$code] ?? htmlspecialchars($code);
      }

      echo formatMembership($membership);
    ?>
  </p>

  <?php if ($membership === "None"): ?>
    <a href="membership.html" class="join-now-btn">Join Now</a>
  <?php else: ?>
    <a href="membership.html" class="join-now-btn">Change Membership</a>
  <?php endif; ?>

  <form action="php/logout.php" method="post">
    <button type="submit" class="join-now-btn" style="margin-top: 15px;">Logout</button>
  </form>
</section>


<!-- Bookings -->
<section class="feature-block">
  <h3 class="h2">Your Class Bookings</h3>
  <?php if (count($bookings) > 0): ?>
    <div class="booking-grid">
      <?php foreach ($bookings as $booking): ?>
        <div class="booking-card">
          <p><strong>Class:</strong> <?php echo htmlspecialchars($booking['class_name']); ?></p>
          <p><strong>Location:</strong> <?php echo htmlspecialchars($booking['location']); ?></p>
          <p><strong>Date:</strong> <?php echo htmlspecialchars($booking['date']); ?></p>
          <p><strong>Time:</strong> <?php echo htmlspecialchars($booking['time']); ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <p>You have not booked any classes yet.</p>
  <?php endif; ?>
</section>

<!-- Footer -->
<footer class="footer">
  <a href="contact.html" class="footer-btn">Contact Us</a>
  <p>Email us at info@apexstrength.com or visit any of our locations nationwide.</p>
</footer>

</body>
</html>
