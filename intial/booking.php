<?php
session_start();
require_once "db.php";

if (!isset($_GET['service'])) {
    die("Service not specified.");
}

$service_id = intval($_GET['service']);

// Fetch service + professional details
$query = "
    SELECT 
        s.service_id, s.title, s.description, s.duration, s.price, s.category,
        p.professional_id, p.img AS pro_img, p.bio,
        u.name AS professional_name
    FROM Service s
    JOIN Professional p ON s.professional_id = p.professional_id
    JOIN User u ON p.professional_id = u.user_id
    WHERE s.service_id = ?
";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $service_id);
mysqli_stmt_execute($stmt);
$service = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$service) {
    die("Service not found.");
}

// Handle booking submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        die("You must be logged in to book.");
    }

    $client_id = $_SESSION['user_id'];
    $professional_id = $service['professional_id'];
    $service_id = $service['service_id'];

    // Dynamic notes
    $notes = "";

    if ($service['category'] === "Makeup") {
        $tone = $_POST['tone'] ?? "";
        $palette = $_POST['palette'] ?? "";
        $skin = $_POST['skin'] ?? "";
        $notes = "Tone: $tone | Palette: $palette | Skin: $skin";
    }

    if ($service['category'] === "Hair") {
        $hair_type = $_POST['hair_type'] ?? "";
        $style_pref = $_POST['style_pref'] ?? "";
        $notes = "Hair Type: $hair_type | Style: $style_pref";
    }

    if ($service['category'] === "Nails") {
        $shape = $_POST['shape'] ?? "";
        $finish = $_POST['finish'] ?? "";
        $nail_art = $_POST['nail_art'] ?? "No";
        $notes = "Shape: $shape | Finish: $finish | Nail Art: $nail_art";
    }

    if ($service['category'] === "Skincare") {
        $skin_type = $_POST['skin_type'] ?? "";
        $notes = "Skin Type: $skin_type";
    }

    if ($service['category'] === "Bodycare") {
        $pressure = $_POST['pressure'] ?? "";
        $extra = $_POST['extra'] ?? "";
        $notes = "Pressure: $pressure | Extra: $extra";
    }

    $preferred_date = $_POST['date'] ?? "";
    $preferred_time = $_POST['time'] ?? "";

    if (!$preferred_date || !$preferred_time) {
        die("Must select date/time.");
    }

    $dateTime = $preferred_date . " " . $preferred_time;

    // Insert booking
    $insert = "
        INSERT INTO Booking (client_id, professional_id, service_id, time, client_notes, status)
        VALUES (?, ?, ?, ?, ?, 'confirmed')
    ";

    $stmt = mysqli_prepare($conn, $insert);
    mysqli_stmt_bind_param(
        $stmt,
        "iiiss",
        $client_id,
        $professional_id,
        $service_id,
        $dateTime,
        $notes
    );

    if (mysqli_stmt_execute($stmt)) {
        header("Location: booking.php?service=$service_id&success=1");
        exit;
    } else {
        die("Booking failed.");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Book Service – Glammd</title>

  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800;900&family=Inter:wght@400;500;600&family=Tartuffo:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="common.css">

<style>
/* ——— HERO BACKGROUND ——— */
.page {
  position: relative;
  min-height: 100vh;
  background-image:
    linear-gradient(rgba(255,255,255,0.20), rgba(255,255,255,0.25)),
    url("img/download.jpeg");
  background-size: cover;
  background-position: center 20%;
  background-attachment: fixed;
}

.page::before {
  content: "";
  position: absolute;
  inset: 0;
  background: linear-gradient(
    to bottom,
    rgba(255,255,255,0) 0%,
    rgba(255,255,255,0) 60%,
    rgba(255,255,255,0.4) 85%,
    rgba(255,255,255,0.95) 100%
  );
  pointer-events: none;
}

body {
  background: #f8f8f8;
  margin: 0;
  font-family: 'Inter', sans-serif;
}

/* ——— BOOKING CARD ——— */
.booking-container {
  background: #fff;
  border-radius: 1rem;
  padding: 2.5rem;
  box-shadow: 0 2px 12px rgba(0,0,0,0.09);
  max-width: 600px;
  margin: 0 auto 3rem;
  position: relative;
  z-index: 2;
  border: 1px solid rgba(0,0,0,0.05);
}

.booking-header {
  display: flex;
  align-items: center;
  gap: 1.25rem;
  margin-bottom: 2rem;
  padding-bottom: 1.25rem;
  border-bottom: 1px solid rgba(0,0,0,0.1);
}

.booking-header img {
  width: 5rem;
  height: 5rem;
  border-radius: 50%;
  object-fit: cover;
  border: 3px solid white;
  box-shadow: 0 4px 15px rgba(0,0,0,0.12);
}

.info h2 {
  margin: 0;
  font-size: 1.375rem;
  color: #333;
  font-weight: 700;
  font-family: 'Playfair Display', serif;
}

.info p {
  margin: 0.25rem 0 0;
  color: #666;
  font-size: 0.875rem;
}

/* ——— SERVICE DETAILS ——— */
.service-details {
  margin: 1.5rem 0;
  padding: 1.25rem;
  background: #fafafa;
  border-radius: 0.75rem;
  border: 1px solid rgba(0,0,0,0.05);
}

.service-details h3 {
  margin: 0 0 0.625rem;
  font-size: 1.25rem;
  color: #333;
  font-family: 'Playfair Display', serif;
}

.service-details p {
  margin: 0.3125rem 0;
  color: #666;
}

/* ——— FORM ——— */
.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  font-weight: 600;
  margin-bottom: 0.5rem;
  display: block;
  color: #333;
}

