<?php
session_start();

// shared DB connection
require 'db.php';

/*
  Figure out which client is logged in.
  login.php already sets $_SESSION['client_id'] for clients,
  but we also add a small fallback if only user_id is set.
*/

$clientId = null;

// Preferred: direct client_id from login
if (isset($_SESSION['client_id'])) {
    $clientId = (int)$_SESSION['client_id'];
}
// Fallback: if only user_id is set, confirm this user is a client
elseif (isset($_SESSION['user_id'])) {
    $tmpId = (int)$_SESSION['user_id'];

    if ($stmt = $conn->prepare("SELECT user_id FROM User WHERE user_id = ? AND role = 'client'")) {
        $stmt->bind_param("i", $tmpId);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows === 1) {
            $clientId = $tmpId;
            // also store as client_id for later pages
            $_SESSION['client_id'] = $tmpId;
        }
        $stmt->close();
    }
}

// If we still have no client => send to login
if (!$clientId) {
    header("Location: login.php");
    exit;
}

$boards     = [];
$manageMode = isset($_GET['manage']) && $_GET['manage'] == '1';


/* =========================
   Handle form actions
   ========================= */

// 1) Create new board
if (isset($_POST['create_board']) && !empty(trim($_POST['board_name']))) {
    $boardName = trim($_POST['board_name']);

    $stmt = $conn->prepare("INSERT INTO List (client_id, name) VALUES (?, ?)");
    $stmt->bind_param("is", $clientId, $boardName);
    $stmt->execute();
    $stmt->close();

    // redirect to avoid resubmission
    header("Location: favorites.php");
    exit;
}

// 2) Delete a board (only in manage mode)
if (isset($_POST['delete_board_id'])) {
    $deleteId = (int)$_POST['delete_board_id'];

    // Only delete if this board belongs to this client
    $stmt = $conn->prepare("DELETE FROM List WHERE list_id = ? AND client_id = ?");
    $stmt->bind_param("ii", $deleteId, $clientId);
    $stmt->execute();
    $stmt->close();

    // stay in manage mode after delete
    header("Location: favorites.php?manage=1");
    exit;
}

/* =========================
   Load boards for this client
   ========================= */

$sql = "
    SELECT 
        l.list_id,
        l.name,
        COUNT(li.service_id) AS service_count
    FROM List l
    LEFT JOIN ListItem li ON li.list_id = l.list_id
    WHERE l.client_id = ?
    GROUP BY l.list_id, l.name
    ORDER BY l.list_id DESC
