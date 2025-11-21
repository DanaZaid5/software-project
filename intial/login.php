<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Log In - Glammd</title>

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800;900&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

<style>
  :root{
    --bg:#fff; --text:#0f0f12; --muted:#6b6b76;
    --accent:#111; --accent-text:#fff; --container:75rem; /* 1200px */
    --hero-image: url("img/download.jpeg");
    --hero-min-h: min(78svh, 51.25rem);
  }

  *{box-sizing:border-box}
  html,body{height:100%}

  body{
    margin:0;
    font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,"Helvetica Neue",Arial,sans-serif;
    color:var(--text); background:var(--bg);
  }

  .container{
    max-width:var(--container);
    margin-inline:auto;
    padding-inline:1.25rem; /* 20px */
    width:100%;
  }

  /* Header */
  .site-header{
    position:fixed; top:0; left:0; right:0; z-index:50;
    height:4rem; /* 64px */
    display:block;
    transition:background .25s ease, border-color .25s ease,
      backdrop-filter .25s ease, -webkit-backdrop-filter .25s ease, box-shadow .25s ease;
    background:transparent; border-bottom:none; backdrop-filter:none; -webkit-backdrop-filter:none; box-shadow:none;
  }
  .site-header.show{
    backdrop-filter:saturate(150%) blur(0.5rem); /* 8px */
    -webkit-backdrop-filter:saturate(150%) blur(0.5rem);
    background:color-mix(in srgb, white 75%, transparent);
    border-bottom:0.0625rem solid rgba(0,0,0,.06); /* 1px */
    box-shadow:0 0.0625rem 0.625rem rgba(0,0,0,.04); /* 1px 10px */
  }
  .header-inner{
    height:4rem; /* 64px */
    display:flex;
    align-items:center;
    justify-content:space-between;
  }
  .brand{
    text-decoration:none;
    color:var(--text);
    font-weight:700;
    font-size:1.375rem; /* 22px */
    letter-spacing:0.0125rem; /* 0.2px */
  }
  .nav{
    display:flex;
    gap:0.625rem; /* 10px */
  }
  .nav-link{
    text-decoration:none;
    color:var(--text);
    padding:0.625rem 0.875rem; /* 10px 14px */
    border-radius:999px;
    transition:background .2s ease;
  }
  .nav-link:hover{ background:rgba(0,0,0,.05) }
  .cta{
    text-decoration:none;
    color:var(--accent-text);
    background:var(--accent);
    padding:0.625rem 1rem; /* 10px 16px */
    border-radius:999px;
    font-weight:600;
    transition:opacity .2s ease;
  }
  .cta:hover{ opacity:.85 }

  /* Hero */
  .hero{
    position:relative;
    min-height:var(--hero-min-h);
    padding:7.5rem 0 10rem; /* 120px 0 160px */
    background-image:
      linear-gradient(rgba(255,255,255,0.25), rgba(255,255,255,0.25)),
      var(--hero-image);
    background-size:cover;
    background-position:center 20%;
    background-repeat:no-repeat;
  }
  .hero::after{
    content:""; position:absolute; inset:0;
    background:linear-gradient(to bottom,
      rgba(255,255,255,0) 0%,
      rgba(255,255,255,0) 60%,
      rgba(255,255,255,0.4) 85%,
      rgba(255,255,255,1) 100%);
    pointer-events:none;
  }
  .hero-overlay{
    position:absolute; inset:0;
    background:linear-gradient(180deg, rgba(255,255,255,.0) 0%, rgba(255,255,255,.06) 40%, rgba(255,255,255,.08) 100%);
  }
  .hero-content{
    position:relative; z-index:10;
    display:grid; grid-template-columns:1fr 1fr; gap:3.75rem; /* 60px */
    align-items:center;
  }

  /* Login card */
  .login-card{
    position:relative; z-index:10;
    background:rgba(255,255,255,0.95);
    backdrop-filter:blur(0.625rem); /* 10px */
    -webkit-backdrop-filter:blur(0.625rem);
    border-radius:1rem; /* 16px */
    padding:2.5rem; /* 40px */
    width:100%;
    box-shadow:0 0.625rem 2.5rem rgba(0,0,0,.1), 0 0.125rem 0.5rem rgba(0,0,0,.06);
    border:0.0625rem solid rgba(0,0,0,.08);
  }
  .login-title{
    margin:0 0 0.5rem;
    font-family:"Playfair Display", Georgia, "Times New Roman", serif;
    font-weight:800; font-size:2rem; /* 32px */
    line-height:1.1; letter-spacing:-0.03125rem; /* -0.5px */
  }
  .login-subtitle{ margin:0 0 1.5rem; color:var(--muted); font-size:0.9375rem; }

  .form-group{ margin-bottom:1.25rem; }
  .form-label{ display:block; margin-bottom:0.5rem; font-weight:500; font-size:0.875rem; color:var(--text); }
  .form-input{
    width:100%; padding:0.75rem 1rem; border:0.0625rem solid rgba(0,0,0,.12);
    border-radius:0.5rem; font-size:0.9375rem; font-family:inherit;
    transition:border-color .2s ease, box-shadow .2s ease; background:white;
  }
  .form-input:focus{
    outline:none;
    border-color:var(--accent);
    box-shadow:0 0 0 0.1875rem rgba(0,0,0,.05); /* 3px */
  }

  .password-toggle{ position:relative; }
  .toggle-password-btn{
    position:absolute; right:0.75rem; top:50%; transform:translateY(-50%);
    background:none; border:none; color:var(--muted); cursor:pointer; padding:0.25rem; font-size:0.875rem; transition:color .2s ease;
  }
  .toggle-password-btn:hover{ color:var(--text); }

  .form-row-inline{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin:0.5rem 0 1rem;
    gap:0.75rem;
    flex-wrap:wrap;
  }
  .remember{ display:flex; align-items:center; gap:0.5rem; color:var(--muted); font-size:0.875rem; }
  .remember input{ cursor:pointer; }
  .forgot{ font-size:0.875rem; text-decoration:none; color:var(--muted); }
  .forgot:hover{ text-decoration:underline; color:var(--text); }

  .submit-btn{
    width:100%; padding:0.875rem 1.5rem; background:var(--accent); color:var(--accent-text);
    border:none; border-radius:0.5rem; font-size:1rem; font-weight:600; cursor:pointer;
    transition:opacity .2s ease, transform .1s ease; font-family:inherit;
  }
  .submit-btn:hover{ opacity:.9; }
  .submit-btn:active{ transform:scale(0.98); }

  .divider{
    display:flex; align-items:center;
    margin:1.5rem 0;
    color:var(--muted); font-size:0.8125rem;
  }
  .divider::before,.divider::after{
    content:""; flex:1; height:0.0625rem; background:rgba(0,0,0,.1);
  }
  .divider::before{ margin-right:0.75rem; }
  .divider::after{ margin-left:0.75rem; }

  .signup-link{
    text-align:center;
    font-size:0.875rem;
    color:var(--muted);
  }
  .signup-link a{
    color:var(--accent);
    text-decoration:none;
    font-weight:600;
  }
  .signup-link a:hover{ text-decoration:underline; }

  /* Welcome side */
  .welcome-side{ position:relative; z-index:10; color:var(--text); padding:1.25rem; }
  .welcome-title{
    margin:0 0 1.25rem;
    font-family:"Playfair Display", Georgia, "Times New Roman", serif;
    font-weight:900; font-size:clamp(2.25rem,5vw,3.5rem); line-height:1.1; letter-spacing:-0.03125rem;
  }
  .welcome-text{
    font-size:1.125rem;
    line-height:1.6;
    color:var(--muted);
    margin-bottom:1.5rem;
  }
  .welcome-points{
    list-style:none;
    padding:0;
    margin:2rem 0 0;
  }
  .welcome-points li{
    padding:0.75rem 0;
    font-size:1rem;
    color:var(--text);
    display:flex;
    align-items:center;
    gap:0.75rem;
  }
  .welcome-points li::before{
    content:"✓";
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width:1.5rem; height:1.5rem;
    background:var(--accent); color:#fff;
    border-radius:50%; font-weight:600; font-size:0.875rem;
    flex-shrink:0;
  }

  /* Footer */
  .site-footer{
    background:#f8f8f8;
    border-top:0.0625rem solid rgba(0,0,0,.08);
    padding:2.5rem 0;
    font-size:0.875rem;
    color:var(--muted);
  }
  .footer-inner{
    display:flex;
    flex-wrap:wrap;
    align-items:center;
    justify-content:space-between;
    gap:0.625rem;
    text-align:center;
  }
  .footer-links a{
    color:var(--muted);
    text-decoration:none;
    margin-left:1rem;
    transition:color .2s ease;
  }
  .footer-links a:hover{ color:var(--text); }

  /* Responsive */
  @media (max-width: 60.5rem){ /* 968px */
    .hero-content{
      grid-template-columns:1fr;
      gap:2.5rem;
      padding:5rem 0 2.5rem;
    }
    .welcome-side{ order:2; text-align:center; }
    .login-card{ order:1; }
    .welcome-points{ display:inline-block; text-align:left; }
  }
  @media (max-width: 40rem){ /* 640px */
    .login-card{ padding:2rem 1.5rem; }
    .login-title{ font-size:1.75rem; }
  }
