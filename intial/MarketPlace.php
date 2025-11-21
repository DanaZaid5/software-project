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

    /* ===== Filters & cards (your marketplace UI) ===== */
    .filters {
      display: flex;
      flex-wrap: wrap;
      gap: 1rem;
      margin: 2rem 1.5rem;
      justify-content: flex-start;
    }

    .filter-group {
      display: flex;
      flex-direction: column;
      background: #fff;
      border-radius: 1rem;
      padding: 0.9375rem 1.25rem;
      box-shadow: 0 0.25rem 1.25rem rgba(0,0,0,0.05);
      transition: transform 0.2s, box-shadow 0.2s;
      cursor: pointer;
      min-width: 11.25rem;
    }

    .filter-group:hover {
      transform: translateY(-0.25rem);
      box-shadow: 0 0.375rem 1.5625rem rgba(255,117,140,0.2);
    }

    .filter-label {
      font-size: 0.75rem;
      color: #888;
      font-weight: 600;
      margin-bottom: 0.5rem;
      text-transform: uppercase;
      letter-spacing: 0.0625rem;
    }

    .filter-select {
      border: none;
      font-size: 0.875rem;
      padding: 0.5rem 0.75rem;
      border-radius: 0.75rem;
      box-shadow: inset 0 0.0625rem 0.1875rem rgba(0,0,0,0.1);
      background: #fdfdfd;
      cursor: pointer;
      transition: all 0.2s;
    }

    .filter-select:hover {
      box-shadow: inset 0 0.125rem 0.375rem rgba(0,0,0,0.15);
    }

    .salon-list {
      display: flex;
      flex-direction: column;
      gap: 1.25rem;
      padding: 1.5rem;
      border-radius: 0.9375rem;
      box-shadow: 0 0.25rem 0.75rem rgba(0,0,0,0.05);
    }

    .salon-card {
      display: flex;
      align-items: center;
      background: white;
      border-radius: 1rem;
      padding: 0.9375rem;
      box-shadow: 0 0.25rem 0.9375rem rgba(0, 0, 0, 0.08);
      transition: transform 0.3s, box-shadow 0.3s;
      cursor: pointer;
    }

    .salon-card:hover {
      transform: translateY(-0.25rem);
      box-shadow: 0 0.375rem 1.125rem rgba(255, 117, 140, 0.25);
    }

    .salon-card img {
      width: 5.625rem;
      height: 5.625rem;
      border-radius: 0.75rem;
      object-fit: cover;
      margin-right: 0.9375rem;
      border: 0.125rem solid #ffb6c1;
    }

    .salon-info h3 {
      margin-bottom: 0.375rem;
      font-size: 1.125rem;
      font-weight: 600;
      color: #444;
    }

    .salon-info p {
      font-size: 0.875rem;
      color: #777;
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
  
 <!-- Marketplace content -->
  <main class="page">
 <section class="filters">
      <div class="filter-group">
        <label class="filter-label">Service</label>
        <select class="filter-select">
          <option>All</option>
          <option>Haircut</option>
          <option>Makeup</option>
          <option>Nails</option>
        </select>
      </div>

      <div class="filter-group">
        <label class="filter-label">city</label>
        <select class="filter-select">
          <option>All</option>
          <option>Riyadh</option>
          <option>Jeddah</option>
        </select>
      </div>
    </section>

    <section class="salon-list">
      <div class="salon-card" onclick="window.location.href='services.php'">
        <img src="img/pro1.jpg" alt="Salon Image">
        <div class="salon-info">
          <h3>Sarah M.</h3>
          <p>Makeup artist</p>
          <p>Riyadh, Saudi Arabia</p>
        </div>
      </div>

      <div class="salon-card">
        <img src="img/Aljohara.png" alt="Salon Image">
        <div class="salon-info">
          <h3>Aljohara alsultan</h3>
          <p>Hairstylist</p>
          <p>Jeddah, Saudi Arabia</p>
        </div>
      </div>

      <div class="salon-card">
        <img src="img/layan.png" alt="Salon Image">
        <div class="salon-info">
          <h3>Layan Abdulaziz</h3>
          <p>Nails artist</p>
          <p>Riyadh, Saudi Arabia</p>
        </div>
      </div>

      <div class="salon-card">
        <img src="img/Noura.png" alt="Salon Image">
        <div class="salon-info">
          <h3>Noura</h3>
          <p>Makeup artist</p>
          <p>Riyadh, Saudi Arabia</p>
        </div>
      </div>
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

  <script src="homeScript.js">
  </script>
  <script>
    document.addEventListener("DOMContentLoaded", () => {
  const serviceSelect = document.querySelectorAll(".filter-select")[0];
  const citySelect = document.querySelectorAll(".filter-select")[1];
  const salonCards = document.querySelectorAll(".salon-card");

 
  serviceSelect.addEventListener("change", filterSalons);
  citySelect.addEventListener("change", filterSalons);

  function filterSalons() {
    const selectedService = serviceSelect.value.toLowerCase();
    const selectedCity = citySelect.value.toLowerCase();

    salonCards.forEach(card => {
      const infoParagraphs = card.querySelectorAll(".salon-info p");
      const serviceText = infoParagraphs[0].textContent.toLowerCase(); 
      const cityText = infoParagraphs[1].textContent.toLowerCase();    

      const matchesService =
        selectedService === "all" || serviceText.includes(selectedService);
      const matchesCity =
        selectedCity === "all" || cityText.includes(selectedCity);

      // Show or hide card
      if (matchesService && matchesCity) {
        card.style.display = "block";
      } else {
        card.style.display = "none";
      }
    });
  }
});
  </script>
</body>
</html>
