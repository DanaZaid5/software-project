<?php
session_start();
require 'db.php'; // change this if your connection file has a different name

// 0) Make sure we have a logged-in client
$clientId = null;

if (isset($_SESSION['client_id'])) {
    $clientId = (int)$_SESSION['client_id'];
} elseif (isset($_SESSION['user_id'])) {
    // Fallback: check if this user_id is a client
    $tmpId = (int)$_SESSION['user_id'];

    if ($stmt = $conn->prepare("SELECT user_id FROM User WHERE user_id = ? AND role = 'client'")) {
        $stmt->bind_param("i", $tmpId);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows === 1) {
            $clientId = $tmpId;
            $_SESSION['client_id'] = $tmpId;
        }
        $stmt->close();
    }
}

if (!$clientId) {
    header("Location: login.php");
    exit;
}

// Handle POST actions (remove service from board, rename board)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // a) Remove a service from this board
    if (isset($_POST['remove_service_id'], $_POST['list_id'])) {
        $listIdPost = (int)$_POST['list_id'];
        $serviceId  = (int)$_POST['remove_service_id'];

        if ($listIdPost > 0 && $serviceId > 0) {
            if ($stmtDel = $conn->prepare("DELETE FROM ListItem WHERE list_id = ? AND service_id = ?")) {
                $stmtDel->bind_param("ii", $listIdPost, $serviceId);
                if (!$stmtDel->execute()) {
                    // Optional: show an error if delete fails
                    die("Error removing service from board: " . $stmtDel->error);
                }
                $stmtDel->close();
            } else {
                die("Error preparing delete: " . $conn->error);
            }
        }

        header("Location: board.php?list_id=" . $listIdPost);
        exit;
    }

    // b) Rename board
    if (isset($_POST['rename_board'], $_POST['board_name'], $_POST['list_id'])) {
        $listIdPost = (int)$_POST['list_id'];
        $newName    = trim($_POST['board_name']);

        if ($listIdPost > 0 && $newName !== '') {
            if ($stmt = $conn->prepare("UPDATE List SET name = ? WHERE list_id = ? AND client_id = ?")) {
                $stmt->bind_param("sii", $newName, $listIdPost, $clientId);
                if (!$stmt->execute()) {
                    die("Error renaming board: " . $stmt->error);
                }
                $stmt->close();
            }
        }

        header("Location: board.php?list_id=" . $listIdPost);
        exit;
    }
}


// Get board (list) id from URL
$listId = isset($_GET['list_id']) ? (int)$_GET['list_id'] : 0;


$board = null;
$services = [];

