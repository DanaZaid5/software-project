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

    // Dynamic fields
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
        $nail_art = $_POST['nail_art'] ?? "no";
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
        header("Location: clientdashboard.php?booked=1");
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
/* YOUR CSS — UNTOUCHED */
<?php /* I am NOT modifying your CSS. It stays exactly as you sent it. */ ?>
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

      <div class="breadcrumbs">
        <a href="clientdashboard.php">Client Dashboard</a>
        <span>›</span>
        <a href="MarketPlace.php">Market</a>
        <span>›</span>
        <a href="services.php?professional_id=<?= $service['professional_id'] ?>">
            <?= htmlspecialchars($service['professional_name']) ?>
        </a>
        <span>›</span>
        <strong>Book Service</strong>
      </div>

      <div class="booking-container">

        <!-- HEADER -->
        <div class="booking-header">
          <img src="img/<?= htmlspecialchars($service['pro_img']) ?>" alt="<?= $service['professional_name'] ?>">
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

</body>
</html>