.form-input, select {
  width: 100%;
  padding: 0.75rem 1rem;
  border-radius: 0.5rem;
  border: 1px solid #ddd;
  outline: none;
}

/* Tag-style options */
.tags {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.tag {
  padding: 0.5rem 1rem;
  background: #f0f0f0;
  border-radius: 1.25rem;
  cursor: pointer;
  font-size: 0.875rem;
  transition: 0.2s;
}

.tag input {
  display: none;
}

.tag:has(input:checked) {
  background: #000;
  color: #fff;
}

/* ——— BUTTON ——— */
.submit-btn {
  width: 100%;
  padding: 0.875rem;
  background: #000;
  color: white;
  border-radius: 0.5rem;
  border: none;
  cursor: pointer;
  font-weight: 600;
  margin-top: 1rem;
  transition: 0.2s;
}

.submit-btn:hover {
  opacity: 0.9;
}

/* ——— POPUP ——— */
.popup {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.7);
  justify-content: center;
  align-items: center;
  z-index: 2000;
  backdrop-filter: blur(4px);
}

.popup.active {
  display: flex;
}

.popup-content {
  background: white;
  padding: 2.5rem;
  border-radius: 1rem;
  text-align: center;
  width: 90%;
  max-width: 400px;
}

.popup h2 {
  font-family: 'Playfair Display', serif;
  margin-bottom: 1rem;
}
</style>
</head>

<body class="has-solid-header">

<header id="siteHeader" class="site-header">
    <div class="container header-inner">
      <a class="brand" href="index.php">Glammd</a>
      <nav class="nav">
        <a href="MarketPlace.php" class="nav-link">Market</a>
        <a href="favorites.php" class="nav-link">Favorites</a>
        <a href="index.php" class="nav-link">Log out</a>
      </nav>
    </div>
</header>