";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $clientId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $boards[] = $row;
    }
    $stmt->close();
}
?>
<!doctype html>
<html lang="en" class="has-solid-header">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Glammd — Favorites</title>

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800;900&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

  <!-- Shared Styles -->
  <link rel="stylesheet" href="common.css">

  <!-- Page-specific styles (mirrors Services page look/feel) -->
  <style>
    body{ background:#f8f8f8; }

    .section{ padding: 40px 0; }
    .page-title{
      margin:0;
      font-family:'Playfair Display', serif;
      font-weight:900;
      font-size:clamp(32px,4vw,56px);
    }

    .boards-toolbar{
      display:flex; gap:12px; flex-wrap:wrap;
      margin: 16px 0 24px;
    }
    .btn{
      display:inline-block; padding:10px 14px; border-radius:8px;
      text-decoration:none; font-weight:600; font-size:14px;
      background:#fff; color:var(--text);
      border:1px solid #eaeaea; box-shadow:0 2px 8px rgba(0,0,0,.04);
      cursor:pointer;
    }
    .btn.primary{ background:var(--accent); color:#fff; border-color:transparent; }

    .boards-grid{
      display:grid; gap:24px;
      grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    }

    .board-card{
      background:#fff; border:1px solid #eaeaea; border-radius:12px;
      overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,.04);
      transition: transform .2s ease, box-shadow .2s ease;
      display:flex; flex-direction:column;
      text-decoration:none;
      color:var(--text);
    }
    .board-card:hover{ transform: translateY(-3px); box-shadow:0 8px 24px rgba(0,0,0,.1); }

    .board-thumb{
      height:140px; background: linear-gradient(135deg,#f5f5f5 0%, #e8e8e8 100%);
      display:grid; grid-template-columns:1fr 1fr; gap:2px; padding:2px;
    }
    .board-thumb div{ background:#fff; }

    .board-content{ padding:16px; }
    .board-title{ margin:0 0 6px; font-weight:700; font-size:18px; }
    .board-sub{ margin:0; color:var(--muted); font-size:13px; }

    .empty{
      background:#fff; border:1px dashed #e2e2e2; border-radius:12px;
      padding:24px; color:var(--muted); text-align:center;
      box-shadow:0 2px 8px rgba(0,0,0,.04); margin-top:16px;
    }
	
	.nav-link {
      text-decoration: none;
      color: var(--text);
      padding: 10px 14px;
      border-radius: 999px;
    }

    .board-card:visited{
      color: var(--text);
    }

    /* Modal styles */
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
            <li><span class="current">Favorites</span></li>
          </ol>
        </nav>
      </div>
	  
      <div class="boards-toolbar">
        <!-- Single Add Board button that opens the popup -->
        <button type="button" class="btn primary" id="openBoardModal">+ New Board</button>

        <!-- Manage Boards toggles manage mode (show delete buttons) -->
        <?php if ($manageMode): ?>
          <a href="favorites.php" class="btn">Done Managing</a>
        <?php else: ?>
          <a href="favorites.php?manage=1" class="btn">Manage Boards</a>
        <?php endif; ?>
      </div>

      <!-- Boards -->
      <?php if (!empty($boards)): ?>
        <div class="boards-grid">
          <?php foreach ($boards as $board): ?>
            <?php if (!$manageMode): ?>
              <!-- Normal mode: clickable card -->
              <a class="board-card" href="board.php?list_id=<?= (int)$board['list_id'] ?>">
                <div class="board-thumb">
                  <div></div><div></div><div></div><div></div>
                </div>
                <div class="board-content">
                  <h3 class="board-title">
                    <?= htmlspecialchars($board['name'], ENT_QUOTES, 'UTF-8') ?>
                  </h3>
                  <p class="board-sub">
                    <?php
                      $count = (int)$board['service_count'];
                      echo $count === 1 ? '1 saved service' : $count . ' saved services';
                    ?>
                  </p>
                </div>
              </a>
            <?php else: ?>
              <!-- Manage mode: card + delete button -->
              <div class="board-card">
                <div class="board-thumb">
                  <div></div><div></div><div></div><div></div>
                </div>
                <div class="board-content">
                  <h3 class="board-title">
                    <?= htmlspecialchars($board['name'], ENT_QUOTES, 'UTF-8') ?>
                  </h3>
                  <p class="board-sub">
                    <?php
                      $count = (int)$board['service_count'];
                      echo $count === 1 ? '1 saved service' : $count . ' saved services';
                    ?>
                  </p>
                </div>
                <form method="post" style="padding:0 16px 16px;">
                  <input type="hidden" name="delete_board_id" value="<?= (int)$board['list_id'] ?>">
                  <button
                    type="submit"
                    class="btn"
                    style="width:100%; text-align:center; background:#fff0f0; border-color:#f5b5b5;"
                    onclick="return confirm('Delete this board? This cannot be undone.');"
                  >
                    Delete Board
                  </button>
                </form>
              </div>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p class="empty">You don’t have any boards yet. Create one to save services you love.</p>
      <?php endif; ?>
    </div>
  </main>

  <!-- Modal for creating a new board -->
  <div class="modal-overlay" id="boardModal">
    <div class="modal">
      <h2>New Board</h2>
      <form method="post">
        <input
          type="text"
          name="board_name"
          class="new-board-input"
          placeholder="Board name (e.g. Wedding Look)"
          required
        />
        <div class="modal-actions">
          <button type="button" class="btn" id="cancelBoardModal">Cancel</button>
          <button type="submit" name="create_board" value="1" class="btn primary">
            Create
          </button>
        </div>
      </form>
    </div>
  </div>

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
      const modal    = document.getElementById('boardModal');
      const openBtn  = document.getElementById('openBoardModal');
      const cancelBtn = document.getElementById('cancelBoardModal');

      if (openBtn && modal) {
        openBtn.addEventListener('click', function (e) {
          e.preventDefault();
          modal.classList.add('is-open');
        });
      }

      if (cancelBtn && modal) {
        cancelBtn.addEventListener('click', function (e) {
          e.preventDefault();
          modal.classList.remove('is-open');
        });
      }

      // close when clicking outside the modal
      if (modal) {
        modal.addEventListener('click', function (e) {
          if (e.target === modal) {
            modal.classList.remove('is-open');
          }
        });
      }
    });
  </script>
</body>
</html>
