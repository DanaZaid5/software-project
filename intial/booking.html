<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Book Service – Glammd</title>

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800;900&family=Inter:wght@400;500;600&family=Tartuffo:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="common.css">

  <style>
    :root {
      --accent: #000;
      --muted: #777;
      --text: #333;
      --hero-image: url("img/download.jpeg");
    }

    body {
      background: #f8f8f8; /* same background as board.html */
      font-family: 'Inter', sans-serif;
      margin: 0;
      padding: 0;
      min-height: 100vh;
    }

    /* Hero Background */
    .page {
      position: relative;
      min-height: 100vh;
      background-image:
        linear-gradient(rgba(255,255,255,0.25), rgba(255,255,255,0.25)),
        var(--hero-image);
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
        rgba(255,255,255,0.9) 100%
      );
      pointer-events: none;
    }

    /* Main Content */
    main.page {
      padding-top: 6rem;
      min-height: 100vh;
      position: relative;
    }

    /* Booking Container */
    .booking-container {
      background: #fff;
      border-radius: 1rem;
      padding: 2.5rem;
      box-shadow: 0 2px 12px rgba(0,0,0,0.08);
      max-width: 600px;
      margin: 0 auto 3rem;
      position: relative;
      z-index: 1;
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
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
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
      font-family: 'Inter', sans-serif;
    }

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
      font-family: 'Inter', sans-serif;
    }

    /* Form Elements */
    .form-group {
      margin-bottom: 1.5rem;
    }

    .form-group label {
      display: block;
      margin-bottom: 0.625rem;
      font-weight: 600;
      color: #333;
      font-family: 'Inter', sans-serif;
    }

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
      transition: all 0.2s;
      font-family: 'Inter', sans-serif;
    }

    .tag.active {
      background: #000;
      color: #fff;
    }

    .color-options {
      display: flex;
      gap: 0.625rem;
      margin-top: 0.5rem;
    }

    .color-choice {
      width: 2rem;
      height: 2rem;
      border-radius: 50%;
      cursor: pointer;
      border: 2px solid transparent;
      transition: all 0.2s;
    }

    .color-choice.active {
      border-color: #000;
      transform: scale(1.1);
    }

    .submit-btn {
      width: 100%;
      padding: 0.875rem;
      background: #000;
      color: white;
      border: none;
      border-radius: 0.5rem;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      margin-top: 1.25rem;
      transition: all 0.2s;
      font-family: 'Inter', sans-serif;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .submit-btn:hover {
      opacity: 0.9;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    /* Breadcrumbs */
    .breadcrumbs {
      font-size: 0.875rem;
      color: #666;
      margin-bottom: 1.25rem;
      padding-top: 1rem;
    }

    .breadcrumbs a {
      color: #666;
      text-decoration: none;
      transition: color 0.2s;
      font-family: 'Inter', sans-serif;
    }

    .breadcrumbs a:hover {
      color: #000;
      text-decoration: underline;
    }

    .breadcrumbs span {
      margin: 0 0.375rem;
      color: #999;
    }

    /* Popup */
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
      animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    .popup-content {
      background: white;
      padding: 2.5rem;
      border-radius: 1rem;
      text-align: center;
      max-width: 400px;
      width: 90%;
      box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    }

    .popup h2 {
      font-family: 'Playfair Display', serif;
      margin-bottom: 1rem;
      color: #333;
    }

    .popup p {
      color: #666;
      margin-bottom: 1.5rem;
      font-family: 'Inter', sans-serif;
      line-height: 1.6;
    }

    @media (max-width: 768px) {
      .booking-container {
        padding: 1.5rem;
        margin: 1.25rem;
      }

      .booking-header {
        flex-direction: column;
        text-align: center;
      }

      .tags {
        justify-content: center;
      }

      .breadcrumbs {
        font-size: 0.75rem;
        text-align: center;
      }
    }
  </style>
</head>

<body class="has-solid-header">
  <!-- Header identical to board.html -->
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
    <div class="container">
      <div class="breadcrumbs">
        <a href="clientdashboard.html">Client Dashboard</a>
        <span>›</span>
        <a href="MarketPlace.html">Market</a>
        <span>›</span>
        <a href="services.html">Sarah M.</a>
        <span>›</span>
        <strong>Book Service</strong>
      </div>

      <div class="booking-container">
        <div class="booking-header">
          <img src="img/pro1.jpg" alt="Sarah M.">
          <div class="info">
            <h2>Sarah M.</h2>
            <p>Professional Makeup Artist & Beauty Specialist</p>
            <p>★ 4.9 | 127 Reviews | 5+ years experience</p>
          </div>
        </div>

        <div class="service-details">
          <h3>Signature Glow Makeup</h3>
          <p>Flawless, radiant makeup perfect for special occasions. Includes skin prep and long-lasting finish.</p>
          <p><strong>Duration:</strong> 90 min</p>
          <p><strong>Price:</strong> SAR 1,200</p>
        </div>

        <form id="bookingForm">
          <div class="form-group">
            <label>Makeup Tone</label>
            <div class="tags" id="toneTags">
              <div class="tag">Natural</div>
              <div class="tag">Soft Glam</div>
              <div class="tag">Bold</div>
            </div>
          </div>

          <div class="form-group">
            <label>Color Palette</label>
            <div class="color-options">
              <div class="color-choice" style="background:#d4a373;"></div>
              <div class="color-choice" style="background:#e07a5f;"></div>
              <div class="color-choice" style="background:#9b5de5;"></div>
              <div class="color-choice" style="background:#f2cc8f;"></div>
            </div>
          </div>

          <div class="form-group">
            <label>Skin Type</label>
            <div class="tags" id="skinTags">
              <div class="tag">Normal</div>
              <div class="tag">Oily</div>
              <div class="tag">Dry</div>
              <div class="tag">Combination</div>
            </div>
          </div>

          <div class="form-group">
            <label>Preferred Time</label>
            <div class="tags" id="timeTags">
              <div class="tag">10:00 AM</div>
              <div class="tag">10:30 AM</div>
              <div class="tag">11:00 AM</div>
              <div class="tag">11:30 AM</div>
              <div class="tag">12:00 PM</div>
              <div class="tag">12:30 PM</div>
              <div class="tag">1:00 PM</div>
              <div class="tag">1:30 PM</div>
            </div>
          </div>

          <div class="form-group">
            <label>Payment Method</label>
            <p style="color:var(--muted); margin-top: 5px;">Pay in person at appointment.</p>
          </div>

          <button type="submit" class="submit-btn">Confirm Booking</button>
        </form>
      </div>
    </div>
  </main>

  <div class="popup" id="popup">
    <div class="popup-content">
      <div style="font-size:3rem; margin-bottom:0.75rem; color: #4CAF50;">✓</div>
      <h2>Thank you for your booking!</h2>
      <p>Your appointment with Sarah M. for Signature Glow Makeup has been confirmed.</p>
      <a href="clientdashboard.html" class="submit-btn" style="width:auto; text-decoration:none; display:inline-block; padding:0.625rem 1.5rem;">Back to Dashboard</a>
    </div>
  </div>

  <script>
    function setupTags(containerId) {
      const container = document.getElementById(containerId);
      if (!container) return;
      const tags = container.querySelectorAll('.tag');
      tags.forEach(tag => {
        tag.addEventListener('click', () => {
          if (containerId === 'timeTags') tags.forEach(t => t.classList.remove('active'));
          tag.classList.toggle('active');
        });
      });
    }

    const colors = document.querySelectorAll('.color-choice');
    colors.forEach(color => {
      color.addEventListener('click', () => {
        colors.forEach(c => c.classList.remove('active'));
        color.classList.add('active');
      });
    });

    const form = document.getElementById('bookingForm');
    const popup = document.getElementById('popup');

    if (form) {
      form.addEventListener('submit', e => {
        e.preventDefault();
        popup.classList.add('active');
        setTimeout(() => window.location.href = 'clientdashboard.html', 3000);
      });
    }

    document.addEventListener('DOMContentLoaded', () => {
      setupTags('toneTags');
      setupTags('skinTags');
      setupTags('timeTags');
      if (colors.length > 0) colors[0].classList.add('active');
    });
  </script>
</body>
</html>
