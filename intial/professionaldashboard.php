<!DOCTYPE html>
<html lang="en" class="has-solid-header">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>My Dashboard</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800;900&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="common.css">
<style>
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
  .nav-link{
    text-decoration:none; color:var(--text);
    padding:10px 14px; border-radius:999px;
  }

  main.page{ padding-top:64px }
  .wrap{ max-width:1120px; margin:28px auto 80px; padding:0 16px; }

  /* ===== Professional Profile ===== */
  .professional-profile{
    background:var(--panel);
    border:1px solid var(--line);
    border-radius:var(--r-md);
    box-shadow:var(--sh-2);
    padding:20px;
    margin-bottom:16px;
    display:flex;
    gap:20px;
    align-items:center;
  }
  .professional-photo{
    width:96px;
    height:96px;
    border-radius:50%;
    overflow:hidden;
    flex-shrink:0;
    box-shadow:0 4px 14px rgba(0,0,0,.12);
  }
  .professional-photo img{
    width:100%;
    height:100%;
    object-fit:cover;
    border-radius:inherit;
  }
  .professional-info{
    flex:1;
    min-width:0;
  }
  .profile-header-row{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap:12px;
    margin-bottom:6px;
  }
  .professional-name{
    margin:0 0 4px;
    font-size:20px;
    font-weight:700;
    letter-spacing:.3px;
  }
  .professional-title{
    margin:0;
    color:var(--muted);
    font-size:14px;
  }
  .edit-profile-btn{
    white-space:nowrap;
  }
  .btn-xs{
    padding:6px 10px;
    font-size:12px;
    border-radius:999px;
  }
  .professional-stats{
    display:flex;
    flex-wrap:wrap;
    gap:16px;
    margin:8px 0;
  }
  .stat-item{
    display:flex;
    flex-direction:column;
    gap:2px;
    min-width:90px;
  }
  .stat-value{
    font-weight:600;
    font-size:15px;
  }
  .stat-label{
    font-size:11px;
    text-transform:uppercase;
    letter-spacing:1px;
    color:var(--muted);
  }
  .professional-bio{
    margin:4px 0 0;
    color:var(--muted);
    font-size:13px;
    max-width:52ch;
  }

  @media (max-width:768px){
    .professional-profile{
      flex-direction:column;
      align-items:flex-start;
    }
    .profile-header-row{
      flex-direction:column;
      align-items:flex-start;
    }
    .edit-profile-btn{
      align-self:flex-start;
    }
  }

  .tabs{ display:flex; gap:8px; margin:18px 0; flex-wrap:wrap; }
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
  input[type="text"], input[type="number"], textarea, select, input[type="datetime-local"]{
    width:100%; padding:12px 14px; border:1px solid var(--line); border-radius:var(--r-sm); background:#fff; outline:none; min-width:0;
  }
  textarea{ min-height:100px; resize:vertical }
  .btn{
    appearance:none; border:1px solid var(--ink); background:var(--ink); color:#fff;
    padding:10px 14px; border-radius:999px; cursor:pointer; font-weight:600;
    transition:transform .1s ease, opacity .2s ease;
  }
  .btn:active{ transform:translateY(1px) }
  .btn.ghost{ background:transparent; color:var(--ink); border-color:var(--line); }
  .btn.success{ background:var(--success); border-color:var(--success) }
  .btn.danger{ background:var(--danger); border-color:var(--danger) }

  .list{ display:grid; gap:12px; margin-top:6px; }
  .item{
    display:grid; grid-template-columns:1fr auto; gap:16px; align-items:center;
    padding:14px; border:1px solid var(--line); border-radius:var(--r-sm); background:#fff;
  }
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
  .tag{
    position:relative; display:inline-flex; align-items:center;
    border:1px solid var(--line); border-radius:999px; background:#fff;
    padding:8px 12px; font-size:13px; color:var(--ink); cursor:pointer;
    transition:transform .1s ease;
  }
  .tag input{ position:absolute; opacity:0; pointer-events:none; }
  .tag:has(input:checked){ border-color:var(--ink); background:var(--ink); color:#fff; }
  .tag:active{ transform:translateY(1px) }

  .foot-hint{ display:flex; justify-content:center; margin-top:20px; color:var(--muted); font-size:12px; }

  /* ---- NOTES (details) ---- */
  details.notes{
    border:1px dashed var(--line);
    border-radius:999px;
    padding:6px 10px;
    display:inline-block;
    background:#fff;
  }
  details.notes > summary{
    list-style:none;
    cursor:pointer;
    font-size:12px;
    color:var(--ink);
    outline:none;
  }
  details.notes > summary::-webkit-details-marker{ display:none }
  details.notes[open]{ background:color-mix(in srgb, var(--panel) 70%, #f5f5f5) }
  .note-text{ margin-top:8px; font-size:13px; color:var(--muted); max-width:48ch; }
</style>
</head>
<body class="has-solid-header">

  <header id="siteHeader" class="site-header">
    <div class="container header-inner">
      <a class="brand" href="index.php">Glammd</a>
      <nav class="nav">
        <a href="index.php" class="nav-link">Log out</a>
      </nav>
    </div>
  </header>

  <main class="page">
    <div class="wrap">

      <!-- Professional profile card + Edit button -->
      <section class="professional-profile">
        <div class="professional-photo">
          <img src="img/pro1.jpg" alt="Sarah M." loading="lazy">
        </div>
        <div class="professional-info">
          <div class="profile-header-row">
            <div>
              <h1 class="professional-name" id="proName">Sarah M.</h1>
              <p class="professional-title" id="proTitle">Professional Makeup Artist &amp; Beauty Specialist</p>
            </div>
            <button class="btn ghost btn-xs edit-profile-btn" type="button" id="editProfileBtn">
              Edit profile
            </button>
          </div>

          <div class="professional-stats">
            <div class="stat-item">
              <span class="stat-value" id="proRating">★ 4.9</span>
              <span class="stat-label">Rating</span>
            </div>
            <div class="stat-item">
              <span class="stat-value" id="proReviews">127</span>
              <span class="stat-label">Reviews</span>
            </div>
            <div class="stat-item">
              <span class="stat-value" id="proServices">8</span>
              <span class="stat-label">Services</span>
            </div>
            <div class="stat-item">
              <span class="stat-value" id="proExperience">5+ years</span>
              <span class="stat-label">Experience</span>
            </div>
          </div>

          <p class="professional-bio" id="proBio">
            Certified makeup artist specializing in bridal, evening, and special occasion looks.
            Passionate about enhancing natural beauty and creating flawless results that last,
            using premium products and techniques tailored to each client.
          </p>
        </div>
      </section>

      <!-- Profile settings panel -->
      <section class="panel" id="profile-settings" style="margin-bottom:24px;">
        <h2>Profile settings</h2>
<form id="profileForm">
  <div class="form-row inline">
    <div>
      <label for="profileName">Name</label>
      <input type="text" id="profileName" placeholder="Your name" value="Sarah M.">
    </div>
    <div>
      <label for="profileTitle">Title</label>
      <input type="text" id="profileTitle" placeholder="Your professional title" value="Professional Makeup Artist &amp; Beauty Specialist">
    </div>
  </div>

  <!-- Profile photo upload -->
  <div class="form-row">
    <div>
      <label for="profilePhoto">Profile photo</label>
      <input type="file" id="profilePhoto" accept="image/*">
    </div>
  </div>

  <div class="form-row">
    <div>
      <label for="profileBio">Bio</label>
      <textarea id="profileBio" placeholder="Tell clients about your experience, style, and what you specialize in.">Certified makeup artist specializing in bridal, evening, and special occasion looks.
Passionate about enhancing natural beauty and creating flawless results that last,
using premium products and techniques tailored to each client.</textarea>
    </div>
  </div>

  <button class="btn" type="submit">Save profile</button>
</form>

      </section>

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
            <div class="panel">
              <h2>Add a service</h2>

              <div class="form-row inline">
                <div>
                  <label>Service</label>
                  <select>
                    <option>Hair</option>
                    <option>Makeup</option>
                    <option>Skincare</option>
                    <option>Bodycare</option>
                  </select>
                </div>
                <div>
                  <label>Display Title</label>
                  <input type="text" placeholder="e.g., ‘Signature Glow’" />
                </div>
              </div>

              <div class="form-row inline">
                <div>
                  <label>Price (SAR)</label>
                  <input type="number" min="0" step="1" placeholder="1200" />
                </div>
                <div>
                  <label>Duration (min)</label>
                  <input type="number" min="15" step="15" placeholder="90" />
                </div>
              </div>

              <div class="form-row">
                <div>
                  <label>Description</label>
                  <textarea placeholder="Describe what’s included, prep notes, and aftercare."></textarea>
                </div>
              </div>

              <div class="form-row">
                <label>Suitable for</label>
                <div class="tags">
                  <label class="tag"><input type="checkbox" /> Thick Hair</label>
                  <label class="tag"><input type="checkbox" /> Thin Hair</label>
                  <label class="tag"><input type="checkbox" /> Oily Hair</label>
                  <label class="tag"><input type="checkbox" /> Dry Hair</label>
                  <label class="tag"><input type="checkbox" /> Curly Hair</label>
                  <label class="tag"><input type="checkbox" /> Wavy Hair</label>
                  <label class="tag"><input type="checkbox" /> Straight Hair</label>
                  <label class="tag"><input type="checkbox" /> Sensitive Skin</label>
                  <label class="tag"><input type="checkbox" /> Dry Skin</label>
                  <label class="tag"><input type="checkbox" /> Oily Skin</label>
                </div>
              </div>

              <button class="btn">Add Service</button>
            </div>

            <div class="panel">
              <h2>Your services</h2>
              <div class="list">
                <div class="item">
                  <div class="meta">
                    <div class="title">Signature Glow</div>
                    <div class="muted">Category <strong>Makeup</strong> · Duration <strong>90 min</strong></div>
                  </div>
                  <div class="actions">
                    <span class="price">SAR 1,200</span>
                    <button class="btn danger">Delete</button>
                  </div>
                </div>
                <div class="item">
                  <div class="meta">
                    <div class="title">Evening Glam</div>
                    <div class="muted">Category <strong>Makeup</strong> · Duration <strong>60 min</strong></div>
                  </div>
                  <div class="actions">
                    <span class="price">SAR 650</span>
                    <button class="btn danger">Delete</button>
                  </div>
                </div>
                <div class="item">
                  <div class="meta">
                    <div class="title">Soft Waves</div>
                    <div class="muted">Category <strong>Hair</strong> · Duration <strong>45 min</strong></div>
                  </div>
                  <div class="actions">
                    <span class="price">SAR 300</span>
                    <button class="btn danger">Delete</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- OFFERS -->
        <section id="offers" class="section">
          <div class="panel" style="margin-bottom:16px;">
            <h2>Pending offers</h2>
            <table class="table">
              <thead>
                <tr>
                  <th>Client</th>
                  <th>Service</th>
                  <th>Requested Time</th>
                  <th>Notes</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Aisha K.</td>
                  <td><span class="chip">Makeup</span></td>
                  <td>2025-11-14 17:00</td>
                  <td>
                    <details class="notes">
                      <summary>View</summary>
                      <div class="note-text">Soft glam; please avoid heavy contour. I’m allergic to lavender oil.</div>
                    </details>
                  </td>
                  <td class="actions">
                    <button class="btn success">Accept</button>
                    <button class="btn ghost">Decline</button>
                  </td>
                </tr>
                <tr>
                  <td>Lama S.</td>
                  <td><span class="chip">Makeup</span></td>
                  <td>2025-11-09 19:30</td>
                  <td>
                    <details class="notes">
                      <summary>View</summary>
                      <div class="note-text">Smokey eye, nude lip. I have sensitive skin—prefer fragrance-free products.</div>
                    </details>
                  </td>
                  <td class="actions">
                    <button class="btn success">Accept</button>
                    <button class="btn ghost">Decline</button>
                  </td>
                </tr>
                <tr>
                  <td>Noor A.</td>
                  <td><span class="chip">Hair</span></td>
                  <td>2025-11-03 16:00</td>
                  <td>
                    <details class="notes">
                      <summary>View</summary>
                      <div class="note-text">Loose waves; bring anti-frizz if possible. Event is outdoors.</div>
                    </details>
                  </td>
                  <td class="actions">
                    <button class="btn success">Accept</button>
                    <button class="btn ghost">Decline</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="panel">
            <h2>Confirmed bookings</h2>
            <table class="table">
              <thead>
                <tr>
                  <th>Client</th>
                  <th>Service</th>
                  <th>Date &amp; Time</th>
                  <th>Notes</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Reem H.</td>
                  <td><span class="chip">Makeup</span></td>
                  <td>2025-11-06 18:00</td>
                  <td>
                    <details class="notes" open>
                      <summary>View</summary>
                      <div class="note-text">Matte base; please cover acne scars gently. Bring oil-control primer.</div>
                    </details>
                  </td>
                  <td><span class="chip">Confirmed</span></td>
                </tr>
                <tr>
                  <td>Maha A.</td>
                  <td><span class="chip">Hair</span></td>
                  <td>2025-11-10 15:30</td>
                  <td>
                    <details class="notes">
                      <summary>View</summary>
                      <div class="note-text">Half-up style with curls. Avoid hairspray with strong scent.</div>
                    </details>
                  </td>
                  <td><span class="chip">Confirmed</span></td>
                </tr>
                <tr>
                  <td>Joud K.</td>
                  <td><span class="chip">Skincare</span></td>
                  <td>2025-11-12 12:00</td>
                  <td>
                    <details class="notes">
                      <summary>View</summary>
                      <div class="note-text">Hydrating facial; allergic to tea tree. Patch test first, please.</div>
                    </details>
                  </td>
                  <td><span class="chip">Confirmed</span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>

        <!-- REVIEWS -->
        <section id="reviews" class="section">
          <div class="panel">
            <h2>Reviews</h2>
            <div class="reviews">
              <article class="review">
                <div class="review-head">
                  <div class="reviewer">
                    <strong>Sarah M.</strong>
                    <span class="service-for">for Makeup — Signature Glow</span>
                  </div>
                  <div class="stars">★★★★★</div>
                </div>
                <p>Flawless finish and super professional. Makeup lasted all night.</p>
              </article>

              <article class="review">
                <div class="review-head">
                  <div class="reviewer">
                    <strong>Dana R.</strong>
                    <span class="service-for">for Makeup — Evening Glam</span>
                  </div>
                  <div class="stars">★★★★☆</div>
                </div>
                <p>Loved the look! Arrived on time and very friendly.</p>
              </article>

              <article class="review">
                <div class="review-head">
                  <div class="reviewer">
                    <strong>Hessa L.</strong>
                    <span class="service-for">for Hair — Soft Waves</span>
                  </div>
                  <div class="stars">★★★★★</div>
                </div>
                <p>Exactly what I wanted. Soft &amp; elegant waves, perfect for photos.</p>
              </article>

              <article class="review">
                <div class="review-head">
                  <div class="reviewer">
                    <strong>Layan T.</strong>
                    <span class="service-for">for Skin — Skin Prep</span>
                  </div>
                  <div class="stars">★★★★★</div>
                </div>
                <p>Great prep — Skin looked smooth and hydrated under makeup.</p>
              </article>
            </div>
          </div>
        </section>

      </div>
    </div>
  </main>
  
    <!-- Footer (from index.html) -->
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
  // Smooth scroll to profile settings
  const editBtn = document.getElementById('editProfileBtn');
  const profileSettings = document.getElementById('profile-settings');

  if (editBtn && profileSettings) {
    editBtn.addEventListener('click', () => {
      profileSettings.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
  }

  // Simple front-end profile update
  const profileForm = document.getElementById('profileForm');
  const proName = document.getElementById('proName');
  const proTitle = document.getElementById('proTitle');
  const proBio = document.getElementById('proBio');
  const inputName = document.getElementById('profileName');
  const inputTitle = document.getElementById('profileTitle');
  const textareaBio = document.getElementById('profileBio');

  const defaultName = proName.textContent.trim();
  const defaultTitle = proTitle.textContent.trim();
  const defaultBio = proBio.textContent.trim();

  if (profileForm) {
    profileForm.addEventListener('submit', (e) => {
      e.preventDefault();

      const newName = (inputName.value || '').trim();
      const newTitle = (inputTitle.value || '').trim();
      const newBio = (textareaBio.value || '').trim();

      proName.textContent = newName || defaultName;
      proTitle.textContent = newTitle || defaultTitle;
      proBio.textContent = newBio || defaultBio;

      // Tiny visual feedback
      const btn = profileForm.querySelector('button[type="submit"]');
      btn.textContent = 'Saved ✔';
      setTimeout(() => {
        btn.textContent = 'Save profile';
      }, 1500);
    });
  }

  // Photo upload preview
  const photoInput = document.getElementById('profilePhoto');
  const proPhoto = document.getElementById('proPhoto');
  const photoTrigger = document.querySelector('.professional-photo');

  if (photoTrigger && photoInput && proPhoto) {
    photoTrigger.addEventListener('click', () => photoInput.click());

    photoInput.addEventListener('change', (e) => {
      const file = e.target.files[0];
      if (!file) return;
      const reader = new FileReader();
      reader.onload = (ev) => {
        proPhoto.src = ev.target.result;
      };
      reader.readAsDataURL(file);
    });
  }
</script>

</body>
</html>
