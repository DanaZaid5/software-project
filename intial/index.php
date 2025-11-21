<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Glammd</title>

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800;900&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="common.css">

  <style>
:root {
  --hero-image: url("img/download.jpeg");
  --hero-min-h: min(78svh, 51.25rem); /* 820px */
}

.home .site-header {
  background: transparent;
  border-bottom: none;
  backdrop-filter: none;
  box-shadow: none;
}

.home .site-header.show {
  backdrop-filter: saturate(150%) blur(0.5rem);
  background: color-mix(in srgb, white 75%, transparent);
  border-bottom: 0.0625rem solid rgba(0,0,0,.06);
  box-shadow: 0 0.0625rem 0.625rem rgba(0,0,0,.04);
}

.hero {
  position: relative;
  min-height: var(--hero-min-h);
  display: grid;
  place-items: center;
  background-image:
    linear-gradient(rgba(255,255,255,0.25), rgba(255,255,255,0.25)),
    var(--hero-image);
  background-size: cover;
  background-position: center 20%;
  background-repeat: no-repeat;
}

.hero::after {
  content: "";
  position: absolute;
  inset: 0;
  background: linear-gradient(
    to bottom,
    rgba(255,255,255,0) 0%,
    rgba(255,255,255,0) 60%,
    rgba(255,255,255,0.4) 85%,
    rgba(255,255,255,1) 100%
  );
  pointer-events: none;
}

.hero-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    180deg,
    rgba(255,255,255,.0) 0%,
    rgba(255,255,255,.06) 40%,
    rgba(255,255,255,.08) 100%
  );
}

.hero-content {
  position: relative;
  text-align: center;
  padding: 4.5rem 0 2.5rem; /* 72px 0 40px */
}

.hero-title {
  margin: 0;
  font-family: "Playfair Display", Georgia, serif;
  font-weight: 900;
  font-size: clamp(2.5rem, 6vw, 5.25rem); /* 40px–84px */
  line-height: 0.95;
  letter-spacing: -0.03125rem; /* -0.5px */
}

/* Vision / Mission Section */
.vision-section {
  text-align: center;
  padding: 0.3125rem 1.25rem 4.375rem 1.25rem; /* 5px 20px 70px 20px */
  max-width: 50rem; /* 800px */
  margin: 0 auto;
}

.vision-section h2 {
  font-family: "Playfair Display", serif;
  font-weight: 800;
  font-size: 2.5rem; /* 40px */
  margin-bottom: 1.25rem; /* 20px */
}

.vision-section p {
  font-size: 1.125rem; /* 18px */
  color: var(--muted);
  line-height: 1.6;
  margin-bottom: 1.75rem; /* 28px */
}

/* Explore Button */
.explore-btn {
  display: inline-block;
  margin-top: 1.5rem; /* 24px */
  background: var(--accent);
  color: var(--accent-text);
  text-decoration: none;
  font-weight: 600;
  padding: 0.875rem 1.75rem; /* 14px 28px */
  border-radius: 999px;
  transition: all 0.25s ease;
  font-size: 1rem; /* 16px */
}

.explore-btn:hover {
  background: color-mix(in srgb, var(--accent) 90%, black 10%);
  transform: translateY(-0.125rem); /* 2px */
  box-shadow: 0 0.25rem 0.75rem rgba(0,0,0,0.1); /* 4px 12px */
}

.explore-text {
  display: block;
  font-family: "Playfair Display", Georgia, serif;
  font-weight: 500;
  font-size: clamp(1.375rem, 2.3vw, 2rem); /* 22px–32px */
  color: var(--text);
  letter-spacing: 0.01875rem; /* 0.3px */
  margin-bottom: 1rem; /* 16px */
}


  </style>
</head>

<body class="home">
  <!-- Header -->
  <header id="siteHeader" class="site-header">
    <div class="container header-inner">
      <a class="brand" href="#">Glammd</a>
      <nav class="nav">
        <a href="login.php" class="nav-link">Log in</a>
        <a href="signup.php" class="cta">Sign up</a>
      </nav>
    </div>
  </header>

  <!-- Hero -->
  <section class="hero">
    <div class="hero-overlay"></div>
    <div class="container hero-content">
      <h1 class="hero-title">Book local beauty and wellness services</h1>
    </div>
  </section>
  

  <!-- Vision & Mission -->
  <main class="container page">
    <section class="vision-section">
	
	      <div class="explore-wrapper">
        <span class="explore-text">Discover local beauty experiences</span>
        <a href="MarketPlace.php" class="explore-btn">Start Exploring</a>
      </div>
	  
      <h2>Our Vision</h2>
      <p>To empower local professionals and clients through seamless, trusted, and beautiful connections that redefine self-care and creativity.</p>
      <h2>Our Mission</h2>
      <p>To make beauty and wellness services accessible, personal, and inspiring — by bringing local expertise right to your fingertips.</p>

    </section>
  </main>

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

  <script src="homeScript.js"></script>
</body>
</html>
