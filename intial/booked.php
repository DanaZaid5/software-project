<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    /* ===== Root Variables ===== */
    :root{
      --bg:#fff;
      --text:#0f0f12;
      --muted:#6b6b76;
      --accent:#111;
      --accent-text:#fff;
      --container:1200px;
      --hero-image: url("img/download.jpeg");
    }

    /* ===== Global Styles ===== */
    *{box-sizing:border-box; margin:0; padding:0;}
    html, body{height:100%;}
    body{
      font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
      display:flex;
      flex-direction:column;
      min-height:100vh;
      background: var(--bg);
      color: var(--text);
    }
    .container{
      max-width: var(--container);
      margin-inline: auto;
      padding-inline: 20px;
      width: 100%;
    }

    /* ===== Header ===== */
    .site-header{
      position: fixed;
      top: 0; left: 0; right: 0;
      z-index: 50;
      height: 64px;
      display: block;
      background: transparent;
      border-bottom: none;
      transition: background .25s ease, border-color .25s ease, backdrop-filter .25s ease, box-shadow .25s ease;
      backdrop-filter: none;
      -webkit-backdrop-filter: none;
      box-shadow: none;
    }
    .site-header.show{
      backdrop-filter: saturate(150%) blur(8px);
      -webkit-backdrop-filter: saturate(150%) blur(8px);
      background: color-mix(in srgb, white 75%, transparent);
      border-bottom: 1px solid rgba(0,0,0,.06);
      box-shadow: 0 1px 10px rgba(0,0,0,.04);
    }

    .header-inner{
      height: 64px;
      display:flex;
      align-items:center;
      justify-content: space-between;
    }

    .brand{
      text-decoration:none;
      color: var(--text);
      font-weight:700;
      font-size:22px;
      letter-spacing:.2px;
    }

    .nav{ display:flex; gap:10px; }
    .nav-link{
      text-decoration:none;
      color: var(--text);
      padding:10px 14px;
      border-radius:999px;
      transition: background .2s ease;
    }
    .nav-link:hover{ background: rgba(0,0,0,.05); }
    .cta{
      text-decoration:none;
      color: var(--accent-text);
      background: var(--accent);
      padding:10px 16px;
      border-radius:999px;
      font-weight:600;
      transition: opacity .2s ease;
    }
    .cta:hover{ opacity:.85; }

    /* ===== Hero Section ===== */
    .hero{
  position: relative;
  min-height: auto;
  padding: 120px 0 80px; /* مسافة من الأعلى والأسفل */
  display: flex;
  justify-content: center; /* لمحاذاة أفقياً */
  align-items: flex-end;   /* لمحاذاة الكارد نحو الأسفل */
  background-image:
    linear-gradient(rgba(255,255,255,0.25), rgba(255,255,255,0.25)),
    var(--hero-image);
  background-size: cover;
  background-position: center 20%;
  background-repeat: no-repeat;
}
    .hero::after{
      content:"";
      position:absolute; inset:0;
      background: linear-gradient(
        to bottom,
        rgba(255,255,255,0) 0%,
        rgba(255,255,255,0) 60%,
        rgba(255,255,255,0.4) 85%,
        rgba(255,255,255,1) 100%
      );
      pointer-events:none;
    }

    /* ===== Success Container ===== */
    .success-container{
      background: #fff;
      border-radius: 20px;
      padding: 50px 35px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.08);
      text-align: center;
      max-width: 450px;
      width: 100%;
      position: relative;
      z-index: 1;
    }
    h1{
      font-family: "Playfair Display", serif;
      font-weight: 800;
      font-size: 28px;
      margin-bottom: 15px;
      color: var(--text);
    }
    p{ font-size:16px; margin:8px 0; color:var(--muted);}
    .service{ font-weight:600; color:var(--text);}
    .instructions{
      background: rgba(17,17,17,0.05);
      padding:15px;
      border-radius:12px;
      margin-top:20px;
      font-size:14px;
      color:var(--text);
    }
    .btn-home{
      display:inline-block;
      margin-top:25px;
      padding:12px 25px;
      background: var(--accent);
      color:#fff;
      border-radius:12px;
      text-decoration:none;
      font-weight:bold;
      transition: all 0.3s ease;
    }
    .btn-home:hover{
      transform: translateY(-3px);
      box-shadow: 0 6px 18px rgba(17,17,17,0.25);
    }

    /* Footer */
    .site-footer{
      background:#f8f8f8;
      padding:20px 0;
      text-align:center;
      font-size:14px;
      color:var(--muted);
    }
    .site-footer a{
      margin:0 10px;
      color:var(--muted);
      text-decoration:none;
    }
  </style>
</head>
<body>

  <!-- Header -->
  <header id="siteHeader" class="site-header">
    <div class="container header-inner">
      <a class="brand" href="index.html">Glammd</a>
      <nav class="nav">
        <a href="index.html" class="nav-link">Log in</a>
        <a href="signup.html" class="cta">Sign up</a>
      </nav>
    </div>
  </header>

  <!-- Main Hero + Success Content -->
  <main class="hero">
    <div class="success-container">
      <div class="success-icon" style="font-size:60px; color:#4CAF50; margin-bottom:20px;">✔️</div>
      <h1>Thank you for your booking!</h1>
      <p>Artist: <span class="service">Sarah M.</span></p>
      <p>Service: <span class="service">Full Makeup Package</span></p>
      <p>Appointment: <span class="service">Nov 10, 3:00 PM</span></p>
      <div class="instructions">
        Please arrive 10 minutes early<br>
        Bring a valid ID
      </div>
      <a href="index.html" class="btn-home">Back to Home</a>
    </div>
  </main>

  <!-- Footer -->
  <footer class="site-footer">
    <p>© 2025 Glammd. All rights reserved.</p>
    <div class="footer-links">
      <a href="#">Privacy Policy</a>
      <a href="#">Terms of Service</a>
      <a href="#">Contact</a>
    </div>
  </footer>

</body>
</html>