<main class="page">
    <div class="container">

      <div class="breadcrumbs" style="margin-top:2rem; color:#666;">
        <a href="clientdashboard.php">Client Dashboard</a> ›
        <a href="MarketPlace.php">Market</a> ›
        <a href="services.php?professional_id=<?= $service['professional_id'] ?>">
            <?= htmlspecialchars($service['professional_name']) ?>
        </a> ›
        <strong>Book Service</strong>
      </div>

      <div class="booking-container">

        <!-- HEADER -->
        <div class="booking-header">
          <img src="img/<?= htmlspecialchars($service['pro_img']) ?>" alt="" loading="lazy">
          <div class="info">
            <h2><?= htmlspecialchars($service['professional_name']) ?></h2>
            <p><?= htmlspecialchars($service['bio']) ?></p>
          </div>
        </div>

        <!-- SERVICE INFO -->
        <div class="service-details">
          <h3><?= htmlspecialchars($service['title']) ?></h3>
          <p><?= htmlspecialchars($service['description']) ?></p>
          <p><strong>Duration:</strong> <?= $service['duration'] ?> min</p>
          <p><strong>Price:</strong> SAR <?= number_format($service['price'], 2) ?></p>
        </div>

        <form method="POST">

        <!-- CATEGORY SPECIFIC FIELDS -->
        <?php if ($service['category'] === "Makeup"): ?>
            <div class="form-group">
                <label>Makeup Tone</label>
                <div class="tags">
                    <label class="tag"><input type="radio" name="tone" value="Natural"> Natural</label>
                    <label class="tag"><input type="radio" name="tone" value="Soft Glam"> Soft Glam</label>
                    <label class="tag"><input type="radio" name="tone" value="Bold"> Bold</label>
                </div>
            </div>

            <div class="form-group">
                <label>Color Palette</label>
                <select name="palette" class="form-input">
                    <option value="Bronze">Bronze</option>
                    <option value="Rose">Rose</option>
                    <option value="Lavender">Lavender</option>
                    <option value="Neutral">Neutral</option>
                </select>
            </div>

            <div class="form-group">
                <label>Skin Type</label>
                <select name="skin" class="form-input">
                    <option>Normal</option>
                    <option>Oily</option>
                    <option>Dry</option>
                    <option>Combination</option>
                </select>
            </div>
        <?php endif; ?>

        <?php if ($service['category'] === "Hair"): ?>
            <div class="form-group">
                <label>Hair Type</label>
                <select name="hair_type" class="form-input">
                    <option>Straight</option>
                    <option>Wavy</option>
                    <option>Curly</option>
                    <option>Coily</option>
                </select>
            </div>

            <div class="form-group">
                <label>Preferences</label>
                <input type="text" name="style_pref" class="form-input" placeholder="(Optional) Describe your preference">
            </div>
        <?php endif; ?>

        <?php if ($service['category'] === "Nails"): ?>
            <div class="form-group">
                <label>Nail Shape</label>
                <select name="shape" class="form-input">
                    <option>Almond</option>
                    <option>Coffin</option>
                    <option>Square</option>
                    <option>Round</option>
                </select>
            </div>

            <div class="form-group">
                <label>Finish</label>
                <select name="finish" class="form-input">
                    <option>Matte</option>
                    <option>Glossy</option>
                </select>
            </div>

            <div class="form-group">
                <label>Nail Art?</label>
                <select name="nail_art" class="form-input">
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                </select>
            </div>
        <?php endif; ?>

        <?php if ($service['category'] === "Skincare"): ?>
            <div class="form-group">
                <label>Skin Type</label>
                <select name="skin_type" class="form-input">
                    <option>Normal</option>
                    <option>Dry</option>
                    <option>Oily</option>
                    <option>Combination</option>
                </select>
            </div>
        <?php endif; ?>

        <?php if ($service['category'] === "Bodycare"): ?>
            <div class="form-group">
                <label>Pressure Level</label>
                <select name="pressure" class="form-input">
                    <option>Light</option>
                    <option>Medium</option>
                    <option>Deep</option>
                </select>
            </div>

            <div class="form-group">
                <label>Extras</label>
                <select name="extra" class="form-input">
                    <option value="">None</option>
                    <option value="Aromatherapy">Aromatherapy</option>
                    <option value="Hot stones">Hot stones</option>
                </select>
            </div>
        <?php endif; ?>

        <!-- DATE & TIME -->
        <div class="form-group">
            <label>Select Date</label>
            <input type="date" name="date" required class="form-input">
        </div>

        <div class="form-group">
            <label>Select Time</label>
            <input type="time" name="time" required class="form-input">
        </div>

        <button class="submit-btn" type="submit">Confirm Booking</button>

        </form>

      </div>
    </div>
</main>

<!-- SUCCESS POPUP -->
<div class="popup" id="popup">
  <div class="popup-content">
    <div style="font-size:3rem; margin-bottom:1rem; color:#4CAF50;">✓</div>
    <h2>Your appointment has been confirmed.</h2>
    <p style="color:#666;">Redirecting to your dashboard...</p>
  </div>
</div>

<script>
// Show popup on success
<?php if (isset($_GET['success'])): ?>
  const popup = document.getElementById('popup');
  popup.classList.add('active');
  setTimeout(() => {
    window.location.href = 'clientdashboard.php';
  }, 2500);
<?php endif; ?>
</script>

</body>
</html>