// 1) Fetch board info and make sure it belongs to THIS client
if ($listId > 0) {
    $stmt = $conn->prepare("
        SELECT list_id, client_id, name 
        FROM List 
        WHERE list_id = ? AND client_id = ?
    ");
    $stmt->bind_param("ii", $listId, $clientId);
    $stmt->execute();
    $result = $stmt->get_result();
    $board = $result->fetch_assoc();
    $stmt->close();

    // 2) If board exists, fetch services saved to it
    if ($board) {
        /*
          We pull:
          - Service info
          - Professional name (from User via Professional)
          - Optional: average rating + count from Review/Booking
        */
        $sql = "
            SELECT
                s.service_id,
                s.title,
                s.description,
                s.duration,
                s.price,
                s.category,
                u.name AS professional_name,
                AVG(r.rating) AS avg_rating,
                COUNT(r.review_id) AS review_count
            FROM ListItem li
            JOIN Service s
              ON li.service_id = s.service_id
            JOIN Professional p
              ON s.professional_id = p.professional_id
            JOIN User u
              ON p.professional_id = u.user_id
            LEFT JOIN Booking b
              ON b.service_id = s.service_id
            LEFT JOIN Review r
              ON r.booking_id = b.booking_id
            WHERE li.list_id = ?
            GROUP BY
              s.service_id,
              s.title,
              s.description,
              s.duration,
              s.price,
              s.category,
              u.name
            ORDER BY s.service_id ASC
        ";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $listId);
        $stmt->execute();
        $servicesRes = $stmt->get_result();

        while ($row = $servicesRes->fetch_assoc()) {
            $services[] = $row;
        }

        $stmt->close();
    }
}

// Helper to choose a cute emoji per category
function category_emoji(string $category): string {
    switch ($category) {
        case 'Makeup':   return '💄';
        case 'Hair':     return '💇‍♀️';
        case 'Skincare': return '🧴';
        case 'Bodycare': return '💆‍♀️';
        case 'Nails':    return '💅';
        default:         return '✨';
    }
}
?>

<!doctype html>
<html lang="en" class="has-solid-header">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Glammd — Board</title>

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800;900&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

  <!-- Shared Styles -->
  <link rel="stylesheet" href="common.css">

  <!-- Page-specific styles -->
  <style>
body{ background:#f8f8f8; }

.section{ padding: 2.5rem 0; }

.page-title{
  margin:0;
  font-family:'Playfair Display', serif;
  font-weight:900;
  font-size:clamp(1.75rem,3.5vw,2.75rem);
}

.actions{ display:flex; gap:0.625rem; flex-wrap:wrap; margin:0.875rem 0 1.5rem; }

.btn{
  display:inline-block; padding:0.625rem 0.875rem; border-radius:0.5rem;
  text-decoration:none; font-weight:600; font-size:0.875rem;
  background:#fff; color:var(--text);
  border:1px solid #eaeaea; box-shadow:0 0.125rem 0.5rem rgba(0,0,0,.04);
}
.btn.primary{ background:var(--accent); color:#fff; border-color:transparent; }

.services-grid{
  display:grid; gap:1.5rem;
  grid-template-columns: repeat(auto-fill, minmax(20rem, 1fr));
}

.service-card{
  background:#fff; border:1px solid #eaeaea; border-radius:0.75rem;
  overflow:hidden; box-shadow:0 0.125rem 0.5rem rgba(0,0,0,.04);
  display:flex; flex-direction:column; transition:transform .2s ease, box-shadow .2s ease;
  position:relative;
}
.service-card:hover{ transform: translateY(-0.1875rem); box-shadow:0 0.5rem 1.5rem rgba(0,0,0,.1); }

.service-image{
  width:100%; height:11.25rem;
  background:linear-gradient(135deg,#f5f5f5 0%, #e8e8e8 100%);
  display:flex; align-items:center; justify-content:center; font-size:3rem;
  border-bottom:1px solid #eaeaea;
}

.service-content{ padding:1.25rem; display:flex; flex-direction:column; flex:1; }
.service-category{
  display:inline-block; padding:0.25rem 0.625rem; background:#f0f0f0; border-radius:999px;
  font-size:0.6875rem; text-transform:uppercase; letter-spacing:1px; font-weight:600; color:var(--muted); margin-bottom:0.75rem;
}
.service-title{ margin:0 0 0.5rem; font-weight:700; font-size:1.25rem; line-height:1.3; }
.service-description{ margin:0 0 1rem; color:var(--muted); font-size:0.875rem; line-height:1.6; }

.service-meta{
  display:flex; justify-content:space-between; align-items:center;
  padding-top:1rem; border-top:1px solid #eaeaea; margin-top:auto; margin-bottom:0.75rem;
}
.service-price{ font-weight:700; font-size:1.25rem; letter-spacing:-0.03125rem; }
.service-duration{ color:var(--muted); font-size:0.8125rem; }
.service-rating{ display:flex; align-items:center; gap:0.25rem; font-size:0.8125rem; }
.stars{ color:#fbbf24; letter-spacing:1px; }

.service-footer{ padding:0 1.25rem 1.25rem; }
.btn-book{
  width:100%; padding:0.75rem 1.25rem; background:var(--accent); color:#fff; border:none;
  border-radius:0.5rem; font-weight:600; font-size:0.9375rem; cursor:pointer; transition:opacity .2s ease; font-family:inherit;
  text-decoration:none; display:inline-block; text-align:center;
}
.btn-book:hover{ opacity:.9; }

.empty{
  background:#fff; border:1px dashed #e2e2e2; border-radius:0.75rem;
  padding:1.5rem; color:var(--muted); text-align:center;
  box-shadow:0 0.125rem 0.5rem rgba(0,0,0,.04); margin-top:1rem;
}

@media (max-width: 48rem){
  .services-grid{ grid-template-columns: 1fr; }
}

/* Favorite heart button (same look/animation as friend's page) */
.favorite-btn {
  position: absolute;
  top: 1rem;
  right: 1rem;
  background: white;
  border: 0.0625rem solid #e0e0e0; /* 1px */
  border-radius: 50%;
  width: 2.25rem;  /* 36px */
  height: 2.25rem; /* 36px */
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
  z-index: 2;
}

.favorite-btn:hover {
  background: #f8f8f8;
}

.favorite-btn svg {
  width: 1.25rem;  /* 20px */
  height: 1.25rem; /* 20px */
  fill: #ccc;
  transition: fill 0.2s ease;
}

.favorite-btn:hover svg {
  fill: #ff4d6d;
}

.favorite-btn.active {
  background: #ff4d6d;
  border-color: #ff4d6d;
}

.favorite-btn.active svg {
  fill: white;
}

.favorite-btn.active:hover svg {
  fill: white;
}

.nav-link {
  text-decoration: none;
  color: var(--text);
  padding: 0.625rem 0.875rem;
  border-radius: 999px;
}

/* small reset so the form around the heart doesn't affect layout */
.remove-service-form {
  margin: 0;
}

/* Modal styles (for Edit Board) */
.modal-overlay{
  position:fixed;
  inset:0;
  background:rgba(0,0,0,0.35);
  display:none;
  align-items:center;
  justify-content:center;
  z-index:1000;
}
.modal-overlay.is-open{
  display:flex;
}
.modal{
  background:#fff;
  border-radius:12px;
  padding:20px 20px 16px;
  max-width:420px;
  width:100%;
  box-shadow:0 10px 30px rgba(0,0,0,0.15);
}
.modal h2{
  margin:0 0 12px;
  font-family:'Playfair Display', serif;
  font-size:22px;
}
.new-board-input{
  width:100%;
  padding:8px 10px;
  border-radius:8px;
  border:1px solid #eaeaea;
  font-size:14px;
  margin-bottom:12px;
}
.modal-actions{
  display:flex;
  justify-content:flex-end;
  gap:8px;
  margin-top:4px;
}
  </style>
</head>

<body class="has-solid-header">
  <!-- Header -->
  <header id="siteHeader" class="site-header">
    <div class="container header-inner">
      <a class="brand" href="index.php">Glammd</a>
      <nav class="nav">
        <a href="logout.php" class="nav-link">Log out</a>
      </nav>
    </div>
  </header>

  <!-- Main -->
  <main class="page">
    <div class="container section">
      <div class="container breadcrumbs-wrap">
        <nav aria-label="Breadcrumb">
          <ol class="breadcrumbs">
            <li><a href="clientdashboard.php">Client Dashboard</a></li>
            <li><a href="favorites.php">Favorites</a></li>
            <li>
              <span class="current" id="crumb-current">
                <?php echo $board ? htmlspecialchars($board['name'], ENT_QUOTES, 'UTF-8') : 'Board'; ?>
              </span>
            </li>
          </ol>
        </nav>
      </div>

      <?php if ($board): ?>
        <!-- Board title -->
        <h1 class="page-title">
          <?php echo htmlspecialchars($board['name'], ENT_QUOTES, 'UTF-8'); ?>
        </h1>

        <div class="actions">
          <a class="btn" href="favorites.php">← Back</a>
          <!-- Edit Board opens rename modal -->
          <button type="button" class="btn primary" id="openEditBoard">Edit Board</button>
        </div>

        <?php if (!empty($services)): ?>
          <!-- Services -->
          <div class="services-grid" id="services-grid">
            <?php foreach ($services as $service): ?>
              <article class="service-card">
                <!-- Heart: remove service from this board -->
                <form method="post" class="remove-service-form">
                  <input type="hidden" name="list_id" value="<?php echo (int)$listId; ?>">
                  <input type="hidden" name="remove_service_id" value="<?php echo (int)$service['service_id']; ?>">
                  <button class="favorite-btn active" aria-label="Remove from this board" type="submit">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                      <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5
                      2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81
                      14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55
                      11.54L12 21.35z"/>
                    </svg>
                  </button>
                </form>

                <div class="service-image">
                  <?php echo category_emoji($service['category']); ?>
                </div>

                <div class="service-content">
                  <span class="service-category">
                    <?php echo htmlspecialchars($service['category'], ENT_QUOTES, 'UTF-8'); ?>
                  </span>

                  <h3 class="service-title">
                    <?php echo htmlspecialchars($service['title'], ENT_QUOTES, 'UTF-8'); ?>
                  </h3>

                  <p class="service-description">
                    <?php echo htmlspecialchars($service['description'], ENT_QUOTES, 'UTF-8'); ?>
                  </p>

                  <div class="service-meta">
                    <div>
                      <div class="service-price">
                        SAR <?php echo number_format((float)$service['price'], 2); ?>
                      </div>
                      <div class="service-duration">
                        <?php echo (int)$service['duration']; ?> min
                        <br>
                        <small>
                          by <?php echo htmlspecialchars($service['professional_name'], ENT_QUOTES, 'UTF-8'); ?>
                        </small>
                      </div>
                    </div>

                    <div class="service-rating">
                      <?php if ((int)$service['review_count'] > 0): ?>
                        <span class="stars">★★★★★</span>
                        <span>(<?php echo (int)$service['review_count']; ?>)</span>
                      <?php else: ?>
                        <span class="stars">☆☆☆☆☆</span>
                        <span>(No reviews yet)</span>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>

                <div class="service-footer">
                  <a href="booking.php?service_id=<?php echo (int)$service['service_id']; ?>"
                     class="btn-book">
                    Book Now
                  </a>
                </div>
              </article>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <p class="empty">
            This board doesn’t have any saved services yet.
          </p>
        <?php endif; ?>

      <?php else: ?>
        <!-- Board not found / invalid id -->
        <h1 class="page-title">Board not found</h1>
        <div class="actions">
          <a class="btn" href="favorites.php">← Back to Favorites</a>
        </div>
        <p class="empty">
          We couldn’t find this board. It may have been deleted or the link is incorrect.
        </p>
      <?php endif; ?>
    </div>
  </main>

  <!-- Rename Board Modal -->
  <?php if ($board): ?>
  <div class="modal-overlay" id="editBoardModal">
    <div class="modal">
      <h2>Edit Board</h2>
      <form method="post">
        <input
          type="text"
          name="board_name"
          class="new-board-input"
          value="<?php echo htmlspecialchars($board['name'], ENT_QUOTES, 'UTF-8'); ?>"
          required
        />
        <input type="hidden" name="list_id" value="<?php echo (int)$board['list_id']; ?>">
        <div class="modal-actions">
          <button type="button" class="btn" id="cancelEditBoard">Cancel</button>
          <button type="submit" name="rename_board" value="1" class="btn primary">
            Save
          </button>
        </div>
      </form>
    </div>
  </div>
  <?php endif; ?>

  <!-- Footer -->
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
    document.addEventListener('DOMContentLoaded', function () {
      // Confirm before removing a service from the board
      const forms = document.querySelectorAll('.remove-service-form');
      forms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
          const ok = confirm('Remove this service from this board?');
          if (!ok) {
            e.preventDefault();
          }
        });
      });

      // Edit Board modal
      const editModal    = document.getElementById('editBoardModal');
      const openEditBtn  = document.getElementById('openEditBoard');
      const cancelEditBtn = document.getElementById('cancelEditBoard');

      if (openEditBtn && editModal) {
        openEditBtn.addEventListener('click', function (e) {
          e.preventDefault();
          editModal.classList.add('is-open');
        });
      }

      if (cancelEditBtn && editModal) {
        cancelEditBtn.addEventListener('click', function (e) {
          e.preventDefault();
          editModal.classList.remove('is-open');
        });
      }

      if (editModal) {
        editModal.addEventListener('click', function (e) {
          if (e.target === editModal) {
            editModal.classList.remove('is-open');
          }
        });
      }
    });
  </script>
</body>
</html>
