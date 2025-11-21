<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sign Up - Glammd</title>

  <!-- Fonts: display + ui -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800;900&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  
  <!-- Embedded Styles -->
 <style>
  /* ===== Common Base Styles ===== */
  :root{
    --bg:#fff; --text:#0f0f12; --muted:#6b6b76;
    --accent:#111; --accent-text:#fff; --container:75rem; /* 1200px */
    --hero-image: url("img/download.jpeg");
    --hero-min-h: min(78svh, 51.25rem); /* 820px */
  }

  *{box-sizing:border-box}
  html,body{height:100%}
  body{
    margin:0; display:flex; flex-direction:column; min-height:100svh;
    font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,"Helvetica Neue",Arial,sans-serif;
    color:var(--text); background:var(--bg);
  }
  .container{
    max-width:var(--container);
    margin-inline:auto;
    padding-inline:1.25rem; /* 20px */
    width:100%;
  }

  /* ===== Header ===== */
  .site-header{
    position:fixed; top:0; left:0; right:0; z-index:50;
    height:4rem; /* 64px */
    display:block;
    transition:
      background .25s ease, border-color .25s ease,
      backdrop-filter .25s ease, -webkit-backdrop-filter .25s ease, box-shadow .25s ease;
    background: transparent;
    border-bottom: none;
    backdrop-filter: none;
    -webkit-backdrop-filter: none;
    box-shadow: none;
  }

  .site-header.show{
    backdrop-filter: saturate(150%) blur(0.5rem); /* 8px */
    -webkit-backdrop-filter: saturate(150%) blur(0.5rem);
    background: color-mix(in srgb, white 75%, transparent);
    border-bottom: 0.0625rem solid rgba(0,0,0,.06); /* 1px */
    box-shadow: 0 0.0625rem 0.625rem rgba(0,0,0,.04); /* 1px 10px */
  }

  .header-inner{
    height:4rem; /* 64px */
    display:flex; align-items:center; justify-content:space-between;
  }
  .brand{
    text-decoration:none; color:var(--text); font-weight:700;
    font-size:1.375rem; /* 22px */
    letter-spacing:0.0125rem; /* 0.2px */
  }
  .nav{ display:flex; gap:0.625rem } /* 10px */
  .nav-link{
    text-decoration:none; color:var(--text);
    padding:0.625rem 0.875rem; /* 10px 14px */
    border-radius:999px;
    transition: background .2s ease;
  }
  .nav-link:hover{ background: rgba(0,0,0,.05) }
  .cta{
    text-decoration:none; color:var(--accent-text); background:var(--accent);
    padding:0.625rem 1rem; /* 10px 16px */
    border-radius:999px; font-weight:600; transition: opacity .2s ease;
  }
  .cta:hover{ opacity: .85 }

  /* ===== Hero Section ===== */
  .hero{
    position: relative;
    min-height: auto;
    padding: 7.5rem 0 5rem; /* 120px 0 80px */

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

  .hero-overlay{
    position:absolute; inset:0;
    background: linear-gradient(180deg, rgba(255,255,255,.0) 0%, rgba(255,255,255,.06) 40%, rgba(255,255,255,.08) 100%);
  }

  .hero-content{
    position: relative;
    z-index: 10;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3.75rem; /* 60px */
    align-items: center;
  }

  /* ===== Sign Up Form Card ===== */
  .signup-card{
    position: relative;
    z-index: 10;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(0.625rem); /* 10px */
    -webkit-backdrop-filter: blur(0.625rem);
    border-radius: 1rem; /* 16px */
    padding: 2.5rem; /* 40px */
    width: 100%;
    box-shadow: 0 0.625rem 2.5rem rgba(0,0,0,.1), 0 0.125rem 0.5rem rgba(0,0,0,.06);
    border: 0.0625rem solid rgba(0,0,0,.08); /* 1px */
  }

  /* ===== Welcome Message Side ===== */
  .welcome-side{
    position: relative;
    z-index: 10;
    color: var(--text);
    padding: 1.25rem; /* 20px */
  }

  .welcome-title{
    margin: 0 0 1.25rem; /* 20px */
    font-family: "Playfair Display", Georgia, "Times New Roman", serif;
    font-weight: 900;
    font-size: clamp(2.25rem, 5vw, 3.5rem); /* 36px–56px */
    line-height: 1.1;
    letter-spacing: -0.03125rem; /* -0.5px */
  }

  .welcome-text{
    font-size: 1.125rem; /* 18px */
    line-height: 1.6;
    color: var(--muted);
    margin-bottom: 1.5rem; /* 24px */
  }

  .welcome-features{
    list-style: none;
    padding: 0;
    margin: 2rem 0 0; /* 32px */
  }

  .welcome-features li{
    padding: 0.75rem 0; /* 12px 0 */
    font-size: 1rem; /* 16px */
    color: var(--text);
    display: flex;
    align-items: center;
    gap: 0.75rem; /* 12px */
  }

  .welcome-features li::before{
    content: "✓";
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 1.5rem; /* 24px */
    height: 1.5rem; /* 24px */
    background: var(--accent);
    color: white;
    border-radius: 50%;
    font-weight: 600;
    font-size: 0.875rem; /* 14px */
    flex-shrink: 0;
  }

  .signup-title{
    margin: 0 0 0.5rem; /* 8px */
    font-family: "Playfair Display", Georgia, "Times New Roman", serif;
    font-weight: 800;
    font-size: 2rem; /* 32px */
    line-height: 1.1;
    letter-spacing: -0.03125rem; /* -0.5px */
  }

  .signup-subtitle{
    margin: 0 0 2rem; /* 32px */
    color: var(--muted);
    font-size: 0.9375rem; /* 15px */
  }

  .form-group{
    margin-bottom: 1.25rem; /* 20px */
  }

  .form-label{
    display: block;
    margin-bottom: 0.5rem; /* 8px */
    font-weight: 500;
    font-size: 0.875rem; /* 14px */
    color: var(--text);
  }

  .form-input{
    width: 100%;
    padding: 0.75rem 1rem; /* 12px 16px */
    border: 0.0625rem solid rgba(0,0,0,.12); /* 1px */
    border-radius: 0.5rem; /* 8px */
    font-size: 0.9375rem; /* 15px */
    font-family: inherit;
    transition: border-color .2s ease, box-shadow .2s ease;
    background: white;
  }

  .form-input:focus{
    outline: none;
    border-color: var(--accent);
    box-shadow: 0 0 0 0.1875rem rgba(0,0,0,.05); /* 3px */
  }

  .form-input.error{
    border-color: #dc2626;
  }

  .error-message{
    display: none;
    margin-top: 0.375rem; /* 6px */
    font-size: 0.8125rem; /* 13px */
    color: #dc2626;
  }

  .error-message.show{
    display: block;
  }

  .password-toggle{
    position: relative;
  }

  .toggle-password-btn{
    position: absolute;
    right: 0.75rem; /* 12px */
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: var(--muted);
    cursor: pointer;
    padding: 0.25rem; /* 4px */
    font-size: 0.875rem; /* 14px */
    transition: color .2s ease;
  }

  .toggle-password-btn:hover{
    color: var(--text);
  }

  .form-checkbox-group{
    display: flex;
    align-items: flex-start;
    gap: 0.625rem; /* 10px */
    margin-bottom: 1.5rem; /* 24px */
  }

  .form-checkbox{
    margin-top: 0.1875rem; /* 3px */
    cursor: pointer;
  }

  .form-checkbox-label{
    font-size: 0.875rem; /* 14px */
    color: var(--muted);
    line-height: 1.5;
    cursor: pointer;
  }

  .form-checkbox-label a{
    color: var(--accent);
    text-decoration: none;
    font-weight: 500;
  }

  .form-checkbox-label a:hover{
    text-decoration: underline;
  }

  .user-type-group{
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem; /* 12px */
    margin-bottom: 1.25rem; /* 20px */
  }

  .user-type-option{
    position: relative;
  }

  .user-type-radio{
    position: absolute;
    opacity: 0;
    pointer-events: none;
  }

  .user-type-label{
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 1.25rem 1rem; /* 20px 16px */
    border: 0.125rem solid rgba(0,0,0,.12); /* 2px */
    border-radius: 0.5rem; /* 8px */
    cursor: pointer;
    transition: all .2s ease;
    background: white;
    text-align: center;
  }

  .user-type-radio:checked + .user-type-label{
    border-color: var(--accent);
    background: rgba(0,0,0,.02);
  }

  .user-type-label:hover{
    border-color: rgba(0,0,0,.25);
  }

  .user-type-icon{
    margin-bottom: 0.5rem; /* 8px */
    color: var(--text);
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .user-type-radio:checked + .user-type-label .user-type-icon{
    color: var(--accent);
  }

  .user-type-title{
    font-weight: 600;
    font-size: 0.9375rem; /* 15px */
    color: var(--text);
    margin-bottom: 0.25rem; /* 4px */
  }

  .user-type-desc{
    font-size: 0.75rem; /* 12px */
    color: var(--muted);
    line-height: 1.3;
  }

  .submit-btn{
    width: 100%;
    padding: 0.875rem 1.5rem; /* 14px 24px */
    background: var(--accent);
    color: var(--accent-text);
    border: none;
    border-radius: 0.5rem; /* 8px */
    font-size: 1rem; /* 16px */
    font-weight: 600;
    cursor: pointer;
    transition: opacity .2s ease, transform .1s ease;
    font-family: inherit;
  }

  .submit-btn:hover{
    opacity: .9;
  }

  .submit-btn:active{
    transform: scale(0.98);
  }

  .submit-btn:disabled{
    opacity: .5;
    cursor: not-allowed;
  }

  .divider{
    display: flex;
    align-items: center;
    margin: 1.5rem 0; /* 24px */
    color: var(--muted);
    font-size: 0.8125rem; /* 13px */
  }

  .divider::before,
  .divider::after{
    content: "";
    flex: 1;
    height: 0.0625rem; /* 1px */
    background: rgba(0,0,0,.1);
  }

  .divider::before{
    margin-right: 0.75rem; /* 12px */
  }

  .divider::after{
    margin-left: 0.75rem; /* 12px */
  }

  .signin-link{
    text-align: center;
    font-size: 0.875rem; /* 14px */
    color: var(--muted);
  }

  .signin-link a{
    color: var(--accent);
    text-decoration: none;
    font-weight: 600;
  }

  .signin-link a:hover{
    text-decoration: underline;
  }

  /* ===== Footer ===== */
  .site-footer{ 
    flex-shrink:0; 
    background:#f8f8f8; 
    border-top:0.0625rem solid rgba(0,0,0,.08); /* 1px */
    padding:2.5rem 0; /* 40px */
    font-size:0.875rem; /* 14px */
    color:var(--muted);
    margin-top: auto;
  }
  .footer-inner{ 
    display:flex; 
    flex-wrap:wrap; 
    align-items:center; 
    justify-content:space-between; 
    gap:0.625rem; /* 10px */
    text-align:center;
  }
  .footer-links a{ 
    color:var(--muted); 
    text-decoration:none; 
    margin-left:1rem; /* 16px */
    transition:color .2s ease;
  }
  .footer-links a:hover{ 
    color:var(--text);
  }

  /* ===== Responsive ===== */
  @media (max-width: 60.5rem) { /* 968px */
    .hero-content{
      grid-template-columns: 1fr;
      gap: 2.5rem; /* 40px */
      padding: 5rem 0 2.5rem; /* 80px 0 40px */
    }

    .welcome-side{
      order: 2;
      text-align: center;
    }

    .signup-card{
      order: 1;
    }

    .welcome-features{
      display: inline-block;
      text-align: left;
    }
  }

  @media (max-width: 40rem) { /* 640px */
    .signup-card{
      padding: 2rem 1.5rem; /* 32px 24px */
    }

    .signup-title{
      font-size: 1.75rem; /* 28px */
    }

    .user-type-group{
      grid-template-columns: 1fr;
    }
  }
</style>

</head>
<body>
  <!-- Header -->
  <header id="siteHeader" class="site-header">
    <div class="container header-inner">
      <a class="brand" href="index.php">Glammd</a>
      <nav class="nav">
        <a href="login.php" class="nav-link">Log in</a>
        <a href="signup.php" class="cta">Sign up</a>
      </nav>
    </div>
  </header>

  <!-- Hero with Sign Up Form -->
  <section class="hero">
    <div class="hero-overlay"></div>
    <div class="container hero-content">
      <div class="signup-card">
        <h1 class="signup-title">Create your account</h1>
        <p class="signup-subtitle">Join our community of clients and beauty professionals</p>
        
        <form id="signupForm" novalidate>
          <div class="form-group">
            <label class="form-label">I am a</label>
            <div class="user-type-group">
              <div class="user-type-option">
                <input 
                  type="radio" 
                  id="userTypeClient" 
                  name="userType" 
                  value="client" 
                  class="user-type-radio"
                  checked
                  required
                />
                <label for="userTypeClient" class="user-type-label">
                  <div class="user-type-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                      <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                  </div>
                  <div class="user-type-title">Client</div>
                  <div class="user-type-desc">Book services</div>
                </label>
              </div>
              <div class="user-type-option">
                <input 
                  type="radio" 
                  id="userTypeProfessional" 
                  name="userType" 
                  value="professional" 
                  class="user-type-radio"
                  required
                />
                <label for="userTypeProfessional" class="user-type-label">
                  <div class="user-type-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                      <path d="M2 17l10 5 10-5"></path>
                      <path d="M2 12l10 5 10-5"></path>
                    </svg>
                  </div>
                  <div class="user-type-title">Professional</div>
                  <div class="user-type-desc">Offer services</div>
                </label>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="fullName" class="form-label">Full Name</label>
            <input 
              type="text" 
              id="fullName" 
              name="fullName" 
              class="form-input" 
              placeholder="Enter your full name"
              required
            />
            <div class="error-message" id="fullNameError">Please enter your full name</div>
          </div>

          <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input 
              type="email" 
              id="email" 
              name="email" 
              class="form-input" 
              placeholder="Enter your email"
              required
            />
            <div class="error-message" id="emailError">Please enter a valid email address</div>
          </div>

          <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <div class="password-toggle">
              <input 
                type="password" 
                id="password" 
                name="password" 
                class="form-input" 
                placeholder="Create a password"
                required
              />
              <button type="button" class="toggle-password-btn" id="togglePassword">
                Show
              </button>
            </div>
            <div class="error-message" id="passwordError">Password must be at least 8 characters</div>
          </div>

          <div class="form-group">
            <label for="confirmPassword" class="form-label">Confirm Password</label>
            <div class="password-toggle">
              <input 
                type="password" 
                id="confirmPassword" 
                name="confirmPassword" 
                class="form-input" 
                placeholder="Confirm your password"
                required
              />
              <button type="button" class="toggle-password-btn" id="toggleConfirmPassword">
                Show
              </button>
            </div>
            <div class="error-message" id="confirmPasswordError">Passwords do not match</div>
          </div>

          <div class="form-checkbox-group">
            <input 
              type="checkbox" 
              id="terms" 
              name="terms" 
              class="form-checkbox"
              required
            />
            <label for="terms" class="form-checkbox-label">
              I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
            </label>
          </div>

          <button type="submit" class="submit-btn">Create Account</button>
        </form>

        <div class="divider">or</div>

        <div class="signin-link">
          Already have an account? <a href="login.php">Log in</a>
        </div>
      </div>

      <div class="welcome-side">
        <h2 class="welcome-title">Your beauty journey starts here</h2>
        <p class="welcome-text">
          Join thousands of clients and professionals who trust Glammd to connect, book, and deliver exceptional beauty experiences.
        </p>
        <ul class="welcome-features">
          <li>Connect clients with beauty professionals</li>
          <li>Book and manage appointments easily</li>
          <li>Secure payments and trusted reviews</li>
          <li>Grow your business or find your perfect service</li>
        </ul>
      </div>
    </div>
  </section>

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

  <!-- Embedded JavaScript -->
  <script>
    // Header scroll effect
    const header = document.getElementById('siteHeader');
    let lastScroll = 0;

    window.addEventListener('scroll', () => {
      const currentScroll = window.pageYOffset;
      
      if (currentScroll > 50) {
        header.classList.add('show');
      } else {
        header.classList.remove('show');
      }
      
      lastScroll = currentScroll;
    });

    // Password toggle functionality
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    const confirmPasswordInput = document.getElementById('confirmPassword');

    togglePassword.addEventListener('click', () => {
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
      togglePassword.textContent = type === 'password' ? 'Show' : 'Hide';
    });

    toggleConfirmPassword.addEventListener('click', () => {
      const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      confirmPasswordInput.setAttribute('type', type);
      toggleConfirmPassword.textContent = type === 'password' ? 'Show' : 'Hide';
    });

    // Form validation and submission
    const signupForm = document.getElementById('signupForm');
    const fullNameInput = document.getElementById('fullName');
    const emailInput = document.getElementById('email');
    const termsCheckbox = document.getElementById('terms');

    // Email validation regex
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    // Clear error on input
    [fullNameInput, emailInput, passwordInput, confirmPasswordInput].forEach(input => {
      input.addEventListener('input', () => {
        input.classList.remove('error');
        const errorElement = document.getElementById(`${input.id}Error`);
        if (errorElement) {
          errorElement.classList.remove('show');
        }
      });
    });

    signupForm.addEventListener('submit', (e) => {
      e.preventDefault();
      
      let isValid = true;

      // Validate full name
      if (fullNameInput.value.trim() === '') {
        fullNameInput.classList.add('error');
        document.getElementById('fullNameError').classList.add('show');
        isValid = false;
      }

      // Validate email
      if (!emailRegex.test(emailInput.value.trim())) {
        emailInput.classList.add('error');
        document.getElementById('emailError').classList.add('show');
        isValid = false;
      }

      // Validate password
      if (passwordInput.value.length < 8) {
        passwordInput.classList.add('error');
        document.getElementById('passwordError').classList.add('show');
        isValid = false;
      }

      // Validate password match
      if (passwordInput.value !== confirmPasswordInput.value) {
        confirmPasswordInput.classList.add('error');
        document.getElementById('confirmPasswordError').classList.add('show');
        isValid = false;
      }

      // Validate terms checkbox
      if (!termsCheckbox.checked) {
        alert('Please agree to the Terms of Service and Privacy Policy');
        isValid = false;
      }

      if (isValid) {
        // Get selected user type
        const userType = document.querySelector('input[name="userType"]:checked').value;
        
        // Form is valid - you can submit to your backend here
        console.log('Form submitted successfully!');
        console.log({
          userType: userType,
          fullName: fullNameInput.value,
          email: emailInput.value,
          password: passwordInput.value
        });
        
        // Show success message
        alert(`Account created successfully! Welcome to Glammd as a ${userType}.`);
        
        // Redirect based on user type
        if (userType === 'client') {
          window.location.href = 'clientdashboard.php';
        } else if (userType === 'professional') {
          window.location.href = 'professionaldashboard.php';
        }
      }
    });
  </script>
</body>
</html>
