<?php
session_start();
require_once "db.php";

// Redirect if not logged in as client
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'client') {
    header("Location: login.php");
    exit;
}

$client_id = $_SESSION['user_id'];

// -------------------------------------------------------------
// 1) HANDLE PROFILE UPDATE
// -------------------------------------------------------------
$profile_message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_profile"])) {
    $new_name  = trim($_POST["name"]);
    $new_email = trim($_POST["email"]);
    $new_pass  = trim($_POST["password"]);

    // Validation
    if ($new_name === "" || $new_email === "") {
        $profile_message = "Please fill in all required fields.";
    } else {
        if ($new_pass === "") {
            // UPDATE without password change
            $stmt = $conn->prepare("UPDATE User SET name=?, email=? WHERE user_id=?");
            $stmt->bind_param("ssi", $new_name, $new_email, $client_id);
        } else {
            // UPDATE with password hashing
            $hashed = password_hash($new_pass, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("UPDATE User SET name=?, email=?, password=? WHERE user_id=?");
            $stmt->bind_param("sssi", $new_name, $new_email, $hashed, $client_id);
        }

        if ($stmt->execute()) {
            $_SESSION["name"] = $new_name;
            $profile_message = "Profile updated successfully.";
        } else {
            $profile_message = "Error updating profile.";
        }
    }
}

// -------------------------------------------------------------
// 2) GET CLIENT'S CURRENT PROFILE INFO
// -------------------------------------------------------------
$user_stmt = $conn->prepare("SELECT name, email FROM User WHERE user_id=?");
$user_stmt->bind_param("i", $client_id);
$user_stmt->execute();
$profile = $user_stmt->get_result()->fetch_assoc();

// -------------------------------------------------------------
// 3) FETCH MOST RECENT BOOKING STATUS (for bell popup)
// -------------------------------------------------------------
$notif_sql = "
    SELECT b.status, s.title, u.name AS professional_name
    FROM Booking b
    JOIN Service s ON b.service_id = s.service_id
    JOIN Professional p ON b.professional_id = p.professional_id
    JOIN User u ON p.professional_id = u.user_id
    WHERE b.client_id = ?
    ORDER BY b.time DESC
    LIMIT 1
";
$notif_stmt = $conn->prepare($notif_sql);
$notif_stmt->bind_param("i", $client_id);
$notif_stmt->execute();
$notif = $notif_stmt->get_result()->fetch_assoc();

// Build notification message
$notification_html = "";
if ($notif) {
    $st = $notif["status"];
    $svc = htmlspecialchars($notif["title"]);
    $pro = htmlspecialchars($notif["professional_name"]);

    if ($st === "confirmed") {
        $notification_html = "<div class='notif success'>🔔 Your booking for <b>$svc</b> with <b>$pro</b> has been <span>confirmed</span>.</div>";
    } elseif ($st === "cancelled") {
        $notification_html = "<div class='notif danger'>🔔 Your booking for <b>$svc</b> with <b>$pro</b> has been <span>cancelled</span>.</div>";
    }
}

// -------------------------------------------------------------
// 4) FETCH UPCOMING BOOKINGS
// -------------------------------------------------------------
$upcoming_sql = "
    SELECT b.booking_id, b.time, b.status,
           s.title, s.duration, s.price,
           u.name AS professional_name
    FROM Booking b
    JOIN Service s ON b.service_id = s.service_id
    JOIN Professional p ON b.professional_id = p.professional_id
    JOIN User u ON p.professional_id = u.user_id
    WHERE b.client_id = ?
      AND b.status = 'confirmed'
    ORDER BY b.time ASC
";
$up_stmt = $conn->prepare($upcoming_sql);
$up_stmt->bind_param("i", $client_id);
$up_stmt->execute();
$upcoming = $up_stmt->get_result();

// -------------------------------------------------------------
// 5) FETCH BOOKING HISTORY
// -------------------------------------------------------------
$history_sql = "
    SELECT b.booking_id, b.time, b.status,
           s.title, s.duration, s.price,
           u.name AS professional_name
    FROM Booking b
    JOIN Service s ON b.service_id = s.service_id
    JOIN Professional p ON b.professional_id = p.professional_id
    JOIN User u ON p.professional_id = u.user_id
    WHERE b.client_id = ?
      AND b.status IN ('completed','cancelled')
    ORDER BY b.time DESC
";
$his_stmt = $conn->prepare($history_sql);
$his_stmt->bind_param("i", $client_id);
$his_stmt->execute();
$history = $his_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en" class="has-solid-header">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Client Dashboard</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800;900&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="common.css">

