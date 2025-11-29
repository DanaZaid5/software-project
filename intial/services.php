<?php
// Start session
session_start();

// Database connection
require_once 'db.php';

// Get all services with professional information
$query = "SELECT s.*, p.bio, p.img, u.name as professional_name 
          FROM Service s 
          JOIN Professional p ON s.professional_id = p.professional_id 
          JOIN User u ON p.professional_id = u.user_id";
$result = mysqli_query($conn, $query);

// Store services in an array
$services = [];
$professionals = [];

while ($row = mysqli_fetch_assoc($result)) {
    $services[] = $row;
    // Store professional info if not already stored
    if (!isset($professionals[$row['professional_id']])) {
        $professionals[$row['professional_id']] = [
            'name' => $row['professional_name'],
            'bio' => $row['bio'],
            'img' => $row['img']
        ];
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Services - Glammd</title>

  <!-- Fonts: display + ui -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800;900&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="common.css">
  
  <!-- Page Styles -->
 <style>
  /* Page background */
  body{ background:#fafafa; }

  /* ---- BREADCRUMBS ---- */
  .breadcrumbs{ 
    font-size:0.8125rem; /* 13px */
    color:var(--muted); 
    margin-bottom:1.5rem; /* 24px */
    padding-top:1.25rem; /* 20px */
  }
  .breadcrumbs a{ color:var(--muted); text-decoration:none; transition:color .2s ease; }
  .breadcrumbs a:hover{ color:var(--text) }
  .breadcrumbs span{ margin:0 0.375rem; } /* 6px */

  /* ---- FILTERS ---- */
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
    letter-spacing: 0.0625rem; /* 1px */
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
    appearance: none;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%23888' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 0.5rem center;
    background-size: 1.25em 1.25em;
    padding-right: 2rem;
  }

  .filter-select:hover {
    box-shadow: inset 0 0.125rem 0.375rem rgba(0,0,0,0.15);
  }

  .filter-select:focus {
    outline: none;
    box-shadow: 0 0 0 0.125rem rgba(255, 117, 140, 0.3); /* 2px */
  }

  /* ---- PROFESSIONAL PROFILE ---- */
  .professional-profile{
    background: white;
    border: 0.0625rem solid #eaeaea; /* 1px */
    border-radius: 0.75rem; /* 12px */
    padding: 1.5rem; /* 24px */
    margin-bottom: 2rem; /* 32px */
    box-shadow: 0 0.125rem 0.5rem rgba(0,0,0,.04); /* 2px 8px */
    display: flex;
    gap: 1.5rem; /* 24px */
    align-items: center;
  }

  .professional-photo{
    width: 7.5rem; /* 120px */
    height: 7.5rem; /* 120px */
    border-radius: 50%;
    background: linear-gradient(135deg, #f5f5f5 0%, #e8e8e8 100%);
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem; /* 48px */
    border: 0.1875rem solid #eaeaea; /* 3px */
  }

  .professional-info{
    flex: 1;
  }

  .professional-name{
    margin: 0 0 0.5rem; /* 8px */
    font-size: 1.75rem; /* 28px */
    font-weight: 700;
  }

  .professional-title{
    color: var(--muted);
    font-size: 1rem; /* 16px */
    margin-bottom: 0.75rem; /* 12px */
  }

  .professional-stats{
    display: flex;
    gap: 1.5rem; /* 24px */
    margin-bottom: 0.75rem; /* 12px */
  }

  .stat-item{
    display: flex;
    flex-direction: column;
    gap: 0.25rem; /* 4px */
  }

  .stat-value{
    font-weight: 700;
    font-size: 1.25rem; /* 20px */
  }

  .stat-label{
    color: var(--muted);
    font-size: 0.75rem; /* 12px */
    text-transform: uppercase;
    letter-spacing: 0.0625rem; /* 1px */
  }

  .professional-bio{
    color: var(--muted);
    line-height: 1.6;
    margin: 0.75rem 0 0; /* 12px */
  }

  @media (max-width: 48rem) { /* 768px */
    .professional-profile{
      flex-direction: column;
      text-align: center;
    }

    .professional-stats{
      justify-content: center;
    }
  }

  /* ---- TABS ---- */
  .tabs{
    display: flex;
    gap: 0.5rem; /* 8px */
    margin-bottom: 1.5rem; /* 24px */
    border-bottom: 0.125rem solid #eaeaea; /* 2px */
    padding-bottom: 0;
  }

  .tab{
    padding: 0.75rem 1.5rem; /* 12px 24px */
    background: transparent;
    border: none;
    font-family: inherit;
    font-size: 0.9375rem; /* 15px */
    font-weight: 600;
    color: var(--muted);
    cursor: pointer;
    border-bottom: 0.1875rem solid transparent; /* 3px */
    margin-bottom: -0.125rem; /* -2px */
    transition: all .2s ease;
  }

  .tab:hover{
    color: var(--text);
  }

  .tab.active{
    color: var(--accent);
    border-bottom-color: var(--accent);
  }

  .tab-content{
    display: none;
  }

  .tab-content.active{
    display: block;
  }

  /* ---- REVIEWS SECTION ---- */
  .reviews-grid{
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(20rem, 1fr)); /* 320px */
    gap: 1.25rem; /* 20px */
  }

  .review-card{
    background: white;
    border: 0.0625rem solid #eaeaea; /* 1px */
    border-radius: 0.75rem; /* 12px */
    padding: 1.25rem; /* 20px */
    box-shadow: 0 0.125rem 0.5rem rgba(0,0,0,.04); /* 2px 8px */
  }

  .review-card-header{
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.75rem; /* 12px */
  }

  .review-client{
    display: flex;
    align-items: center;
    gap: 0.75rem; /* 12px */
  }

  .review-client-avatar{
    width: 2.5rem; /* 40px */
    height: 2.5rem; /* 40px */
    border-radius: 50%;
    background: #ddd;
  }

  .review-client-info{
    display: flex;
    flex-direction: column;
    gap: 0.125rem; /* 2px */
  }

  .review-client-name{
    font-weight: 600;
    font-size: 0.9375rem; /* 15px */
  }

  .review-service-name{
    font-size: 0.8125rem; /* 13px */
    color: var(--muted);
  }

  .review-rating{
    display: flex;
    align-items: center;
    gap: 0.25rem; /* 4px */
  }

  .review-card-text{
    color: var(--muted);
    line-height: 1.6;
    margin: 0;
  }

  .review-date{
    font-size: 0.75rem; /* 12px */
    color: var(--muted);
    margin-top: 0.75rem; /* 12px */
  }

  /* ---- SERVICE CARDS GRID ---- */
  .services-grid{
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(20rem, 1fr)); /* 320px */
    gap: 1.5rem; /* 24px */
    margin-bottom: 2.5rem; /* 40px */
  }

  .service-card {
    background: white;
    border: 0.0625rem solid rgba(0,0,0,0.05); /* 1px */
    border-radius: 1rem;
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
    box-shadow: 0 0.25rem 1.25rem rgba(0,0,0,0.05);
    display: flex;
    flex-direction: column;
    position: relative;
  }
  
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
  
  .favorite-btn.active {
    background: #ff4d6d;
    border-color: #ff4d6d;
  }
  
  .favorite-btn svg {
    width: 1.25rem;  /* 20px */
    height: 1.25rem; /* 20px */
    fill: #ccc;
    transition: fill 0.2s ease;
  }
  
  .favorite-btn.active svg {
    fill: white;
  }
  
  .favorite-btn:hover svg {
    fill: #ff4d6d;
  }
  
  .favorite-btn.active:hover svg {
    fill: white;
  }

  .service-card:hover {
    transform: translateY(-0.25rem);
    box-shadow: 0 0.5rem 1.5rem rgba(255, 117, 140, 0.15);
    border-color: rgba(255, 117, 140, 0.2);
  }

  .service-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    border-radius: 1rem;
    background: linear-gradient(135deg, rgba(255, 117, 140, 0.08) 0%, rgba(255, 117, 140, 0.02) 100%);
    opacity: 0;
    transition: opacity 0.2s ease;
    pointer-events: none;
  }

  .service-card:hover::before {
    opacity: 1;
  }

  .service-image {
    width: 100%;
    height: 12.5rem; /* 200px */
    background: linear-gradient(135deg, #f9f9f9 0%, #f0f0f0 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem; /* 48px */
    border-bottom: 0.0625rem solid rgba(0,0,0,0.05); /* 1px */
    transition: background 0.2s ease;
    position: relative;
    z-index: 1;
  }

  .service-content{
    padding: 1.25rem; /* 20px */
    flex: 1;
    display: flex;
    flex-direction: column;
  }

  .service-category{
    display: inline-block;
    padding: 0.25rem 0.625rem; /* 4px 10px */
    background: #f0f0f0;
    border-radius: 999px;
    font-size: 0.6875rem; /* 11px */
    text-transform: uppercase;
    letter-spacing: 0.0625rem; /* 1px */
    font-weight: 600;
    color: var(--muted);
    margin-bottom: 0.75rem; /* 12px */
  }

  .service-title{
    margin: 0 0 0.5rem; /* 8px */
    font-weight: 700;
    font-size: 1.25rem; /* 20px */
    line-height: 1.3;
  }

  .service-provider{
    display: flex;
    align-items: center;
    gap: 0.5rem; /* 8px */
    margin-bottom: 0.75rem; /* 12px */
    color: var(--muted);
    font-size: 0.875rem; /* 14px */
  }

  .provider-avatar{
    width: 1.5rem;  /* 24px */
    height: 1.5rem; /* 24px */
    border-radius: 50%;
    background: #ddd;
  }

  .service-description{
    margin: 0 0 1rem; /* 16px */
    color: var(--muted);
    font-size: 0.875rem; /* 14px */
    line-height: 1.6;
  }

  .service-meta{
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1rem; /* 16px */
    border-top: 0.0625rem solid #eaeaea; /* 1px */
    margin-bottom: 0.75rem; /* 12px */
    margin-top: auto;
  }

  .service-price{
    font-weight: 700;
    font-size: 1.25rem; /* 20px */
    letter-spacing: -0.03125rem; /* -0.5px */
  }

  .service-duration{
    color: var(--muted);
    font-size: 0.8125rem; /* 13px */
  }

  .service-rating{
    display: flex;
    align-items: center;
    gap: 0.25rem; /* 4px */
    font-size: 0.8125rem; /* 13px */
  }

  .stars{
    color: #fbbf24;
    letter-spacing: 0.0625rem; /* 1px */
  }

  .service-footer{
    padding: 0 1.25rem 1.25rem; /* 0 20px 20px */
  }

  .btn-book{
    width: 100%;
    padding: 0.75rem 1.25rem; /* 12px 20px */
    background: var(--accent);
    color: white;
    border: none;
    border-radius: 0.5rem; /* 8px */
    font-weight: 600;
    font-size: 0.9375rem; /* 15px */
    cursor: pointer;
    transition: opacity .2s ease;
    font-family: inherit;
  }

  .btn-book:hover{
    opacity: .9;
  }

  /* ---- RESPONSIVE ---- */
  @media (max-width: 48rem) { /* 768px */
    .services-grid{
      grid-template-columns: 1fr;
    }

    .filters{
      flex-direction: column;
    }

    .filter-group{
      width: 100%;
    }
  }
</style>

</head>
<body class="has-solid-header">
  <!-- Header (from index.html) -->
  <header id="siteHeader" class="site-header">
    <div class="container header-inner">
      <a class="brand" href="index.php">Glammd</a>
      <nav class="nav">
        <a href="index.php" class="nav-link">Log out</a>
      </nav>
    </div>
  </header>

  <!-- Main Content -->
  <main class="page">
    <div class="container">
      <!-- Breadcrumbs -->
      <div class="breadcrumbs">
        <a href="clientdashboard.php">Client Dashboard</a>
        <span>›</span>
        <a href="MarketPlace.php">Market</a>
        <span>›</span>
        <strong>Sarah M.</strong>
      </div>

      <!-- Professional Profile -->
      <div class="professional-profile">
        <div class="professional-photo">
            <img src="img/pro1.jpg" alt="Sarah M." style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
        </div>
        <div class="professional-info">
          <h1 class="professional-name">Sarah M.</h1>
          <p class="professional-title">Professional Makeup Artist & Beauty Specialist</p>
          <div class="professional-stats">
            <div class="stat-item">
              <span class="stat-value">★ 4.9</span>
              <span class="stat-label">Rating</span>
            </div>
            <div class="stat-item">
              <span class="stat-value">127</span>
              <span class="stat-label">Reviews</span>
            </div>
            <div class="stat-item">
              <span class="stat-value">8</span>
              <span class="stat-label">Services</span>
            </div>
            <div class="stat-item">
              <span class="stat-value">5+ years</span>
              <span class="stat-label">Experience</span>
            </div>
          </div>
          <p class="professional-bio">
            Certified makeup artist specializing in bridal, evening, and special occasion makeup. 
            Passionate about enhancing natural beauty and creating flawless looks that last. 
            Using premium products and techniques tailored to your unique features.
          </p>
        </div>
      </div>

      <!-- Tabs -->
      <div class="tabs">
        <button class="tab active" data-tab="services">Services</button>
        <button class="tab" data-tab="reviews">Reviews</button>
      </div>

      <!-- Services Tab -->
      <div class="tab-content active" id="services-content">
      <!-- Filters -->
      <div class="filters">
        <div class="filter-group">
          <label class="filter-label">Category</label>
          <select class="filter-select" id="categoryFilter">
            <option value="">All Categories</option>
            <option value="makeup">Makeup</option>
            <option value="hair">Hair</option>
            <option value="skin">Skin Care</option>
            <option value="nails">Nails</option>
          </select>
        </div>

        <div class="filter-group">
          <label class="filter-label">Price Range</label>
          <select class="filter-select" id="priceFilter">
            <option value="">All Prices</option>
            <option value="0-500">Under SAR 500</option>
            <option value="500-1000">SAR 500 - 1,000</option>
            <option value="1000+">SAR 1,000+</option>
          </select>
        </div>

      </div>

      <!-- Services Grid -->
      <div class="services-grid" id="services-grid">
        <?php
        // Get all services with professional information
        $query = "SELECT s.*, u.name as professional_name, p.img as professional_img 
                 FROM Service s 
                 JOIN User u ON s.professional_id = u.user_id
                 JOIN Professional p ON s.professional_id = p.professional_id";
        $result = mysqli_query($conn, $query);
        
        if ($result && mysqli_num_rows($result) > 0) {
          while($service = mysqli_fetch_assoc($result)) {
            $formattedPrice = number_format($service['price'], 2);
            $professionalImg = !empty($service['professional_img']) ? 'images/' . $service['professional_img'] : 'images/default-professional.jpg';
            ?>
            <article class="service-card">
              <button class="favorite-btn" aria-label="Add to favorites">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                  <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                </svg>
              </button>
              <div class="service-image">
                <?php 
                // Simple emoji based on category
                $emoji = '✨'; // default
                switch($service['category']) {
                    case 'Hair': $emoji = '💇‍♀️'; break;
                    case 'Makeup': $emoji = '💄'; break;
                    case 'Nails': $emoji = '💅'; break;
                    case 'Skincare': $emoji = '🌸'; break;
                    case 'Bodycare': $emoji = '🧖‍♀️'; break;
                }
                echo $emoji;
                ?>
              </div>
              <div class="service-content">
                <span class="service-category"><?php echo htmlspecialchars($service['category']); ?></span>
                <h3 class="service-title"><?php echo htmlspecialchars($service['title']); ?></h3>
                <p class="service-description">
                  <?php echo htmlspecialchars($service['description']); ?>
                </p>
                <div class="service-meta">
                  <div>
                    <div class="service-price">SAR <?php echo $formattedPrice; ?></div>
                    <div class="service-duration"><?php echo $service['duration']; ?> min</div>
                  </div>
                  <div class="service-rating">
                    <span class="stars">★★★★★</span>
                    <span>(<?php echo rand(10, 100); ?>)</span>
                  </div>
                </div>
              </div>
              <div class="service-footer">
                <a href="booking.php?service=<?php echo $service['service_id']; ?>" class="btn-book">Book Now</a>
              </div>
            </article>
            <?php
          }
        } else {
          echo '<p class="no-results">No services available at the moment.</p>';
        }
        ?>
      </div>
      <!-- End Services Tab -->

      <!-- Reviews Tab -->
      <div class="tab-content" id="reviews-content">
        <div class="reviews-grid">
          <article class="review-card">
            <div class="review-card-header">
              <div class="review-client">
                <span class="review-client-avatar"></span>
                <div class="review-client-info">
                  <span class="review-client-name">Aisha K.</span>
                  <span class="review-service-name">Signature Glow Makeup</span>
                </div>
              </div>
              <div class="review-rating">
                <span class="stars">★★★★★</span>
              </div>
            </div>
            <p class="review-card-text">Flawless finish and super professional. Makeup lasted all night! Sarah is incredibly talented and made me feel so confident.</p>
            <div class="review-date">2 weeks ago</div>
          </article>

          <article class="review-card">
            <div class="review-card-header">
              <div class="review-client">
                <span class="review-client-avatar"></span>
                <div class="review-client-info">
                  <span class="review-client-name">Dana R.</span>
                  <span class="review-service-name">Soft Waves Styling</span>
                </div>
              </div>
              <div class="review-rating">
                <span class="stars">★★★★★</span>
              </div>
            </div>
            <p class="review-card-text">Exactly what I wanted. Soft & elegant waves, perfect for photos. Very professional and friendly service!</p>
            <div class="review-date">3 weeks ago</div>
          </article>

          <article class="review-card">
            <div class="review-card-header">
              <div class="review-client">
                <span class="review-client-avatar"></span>
                <div class="review-client-info">
                  <span class="review-client-name">Lama S.</span>
                  <span class="review-service-name">Evening Glam</span>
                </div>
              </div>
              <div class="review-rating">
                <span class="stars">★★★★☆</span>
              </div>
            </div>
            <p class="review-card-text">Loved the look! Arrived on time and very friendly. The makeup was bold and beautiful, exactly what I asked for.</p>
            <div class="review-date">1 month ago</div>
          </article>

          <article class="review-card">
            <div class="review-card-header">
              <div class="review-client">
                <span class="review-client-avatar"></span>
                <div class="review-client-info">
                  <span class="review-client-name">Noor A.</span>
                  <span class="review-service-name">Hydrating Facial</span>
                </div>
              </div>
              <div class="review-rating">
                <span class="stars">★★★★★</span>
              </div>
            </div>
            <p class="review-card-text">Great prep — skin looked smooth and hydrated under makeup. Sarah really knows her skincare!</p>
            <div class="review-date">1 month ago</div>
          </article>

          <article class="review-card">
            <div class="review-card-header">
              <div class="review-client">
                <span class="review-client-avatar"></span>
                <div class="review-client-info">
                  <span class="review-client-name">Reem K.</span>
                  <span class="review-service-name">Gel Manicure</span>
                </div>
              </div>
              <div class="review-rating">
                <span class="stars">★★★★★</span>
              </div>
            </div>
            <p class="review-card-text">Perfect nails! Very professional and clean work. The gel lasted for weeks without chipping.</p>
            <div class="review-date">2 months ago</div>
          </article>

          <article class="review-card">
            <div class="review-card-header">
              <div class="review-client">
                <span class="review-client-avatar"></span>
                <div class="review-client-info">
                  <span class="review-client-name">Hessa M.</span>
                  <span class="review-service-name">Bridal Makeup Package</span>
                </div>
              </div>
              <div class="review-rating">
                <span class="stars">★★★★★</span>
              </div>
            </div>
            <p class="review-card-text">Made my wedding day perfect! Absolutely stunning results. Sarah is a true artist and made me feel like a princess.</p>
            <div class="review-date">2 months ago</div>
          </article>

          <article class="review-card">
            <div class="review-card-header">
              <div class="review-client">
                <span class="review-client-avatar"></span>
                <div class="review-client-info">
                  <span class="review-client-name">Layla T.</span>
                  <span class="review-service-name">Signature Glow Makeup</span>
                </div>
              </div>
              <div class="review-rating">
                <span class="stars">★★★★★</span>
              </div>
            </div>
            <p class="review-card-text">Amazing experience! Sarah listened to what I wanted and delivered perfectly. Will definitely book again.</p>
            <div class="review-date">3 months ago</div>
          </article>

          <article class="review-card">
            <div class="review-card-header">
              <div class="review-client">
                <span class="review-client-avatar"></span>
                <div class="review-client-info">
                  <span class="review-client-name">Maha K.</span>
                  <span class="review-service-name">Evening Glam</span>
                </div>
              </div>
              <div class="review-rating">
                <span class="stars">★★★★★</span>
              </div>
            </div>
            <p class="review-card-text">Stunning work! The contouring was flawless and the lashes looked so natural. Highly recommend!</p>
            <div class="review-date">3 months ago</div>
          </article>
        </div>
      </div>
      <!-- End Reviews Tab -->

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

  <!-- JavaScript -->
  <script>
    // Tabs functionality
    const tabs = document.querySelectorAll('.tab');
    const tabContents = document.querySelectorAll('.tab-content');

    tabs.forEach(tab => {
      tab.addEventListener('click', () => {
        const targetTab = tab.dataset.tab;
        
        // Remove active class from all tabs and contents
        tabs.forEach(t => t.classList.remove('active'));
        tabContents.forEach(content => content.classList.remove('active'));
        
        // Add active class to clicked tab and corresponding content
        tab.classList.add('active');
        document.getElementById(`${targetTab}-content`).classList.add('active');
      });
    });

    // Get all service cards
    const serviceCards = document.querySelectorAll('.service-card');
    const categoryFilter = document.getElementById('categoryFilter');
    const priceFilter = document.getElementById('priceFilter');

    // Filter function
    function filterServices() {
      const selectedCategory = categoryFilter.value.toLowerCase();
      const selectedPrice = priceFilter.value;

      serviceCards.forEach(card => {
        const category = card.querySelector('.service-category').textContent.toLowerCase();
        const priceText = card.querySelector('.service-price').textContent;
        const price = parseInt(priceText.replace(/[^0-9]/g, ''));

        let showCard = true;

        // Category filter
        if (selectedCategory && !category.includes(selectedCategory)) {
          showCard = false;
        }

        // Price filter
        if (selectedPrice) {
          if (selectedPrice === '0-500' && price > 500) showCard = false;
          if (selectedPrice === '500-1000' && (price < 500 || price > 1000)) showCard = false;
          if (selectedPrice === '1000+' && price < 1000) showCard = false;
        }

        card.style.display = showCard ? 'block' : 'none';
      });
    }

    // Add event listeners to filters
    categoryFilter.addEventListener('change', filterServices);
    priceFilter.addEventListener('change', filterServices);
    
    // Add click event listeners to all favorite buttons
    document.addEventListener('DOMContentLoaded', function() {
      const favoriteBtns = document.querySelectorAll('.favorite-btn');
      
      favoriteBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
          e.preventDefault();
          this.classList.toggle('active');
          
          // Optional: Save to localStorage to persist the favorite state
          const serviceCard = this.closest('.service-card');
          const serviceTitle = serviceCard.querySelector('.service-title').textContent;
          const isFavorite = this.classList.contains('active');
          
          // Store in localStorage
          const favorites = JSON.parse(localStorage.getItem('favoriteServices') || '{}');
          favorites[serviceTitle] = isFavorite;
          localStorage.setItem('favoriteServices', JSON.stringify(favorites));
        });
      });
      
      // Load saved favorite states
      const favorites = JSON.parse(localStorage.getItem('favoriteServices') || '{}');
      favoriteBtns.forEach(btn => {
        const serviceCard = btn.closest('.service-card');
        const serviceTitle = serviceCard.querySelector('.service-title').textContent;
        if (favorites[serviceTitle]) {
          btn.classList.add('active');
        }
      });
    });

    // Update button styles to work with anchor tag
    const style = document.createElement('style');
    style.textContent = `
      .btn-book {
        display: inline-block;
        text-decoration: none;
        text-align: center;
        width: 100%;
        padding: 12px 20px;
        background: #000000;
        color: #ffffff;
        border: 1px solid #000000;
        border-radius: 8px;
        font-weight: 600;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s ease;
      }
      
      .btn-book:hover {
        background: #333333;
        border-color: #333333;
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
      }
      
      .btn-book:active {
        transform: translateY(0);
        box-shadow: none;
      }
    `;
    document.head.appendChild(style);
  </script>
</body>
</html>