</style>

</head>
<body>

  <header id="siteHeader" class="site-header">
    <div class="container header-inner">
      <a class="brand" href="index.php">Glammd</a>
      <nav class="nav">
        <a href="login.php" class="nav-link">Log in</a>
        <a href="signup.php" class="cta">Sign up</a>
      </nav>
    </div>
  </header>

  <section class="hero">
    <div class="hero-overlay"></div>
    <div class="container hero-content">
      <!-- Left: Card -->
      <div class="login-card">
        <h1 class="login-title">Welcome back</h1>
        <p class="login-subtitle">Log in to manage bookings and services</p>

        <form id="loginForm">
          <!-- Email -->
          <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input type="text" id="email" name="email" class="form-input" placeholder="Enter your email" required />
          </div>

          <!-- Password -->
          <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <div class="password-toggle">
              <input type="password" id="password" name="password" class="form-input" placeholder="Enter your password" required />
              <button type="button" class="toggle-password-btn" id="togglePassword">Show</button>
            </div>
          </div>

          <!-- Sign in as -->
          <div class="form-group">
            <label for="role" class="form-label">Sign in as</label>
            <select id="role" name="role" class="form-input" required>
              <option value="" disabled selected>Select role</option>
              <option value="client">Client</option>
              <option value="professional">Professional</option>
            </select>
          </div>

          <div class="form-row-inline">
            <label class="remember">
              <input type="checkbox" id="rememberMe" />
              <span>Remember me</span>
            </label>
            <a href="#" class="forgot">Forgot password?</a>
          </div>

          <button type="submit" class="submit-btn">Log in</button>
        </form>

        <div class="divider">or</div>

        <div class="signup-link">
          New to Glammd? <a href="signup.php">Create an account</a>
        </div>
      </div>

      <!-- Right: Welcome copy -->
      <div class="welcome-side">
        <h2 class="welcome-title">Your beauty, organized</h2>
        <p class="welcome-text">
          Pick up where you left off — manage appointments, track offers, and delight clients.
        </p>
        <ul class="welcome-points">
          <li>Fast, secure access to your account</li>
          <li>Manage bookings &amp; offers in one place</li>
          <li>Trusted reviews to build your brand</li>
        </ul>
      </div>
    </div>
  </section>

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
    // Header blur on scroll
    const header = document.getElementById('siteHeader');
    window.addEventListener('scroll', () => {
      header.classList.toggle('show', window.pageYOffset > 50);
    });

    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    togglePassword.addEventListener('click', () => {
      const type = passwordInput.type === 'password' ? 'text' : 'password';
      passwordInput.type = type;
      togglePassword.textContent = type === 'password' ? 'Show' : 'Hide';
    });

    // Simple role-based redirect (frontend only)
    const loginForm = document.getElementById('loginForm');
    const roleSelect = document.getElementById('role');

    loginForm.addEventListener('submit', (e) => {
      if (!loginForm.checkValidity()) {
        return;
      }
      e.preventDefault();

      const role = roleSelect.value;
      if (role === 'client') {
        window.location.href = 'clientdashboard.php';
      } else {
        window.location.href = 'professionaldashboard.php';
      }
    });
  </script>
</body>
</html>
