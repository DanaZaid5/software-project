<!DOCTYPE html>
<html lang="en" class="has-solid-header">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Client Dashboard</title>

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800;900&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="common.css">

  <style>
    /* Page-specific styling only */
    main.page { padding-top: 4rem; }
body {
  background: #f8f8f8; /* same as board.html */
}


    .wrap {
      max-width: 70rem;
      margin: 2rem auto 5rem;
      padding: 0 1rem;
    }

    h1 { font-family: 'Playfair Display', serif; font-weight: 900; font-size: clamp(1.75rem, 3.5vw, 2.75rem); margin: 0 0 1.5rem; }

    .tabs {
      display: flex;
      gap: 0.5rem;
      margin-bottom: 1.25rem;
      flex-wrap: wrap;
    }

    .tabs a {
      text-decoration: none;
      color: var(--muted);
      padding: 0.625rem 0.875rem;
      border: 1px solid var(--line);
      border-radius: 999px;
      background: #fff;
      transition: all .2s ease;
      font-weight: 500;
    }
    .tabs a:hover {
      color: var(--text);
      transform: translateY(-1px);
    }
    .tabs a.active {
      color: #fff;
      background: var(--accent);
      border-color: var(--accent);
    }

    .panel {
      background: #fff;
      border: 1px solid var(--line);
      border-radius: 0.875rem;
      box-shadow: 0 2px 8px rgba(0,0,0,.06);
      padding: 1.25rem;
    }

    .list {
      display: grid;
      gap: 1rem;
      margin-top: 0.5rem;
    }

    .item {
      display: grid;
      grid-template-columns: 1fr auto;
      align-items: center;
      gap: 1rem;
      padding: 1rem;
      border: 1px solid var(--line);
      border-radius: 0.5rem;
      background: #fff;
    }

.panel, .item, .wrap {
  background: #fff;
}


    .meta { display: grid; gap: 0.25rem; }
    .title { font-weight: 600; }
    .muted { color: var(--muted); }
    .price { font-weight: 700; letter-spacing: 0.3px; }
  </style>
</head>

<body class="has-solid-header">

  <!-- Header (same as board.html) -->
  <header id="siteHeader" class="site-header">
    <div class="container header-inner">
      <a class="brand" href="index.html">Glammd</a>
      <nav class="nav">
        <a href="MarketPlace.html" class="nav-link">Market</a>
        <a href="favorites.html" class="nav-link">Favorites</a>
        <a href="index.html" class="nav-link">Log out</a>
      </nav>
    </div>
  </header>

  <main class="page">
    <div class="wrap">
      <h1>Hello, Seema</h1>

      <nav class="tabs">
        <a href="#upcoming" class="active">Upcoming Bookings</a>
        <a href="#history">Booking History</a>
      </nav>

      <div class="content">
        <!-- Upcoming -->
        <section id="upcoming" class="section default-visible">
          <div class="panel list">
            <div class="item">
              <div class="meta">
                <div class="title">Signature Glow Makeup</div>
                <div class="muted">Sarah M. · Riyadh · 90 min · 2025-11-10 10:00</div>
              </div>
              <div class="price">SAR 1,200</div>
            </div>

            <div class="item">
              <div class="meta">
                <div class="title">Soft Waves Styling</div>
                <div class="muted">Aljohara Alsultan · Riyadh · 45 min · 2025-11-12 14:30</div>
              </div>
              <div class="price">SAR 300</div>
            </div>
          </div>
        </section>

        <!-- History -->
        <section id="history" class="section" style="display:none;">
          <div class="panel list">
            <div class="item">
              <div class="meta">
                <div class="title">Evening Glam</div>
                <div class="muted">Sarah M. · Riyadh · 60 min · 2025-10-30 18:00</div>
              </div>
              <div class="price">SAR 650</div>
            </div>
            <div class="item">
              <div class="meta">
                <div class="title">Gel Manicure</div>
                <div class="muted">Layan Abdulaziz · Riyadh · 50 min · 2025-10-28 16:30</div>
              </div>
              <div class="price">SAR 180</div>
            </div>
            <div class="item">
              <div class="meta">
                <div class="title">Hydrating Facial</div>
                <div class="muted">Sarah M. · Riyadh · 75 min · 2025-10-25 11:00</div>
              </div>
              <div class="price">SAR 450</div>
            </div>
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
    // Tabs toggle
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
  </script>
</body>
</html>
