<?php
// professionaldashboard.php
ob_start();
if (session_status() === PHP_SESSION_NONE) session_start();

require_once 'db.php';

// Auth guard
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'professional') {
    header('Location: clientdashboard.php');
    exit;
}

$pro_id = (int) $_SESSION['user_id'];

// Fetch professional user/profile
$stmt = $conn->prepare("
    SELECT U.name, P.bio, P.img
    FROM User U
    LEFT JOIN Professional P 
ON U.user_id = P.professional_id


  WHERE U.user_id = ?
");
$stmt->bind_param("i", $pro_id);
$stmt->execute();
$res = $stmt->get_result();
$pro = $res->fetch_assoc();

$proName = $pro['name'] ?? 'Professional';
$proBio  = $pro['bio'] ?? 'No bio added yet.';
$proImg  = !empty($pro['img']) ? 'img/' . $pro['img'] : 'img/pro1.jpg';
// Fetch average rating
$ratingStmt = $conn->prepare("
    SELECT ROUND(AVG(rating), 1) AS avg_rating, COUNT(*) AS total_reviews
    FROM Review
    WHERE professional_id = ?
");
$ratingStmt->bind_param("i", $pro_id);
$ratingStmt->execute();
$ratingData = $ratingStmt->get_result()->fetch_assoc();

$avgRating = $ratingData['avg_rating'] ?? 0;
$totalReviews = $ratingData['total_reviews'] ?? 0;




// Fetch services for this professional
$servicesStmt = $conn->prepare("SELECT service_id, category, title, description, duration, price, tags FROM Service WHERE professional_id = ? ORDER BY title");
$servicesStmt->bind_param("i", $pro_id);
$servicesStmt->execute();
$servicesRes = $servicesStmt->get_result();

// Fetch pending booking requests
$pendingStmt = $conn->prepare("
    SELECT R.request_id, R.preferred_date, R.preferred_time, U.name AS client_name, S.title AS service_title
    FROM BookingRequest R
    JOIN User U ON R.client_id = U.user_id
    JOIN Service S ON R.service_id = S.service_id
    WHERE R.professional_id = ? AND R.status = 'pending'
    ORDER BY R.preferred_date, R.preferred_time
");
$pendingStmt->bind_param("i", $pro_id);
$pendingStmt->execute();
$pendingRes = $pendingStmt->get_result();

// Fetch confirmed bookings
$confirmedStmt = $conn->prepare("
    SELECT B.booking_id, B.time, U.name AS client_name, S.title AS service_title
    FROM Booking B
    JOIN User U ON B.client_id = U.user_id
    JOIN Service S ON B.service_id = S.service_id
    WHERE B.professional_id = ? AND B.status = 'confirmed'
    ORDER BY B.time ASC
");
$confirmedStmt->bind_param("i", $pro_id);
$confirmedStmt->execute();
$confirmedRes = $confirmedStmt->get_result();

// Fetch reviews
$reviewStmt = $conn->prepare("
    SELECT R.review_id, R.rating, R.comment, R.review_date, U.name AS client_name, S.title AS service_title
    FROM Review R
    JOIN User U ON R.client_id = U.user_id
    JOIN Service S ON R.service_id = S.service_id
    WHERE R.professional_id = ?
    ORDER BY R.review_date DESC
    LIMIT 20
");
$reviewStmt->bind_param("i", $pro_id);
$reviewStmt->execute();
$reviewRes = $reviewStmt->get_result();

?>
<!DOCTYPE html>
<html lang="en" class="has-solid-header">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>My Dashboard</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800;900&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

<style>
/* ---------- KEEP YOUR ORIGINAL CSS (unchanged) ---------- */
  *,*::before,*::after{ box-sizing:border-box }
  :root{
    --bg:#f8f8f8; --panel:#ffffff; --ink:#111; --muted:#6f6f6f; --line:#eaeaea;
    --accent:#111; --success:#0f7b47; --danger:#a11616;
    --r-sm:8px; --r-md:14px; --sh-1:0 1px 2px rgba(0,0,0,.04); --sh-2:0 8px 24px rgba(0,0,0,.06);
    --text:#0f0f12; --accent-text:#fff; --container:1200px;
  }
  html,body{height:100%}
  body{
    margin:0; background:var(--bg); color:var(--ink);
    font:14px/1.5 Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    letter-spacing:.2px;
  }
  .container{max-width:var(--container); margin-inline:auto; padding-inline:20px; width:100%}
  .site-header{
    position:fixed; top:0; left:0; right:0; z-index:50; height:64px; display:block;
    transition: background .25s ease, border-color .25s ease, backdrop-filter .25s ease, -webkit-backdrop-filter .25s ease, box-shadow .25s ease;
    backdrop-filter:saturate(150%) blur(8px); -webkit-backdrop-filter:saturate(150%) blur(8px);
    background:color-mix(in srgb, white 75%, transparent);
    border-bottom:1px solid rgba(0,0,0,.06);
    box-shadow:0 1px 10px rgba(0,0,0,.04);
  }
  .header-inner{ height:64px; display:flex; align-items:center; justify-content:space-between; }
  .brand{ text-decoration:none; color:var(--text); font-weight:700; font-size:22px; letter-spacing:.2px }
  .nav{ display:flex; gap:10px }
  .nav-link{ text-decoration:none; color:var(--text); padding:10px 14px; border-radius:999px; }
  main.page{ padding-top:64px }
  .wrap{ max-width:1120px; margin:28px auto 80px; padding:0 16px; }
  .tabs{ display:flex; gap:8px; margin-bottom:18px; flex-wrap:wrap; }
  .tabs a{ text-decoration:none; color:var(--muted); padding:10px 14px; border:1px solid var(--line); border-radius:999px; background:#fff; transition:all .2s ease; }
  .tabs a:hover{ color:var(--ink); transform:translateY(-1px) }
  .tabs a[href="#services"]{ color:#fff; background:var(--accent); border-color:var(--accent); }
  :target ~ .tabs a[href="#services"], :target ~ .tabs a[href="#offers"], :target ~ .tabs a[href="#reviews"]{ color:var(--muted); background:#fff; border-color:var(--line); }
  #offers:target ~ .tabs a[href="#offers"], #reviews:target ~ .tabs a[href="#reviews"]{ color:#fff; background:var(--accent); border-color:var(--accent); }
  .section{ display:none; }
  .section.default-visible{ display:block; }
  #offers:target ~ .content #services, #reviews:target ~ .content #services{ display:none; }
  #offers:target ~ .content #offers{ display:block; }
  #reviews:target ~ .content #reviews{ display:block; }
  .panel{ background:var(--panel); border:1px solid var(--line); border-radius:var(--r-md); box-shadow:var(--sh-2); padding:20px; }
  .panel h2{ font-size:18px; margin:0 0 18px 0; letter-spacing:.3px; }
  .muted{ color:var(--muted) }
  .grid{ display:grid; gap:16px; }
  @media (min-width:860px){ .grid-2{ grid-template-columns:1.2fr 1fr; } .grid-3{ grid-template-columns:repeat(3,1fr); } }
  label{ font-size:12px; text-transform:uppercase; letter-spacing:1px; color:var(--muted) }
  .form-row{ display:grid; gap:12px; margin-bottom:14px; }
  .form-row.inline{ grid-template-columns:minmax(0,1fr) minmax(0,1fr); }
  input[type="text"], input[type="number"], textarea, select, input[type="datetime-local"]{ width:100%; padding:12px 14px; border:1px solid var(--line); border-radius:var(--r-sm); background:#fff; outline:none; min-width:0; }
  textarea{ min-height:100px; resize:vertical }
  .btn{ appearance:none; border:1px solid var(--ink); background:var(--ink); color:#fff; padding:10px 14px; border-radius:999px; cursor:pointer; font-weight:600; transition:transform .1s ease, opacity .2s ease; }
  .btn:active{ transform:translateY(1px) }
  .btn.ghost{ background:transparent; color:var(--ink); border-color:var(--line); }
  .btn.success{ background:var(--success); border-color:var(--success) }
  .btn.danger{ background:var(--danger); border-color:var(--danger) }
  .list{ display:grid; gap:12px; margin-top:6px; }
  .item{ display:grid; grid-template-columns:1fr auto; gap:16px; align-items:center; padding:14px; border:1px solid var(--line); border-radius:var(--r-sm); background:#fff; }
  .meta{ display:grid; gap:4px; }
  .title{ font-weight:600; }
  .price{ font-weight:600; letter-spacing:.3px }
  .actions{ display:flex; align-items:center; gap:8px; }
  .table{ width:100%; border-collapse:collapse; font-size:14px; }
  .table th, .table td{ text-align:left; padding:12px 10px; border-bottom:1px solid var(--line); vertical-align:middle; }
  .table th{ font-size:12px; text-transform:uppercase; letter-spacing:1px; color:var(--muted) }
  .chip{ display:inline-flex; align-items:center; gap:8px; border:1px solid var(--line); padding:6px 10px; border-radius:999px; font-size:12px; color:var(--muted); background:#fff; }
  .reviews{ display:grid; gap:16px; }
  @media (min-width:860px){ .reviews{ grid-template-columns:repeat(3,1fr); } }
  .review{ border:1px solid var(--line); border-radius:var(--r-sm); padding:14px; background:#fff; display:grid; gap:8px; }
  .review-head{ display:flex; align-items:center; justify-content:space-between; gap:8px; }
  .reviewer{ display:grid; gap:2px; }
  .reviewer strong{ font-weight:600 }
  .service-for{ font-size:12px; color:var(--muted) }
  .stars{ font-size:13px; letter-spacing:2px; color:#111; }
  .review p{ margin:0; color:var(--muted); }
  .tags{ display:flex; flex-wrap:wrap; gap:8px; }
  .tag{ position:relative; display:inline-flex; align-items:center; border:1px solid var(--line); border-radius:999px; background:#fff; padding:8px 12px; font-size:13px; color:var(--ink); cursor:pointer; transition:transform .1s ease; }
  .tag input{ position:absolute; opacity:0; pointer-events:none; }
  .tag:has(input:checked){ border-color:var(--ink); background:var(--ink); color:#fff; }
  .tag:active{ transform:translateY(1px) }
  .foot-hint{ display:flex; justify-content:center; margin-top:20px; color:var(--muted); font-size:12px; }
/* ---------- end CSS ---------- */
</style>
</head>
<body class="has-solid-header">

  <header id="siteHeader" class="site-header">
    <div class="container header-inner">
      <a class="brand" href="index.php">Glammd</a>
      <nav class="nav">
        <a href="logout.php" class="nav-link">Log out</a>
      </nav>
    </div>
  </header>

  <main class="page">
    <div class="wrap">
       <!-- PROFESSIONAL PROFILE HEADER -->
<div class="panel" style="margin-bottom:20px; display:flex; gap:20px; align-items:center;">
    
    <!-- Profile photo -->
    <div style="width:90px; height:90px; border-radius:50%; overflow:hidden; flex-shrink:0;">
        <img src="<?= htmlspecialchars($proImg) ?>" 
             alt="Profile Photo" 
             style="width:100%; height:100%; object-fit:cover;">
    </div>


      <!-- Professional profile card + Edit button -->
      <section class="professional-profile">
        <div class="professional-photo">
          <img src="img/pro1.jpg" alt="Sarah M." loading="lazy">

    <!-- Profile text -->
    <div style="flex:1;">
        <h2 style="margin:0; font-size:22px; font-weight:700;">
            <?= htmlspecialchars($proName) ?>
        </h2>

        <p class="muted" style="margin:5px 0 10px;">
            <?= nl2br(htmlspecialchars($proBio)) ?>
        </p>

        <!-- ⭐ Rating row -->
        <div style="display:flex; align-items:center; gap:10px; margin-top:8px;">
            <span style="font-size:18px;">
                ★ <?= $avgRating ? number_format($avgRating, 1) : "No rating" ?>
            </span>

            <?php if ($totalReviews > 0): ?>
                <span class="muted">(<?= $totalReviews ?> reviews)</span>
            <?php else: ?>
                <span class="muted">(no reviews yet)</span>
            <?php endif; ?>

        </div>
    </div>
</div>



      <!-- Services / Offers / Reviews anchors -->
      <div id="services"></div><div id="offers"></div><div id="reviews"></div>

      <nav class="tabs">
        <a href="#services">Services</a>
        <a href="#offers">Offers</a>
        <a href="#reviews">Reviews</a>
      </nav>

      <div class="content">
        <!-- SERVICES -->
        <section id="services" class="section default-visible">
          <div class="grid grid-2">
            <!-- Add Service panel (POST to service_add.php) -->
            <div class="panel">
              <h2>Add a service</h2>

              <form method="POST" action="service_add.php">
                <div class="form-row inline">
                  <div>
                    <label>Category</label>
                    <select name="category" required>
                      <option value="Hair">Hair</option>
                      <option value="Makeup">Makeup</option>
                      <option value="Skincare">Skincare</option>
                      <option value="Bodycare">Bodycare</option>
                      <option value="Nails">Nails</option>
                    </select>
                  </div>
                  <div>
                    <label>Display Title</label>
                    <input type="text" name="title" placeholder="e.g., ‘Signature Glow’" required />
                  </div>
                </div>

                <div class="form-row inline">
                  <div>
                    <label>Price (SAR)</label>
                    <input type="number" name="price" min="0" step="0.01" placeholder="1200" required />
                  </div>
                  <div>
                    <label>Duration (min)</label>
                   <input type="number" name="duration" min="1" step="1" required>

                  </div>
                </div>

                <div class="form-row">
                  <div>
                    <label>Description</label>
                    <textarea name="description" placeholder="Describe what’s included, prep notes, and aftercare."></textarea>
                  </div>
                </div>

                <div class="form-row">
                  <label>Tags (comma separated)</label>
                  <input type="text" name="tags" placeholder="e.g., sensitive skin, bridal" />
                </div>

                <button class="btn" type="submit">Add Service</button>
              </form>
            </div>

            <!-- List Services -->
            <div class="panel">
              <h2>Your services</h2>
              <div class="list">
                <?php while ($row = $servicesRes->fetch_assoc()): ?>
                  <div class="item">
                    <div class="meta">
                      <div class="title"><?= htmlspecialchars($row['title']); ?></div>
                      <div class="muted">Category <strong><?= htmlspecialchars($row['category']); ?></strong> · Duration <strong><?= (int)$row['duration']; ?> min</strong></div>
                      <div class="muted"><?= htmlspecialchars($row['description']); ?></div>
                      <?php if (!empty($row['tags'])): ?>
                        <div class="muted">Tags: <?= htmlspecialchars($row['tags']); ?></div>
                      <?php endif; ?>
                    </div>

                    <div class="actions">
                      <span class="price">SAR <?= number_format((float)$row['price'], 2); ?></span>

                      <!-- Edit button triggers JS modal -->
                      <button class="btn ghost btn-xs" type="button"
                        data-service='<?= json_encode([
                          'service_id' => $row['service_id'],
                          'category' => $row['category'],
                          'title' => $row['title'],
                          'description' => $row['description'],
                          'duration' => $row['duration'],
                          'price' => $row['price'],
                          'tags' => $row['tags'],
                        ], JSON_HEX_APOS | JSON_HEX_QUOT); ?>'
                        onclick="openEditModal(this)">Edit</button>

                      <!-- Delete form -->
                      <form method="POST" action="service_delete.php" style="display:inline;">
                        <input type="hidden" name="service_id" value="<?= (int)$row['service_id']; ?>">
                        <button class="btn danger" type="submit" onclick="return confirm('Delete this service?');">Delete</button>
                      </form>
                    </div>
                  </div>
                <?php endwhile; ?>
              </div>
              <div class="foot-hint">Manage your service offerings.</div>
            </div>
          </div>
        </section>

        <!-- OFFERS (pending booking requests) -->
        <section id="offers" class="section">
          <div class="panel">
            <h2>Incoming offers</h2>
            <table class="table">
              <thead>
                <tr>
                  <th>Client</th>
                  <th>Service</th>
                  <th>Requested Date</th>
                  <th>Requested Time</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($pendingRes->num_rows === 0): ?>
                  <tr><td colspan="5" class="muted">No pending requests</td></tr>
                <?php else: while ($p = $pendingRes->fetch_assoc()): ?>
                  <tr>
                    <td><?= htmlspecialchars($p['client_name']); ?></td>
                    <td><span class="chip"><?= htmlspecialchars($p['service_title']); ?></span></td>
                    <td><?= htmlspecialchars($p['preferred_date']); ?></td>
                    <td><?= htmlspecialchars($p['preferred_time']); ?></td>
                    <td class="actions">
                      <form method="POST" action="request_update.php" style="display:inline;">
                        <input type="hidden" name="request_id" value="<?= (int)$p['request_id']; ?>">
                        <input type="hidden" name="status" value="accepted">
                        <button class="btn success" type="submit">Accept</button>
                      </form>

                      <form method="POST" action="request_update.php" style="display:inline;">
                        <input type="hidden" name="request_id" value="<?= (int)$p['request_id']; ?>">
                        <input type="hidden" name="status" value="rejected">
                        <button class="btn ghost" type="submit">Reject</button>
                      </form>
                    </td>
                  </tr>
                <?php endwhile; endif; ?>
              </tbody>
            </table>
          </div>

          <!-- Confirmed bookings -->
          <div class="panel" style="margin-top:16px;">
            <h2>Confirmed bookings</h2>
            <table class="table">
              <thead>
                <tr>
                  <th>Client</th>
                  <th>Service</th>
                  <th>Date/Time</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($confirmedRes->num_rows === 0): ?>
                  <tr><td colspan="3" class="muted">No confirmed bookings</td></tr>
                <?php else: while ($c = $confirmedRes->fetch_assoc()): ?>
                  <tr>
                    <td><?= htmlspecialchars($c['client_name']); ?></td>
                    <td><?= htmlspecialchars($c['service_title']); ?></td>
                    <td><?= htmlspecialchars($c['time']); ?></td>
                  </tr>
                <?php endwhile; endif; ?>
              </tbody>
            </table>
          </div>
        </section>

        <!-- REVIEWS -->
        <section id="reviews" class="section">
          <div class="panel">
            <h2>Reviews</h2>
            <div class="reviews">
              <?php if ($reviewRes->num_rows === 0): ?>
                <div class="muted">No reviews yet.</div>
              <?php else: while ($r = $reviewRes->fetch_assoc()): ?>
                <article class="review">
                  <div class="review-head">
                    <div class="reviewer">
                      <strong><?= htmlspecialchars($r['client_name']); ?></strong>
                      <span class="service-for">for <?= htmlspecialchars($r['service_title']); ?></span>
                    </div>
                    <div class="stars"><?= str_repeat('★', (int)$r['rating']) . str_repeat('☆', 5 - (int)$r['rating']); ?></div>
                  </div>
                  <p><?= nl2br(htmlspecialchars($r['comment'])); ?></p>
                </article>
              <?php endwhile; endif; ?>
            </div>
          </div>
        </section>

      </div>
    </div>
  </main>

  <!-- Edit service modal (hidden) -->
  <div id="editModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.4); align-items:center; justify-content:center; z-index:9999;">
    <div style="width:720px; max-width:95%; background:white; border-radius:12px; padding:20px;">
      <h3>Edit service</h3>
      <form id="editForm" method="POST" action="service_edit.php">
        <input type="hidden" name="service_id" id="edit_service_id">
        <div class="form-row inline">
          <div>
            <label>Category</label>
            <select name="category" id="edit_category" required>
              <option value="Hair">Hair</option>
              <option value="Makeup">Makeup</option>
              <option value="Skincare">Skincare</option>
              <option value="Bodycare">Bodycare</option>
              <option value="Nails">Nails</option>
            </select>
          </div>
          <div>
            <label>Display Title</label>
            <input type="text" name="title" id="edit_title" required />
          </div>
        </div>

        <div class="form-row inline">
          <div>
            <label>Price (SAR)</label>
            <input type="number" name="price" id="edit_price" min="0" step="0.01" required />
          </div>
          <div>
            <label>Duration (min)</label>
            <input type="number" name="duration" id="edit_duration" min="15" step="15" required />
          </div>
        </div>

        <div class="form-row">
          <label>Description</label>
          <textarea name="description" id="edit_description"></textarea>
        </div>

        <div class="form-row">
          <label>Tags (comma separated)</label>
          <input type="text" name="tags" id="edit_tags" />
        </div>

        <div style="display:flex; gap:10px; justify-content:flex-end;">
          <button type="button" class="btn ghost" onclick="closeEditModal()">Cancel</button>
          <button type="submit" class="btn">Save</button>
        </div>
      </form>
    </div>
  </div>

<script>
  function openEditModal(btn) {
    const dataStr = btn.getAttribute('data-service');
    try {
      const data = JSON.parse(dataStr);
      document.getElementById('edit_service_id').value = data.service_id;
      document.getElementById('edit_category').value = data.category || '';
      document.getElementById('edit_title').value = data.title || '';
      document.getElementById('edit_description').value = data.description || '';
      document.getElementById('edit_duration').value = data.duration || '';
      document.getElementById('edit_price').value = data.price || '';
      document.getElementById('edit_tags').value = data.tags || '';
      document.getElementById('editModal').style.display = 'flex';
    } catch (e) {
      alert('Could not open edit modal');
      console.error(e);
    }
  }
  function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
  }

  // Close modal on ESC
  window.addEventListener('keydown', (ev) => {
    if (ev.key === 'Escape') closeEditModal();
  });
</script>

</body>
</html>
<?php
// flush at the end
ob_end_flush();
?>