<style>
/* keep original styles */
main.page { padding-top: 4rem; }
body { background: #f8f8f8; }

.wrap { max-width: 70rem; margin: 2rem auto 5rem; padding: 0 1rem; }

h1 { font-family: 'Playfair Display', serif; font-weight: 900; font-size: clamp(1.75rem, 3.5vw, 2.75rem); margin: 0 0 1.5rem; }

.tabs { display: flex; gap: 0.5rem; margin-bottom: 1.25rem; flex-wrap: wrap; }

.tabs a {
  text-decoration: none; color: var(--muted);
  padding: 0.625rem 0.875rem; border: 1px solid var(--line);
  border-radius: 999px; background: #fff;
  transition: all .2s ease; font-weight: 500;
}
.tabs a:hover { color: var(--text); transform: translateY(-1px); }
.tabs a.active { color: #fff; background: var(--accent); border-color: var(--accent); }

.panel {
  background: #fff; border: 1px solid var(--line);
  border-radius: 0.875rem; box-shadow: 0 2px 8px rgba(0,0,0,.06);
  padding: 1.25rem;
}

.list { display: grid; gap: 1rem; margin-top: 0.5rem; }

.item {
  display: grid; grid-template-columns: 1fr auto;
  align-items: center; gap: 1rem; padding: 1rem;
  border: 1px solid var(--line); border-radius: 0.5rem; background: #fff;
}

.meta { display: grid; gap: 0.25rem; }
.title { font-weight: 600; }
.muted { color: var(--muted); }
.price { font-weight: 700; letter-spacing: 0.3px; }

/* Notification */
.notif {
  padding: 1rem;
  border-radius: 0.75rem;
  margin-bottom: 1rem;
  font-weight: 600;
}
.notif.success { background:#e8f7ee; color:#0f7b47; }
.notif.danger  { background:#fdebec; color:#a11616; }

/* PROFILE PANEL */
.profile-panel {
  display:none;
  margin-top:1.5rem;
  margin-bottom:1.5rem;
}
.profile-form label {
  font-weight:600;
  margin-bottom:4px;
  display:block;
}
.profile-form input {
  width:100%;
  padding:0.75rem;
  border:1px solid var(--line);
  border-radius:8px;
  margin-bottom:1rem;
}
.save-btn {
  padding:0.75rem 1.5rem;
  background:var(--accent);
  border:none;
  border-radius:8px;
  color:white;
  font-weight:600;
  cursor:pointer;
}
.profile-msg {
  margin-bottom:1rem;
  padding:0.75rem;
  border-radius:8px;
  font-weight:600;
  background:#f7f7f7;
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
        <a href="#" id="profileToggle" class="nav-link">Profile</a>
        <a href="logout.php" class="nav-link">Log out</a>
      </nav>
    </div>
</header>

<main class="page">
    <div class="wrap">

      <!-- POP-UP NOTIFICATION -->
      <?= $notification_html ?>

      <h1>Hello, <?= htmlspecialchars($_SESSION['name']) ?></h1>

      <!-- PROFILE PANEL -->
      <div id="profilePanel" class="profile-panel panel">
        <h2>Edit Profile</h2>

        <?php if ($profile_message): ?>
          <div class="profile-msg"><?= htmlspecialchars($profile_message) ?></div>
        <?php endif; ?>

        <form method="POST" class="profile-form">
          <input type="hidden" name="update_profile" value="1">

          <label>Name</label>
          <input type="text" name="name" value="<?= htmlspecialchars($profile['name']) ?>" required>

          <label>Email</label>
          <input type="email" name="email" value="<?= htmlspecialchars($profile['email']) ?>" required>

          <label>New Password (leave empty to keep current)</label>
          <input type="password" name="password" placeholder="••••••••">

          <button class="save-btn" type="submit">Save Changes</button>
        </form>
      </div>

      <nav class="tabs">
        <a href="#upcoming" class="active">Upcoming Bookings</a>
        <a href="#history">Booking History</a>
      </nav>

      <div class="content">

        <!-- UPCOMING -->
        <section id="upcoming" class="section default-visible">
          <div class="panel list">

            <?php if ($upcoming->num_rows === 0): ?>
              <p class="muted" style="padding:1rem;">You have no upcoming bookings.</p>
            <?php else: ?>
              <?php while ($row = $upcoming->fetch_assoc()): ?>
                <div class="item">
                  <div class="meta">
                    <div class="title"><?= htmlspecialchars($row['title']) ?></div>
                    <div class="muted">
                      <?= htmlspecialchars($row['professional_name']) ?> · Riyadh ·
                      <?= $row['duration'] ?> min ·
                      <?= date("Y-m-d H:i", strtotime($row['time'])) ?>
                    </div>
                  </div>
                  <div class="price">SAR <?= number_format($row['price'], 2) ?></div>
                </div>
              <?php endwhile; ?>
            <?php endif; ?>

          </div>
        </section>

        <!-- HISTORY -->
        <section id="history" class="section" style="display:none;">
          <div class="panel list">

            <?php if ($history->num_rows === 0): ?>
              <p class="muted" style="padding:1rem;">You have no past bookings.</p>
            <?php else: ?>
              <?php while ($row = $history->fetch_assoc()): ?>
                <div class="item">
                  <div class="meta">
                    <div class="title"><?= htmlspecialchars($row['title']) ?></div>
                    <div class="muted">
                      <?= htmlspecialchars($row['professional_name']) ?> · Riyadh · 
                      <?= $row['duration'] ?> min · 
                      <?= date("Y-m-d H:i", strtotime($row['time'])) ?>
                    </div>
                  </div>
                  <div class="price">SAR <?= number_format($row['price'], 2) ?></div>
                </div>
              <?php endwhile; ?>
            <?php endif; ?>

          </div>
        </section>

      </div>
    </div>
</main>

<footer class="site-footer">
    <div class="container footer-inner">
      <p>© 2025 Glammd. All rights reserved.</p>
      <div class="footer-links">
        <a href="#">Privacy Policy</a>
        <a href="#">Terms of Service</a>
        <a href="#">Contact</a>
      </div>
    </div>
</footer>

<script>
// Tabs logic
const tabs = document.querySelectorAll('.tabs a');
const sections = document.querySelectorAll('.section');

tabs.forEach(tab => {
  tab.addEventListener('click', e => {
    e.preventDefault();
    tabs.forEach(t => t.classList.remove('active'));
    sections.forEach(s => s.style.display = 'none');

    const target = document.querySelector(tab.getAttribute('href'));
    tab.classList.add('active');
    target.style.display = 'block';
  });
});

// Profile panel toggle
const toggleBtn = document.getElementById("profileToggle");
const panel = document.getElementById("profilePanel");

toggleBtn.addEventListener("click", (e) => {
    e.preventDefault();
    panel.style.display = panel.style.display === "block" ? "none" : "block";
});
</script>

</body>
</html>
