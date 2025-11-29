<?php
// Start session
session_start();

// Database connection
require_once 'db.php';

// Initialize variables
$is_logged_in = isset($_SESSION['user_id']);
$is_professional = false;
$professional_id = null;
$user_id = $is_logged_in ? $_SESSION['user_id'] : null;

// Check if user is a professional (only if logged in)
if ($is_logged_in) {
    $check_professional = mysqli_prepare($conn, "SELECT professional_id FROM Professional WHERE professional_id = ?");
    mysqli_stmt_bind_param($check_professional, "i", $user_id);
    mysqli_stmt_execute($check_professional);
    $result = mysqli_stmt_get_result($check_professional);

    if (mysqli_num_rows($result) > 0) {
        $is_professional = true;
        $professional = mysqli_fetch_assoc($result);
        $professional_id = $professional['professional_id'];
    }
}

// Check if viewing a specific professional's services
$viewing_professional_id = isset($_GET['professional_id']) ? intval($_GET['professional_id']) : null;

// Build the query based on the viewing context
if ($viewing_professional_id) {
    // Viewing a specific professional's services
    $query = "SELECT s.*, u.name as professional_name, p.img as professional_img, p.bio, p.professional_id
              FROM Service s 
              JOIN Professional p ON s.professional_id = p.professional_id 
              JOIN User u ON p.professional_id = u.user_id
              WHERE s.professional_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $viewing_professional_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    // Get professional info
    $prof_query = "SELECT u.name, p.bio, p.img as professional_img 
                  FROM User u 
                  JOIN Professional p ON u.user_id = p.professional_id 
                  WHERE p.professional_id = ?";
    $prof_stmt = mysqli_prepare($conn, $prof_query);
    mysqli_stmt_bind_param($prof_stmt, "i", $viewing_professional_id);
    mysqli_stmt_execute($prof_stmt);
    $current_professional = mysqli_fetch_assoc(mysqli_stmt_get_result($prof_stmt));
} elseif ($is_professional && !$viewing_professional_id) {
    // Professional viewing their own services
    $query = "SELECT s.*, u.name as professional_name, p.img as professional_img, p.bio
              FROM Service s 
              JOIN Professional p ON s.professional_id = p.professional_id 
              JOIN User u ON p.professional_id = u.user_id
              WHERE s.professional_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $professional_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    // Default view - show all services
    $query = "SELECT s.*, u.name as professional_name, p.img as professional_img, p.bio
              FROM Service s 
              JOIN Professional p ON s.professional_id = p.professional_id 
              JOIN User u ON p.professional_id = u.user_id
              ORDER BY s.title";
    $result = mysqli_query($conn, $query);
}

// Store services in an array
$services = [];
$current_professional = null;

// Get professional info if viewing a specific professional
if ($viewing_professional_id) {
    $prof_query = "SELECT u.name, p.bio, p.img as professional_img 
                  FROM User u 
                  JOIN Professional p ON u.user_id = p.professional_id 
                  WHERE p.professional_id = ?";
    $stmt = mysqli_prepare($conn, $prof_query);
    mysqli_stmt_bind_param($stmt, "i", $viewing_professional_id);
    mysqli_stmt_execute($stmt);
    $current_professional = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

// Process services
while ($row = mysqli_fetch_assoc($result)) {
    $services[] = $row;
    
    // If we don't have current professional info yet, get it from the first result
    if (!$current_professional && !$viewing_professional_id) {
        $current_professional = [
            'name' => $row['professional_name'],
            'bio' => $row['bio'],
            'img' => $row['professional_img']
        ];
    }
}

// If no services found for a specific professional, try to get their info anyway
if (empty($services) && $viewing_professional_id) {
    $prof_query = "SELECT u.name, p.bio, p.img as professional_img 
                  FROM User u 
                  JOIN Professional p ON u.user_id = p.professional_id 
                  WHERE p.professional_id = ?";
    $stmt = mysqli_prepare($conn, $prof_query);
    mysqli_stmt_bind_param($stmt, "i", $viewing_professional_id);
    mysqli_stmt_execute($stmt);
    $current_professional = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
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
  .reviews-grid {
    max-width: 1200px;
    margin: 0 auto;
    padding: 1rem;
  }

  .reviews-list {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 2rem;
  }

  .review-item {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    height: 100%;
    display: flex;
    flex-direction: column;
  }

  .review-header {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1rem;
  }

  .reviewer-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background-color: #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    color: #555;
    font-size: 1.2rem;
  }

  .reviewer-info {
    flex: 1;
  }

  .reviewer-name {
    font-weight: 600;
    color: #333;
    margin-bottom: 0.25rem;
  }

  .review-rating {
    color: #FFD700; /* Gold color for stars */
    font-size: 1.1rem;
    letter-spacing: 2px;
  }

  .review-date {
    color: #888;
    font-size: 0.9rem;
  }

  .review-comment {
    margin: 1rem 0;
    color: #555;
    line-height: 1.6;
    flex-grow: 1;
  }

  .review-service {
    font-size: 0.9rem;
    color: #666;
    font-style: italic;
    margin-top: 0.5rem;
  }

  .no-reviews {
    text-align: center;
    color: #666;
    padding: 2rem;
    background: #f9f9f9;
    border-radius: 8px;
    margin: 2rem 0;
  }

  .review-card {
    background: white;
    border: 0.0625rem solid #eaeaea;
    border-radius: 0.75rem;
    padding: 1.25rem;
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
  @media (max-width: 64rem) { /* 1024px */
    .reviews-list {
      grid-template-columns: repeat(2, 1fr);
    }
  }

  @media (max-width: 48rem) { /* 768px */
    .services-grid,
    .reviews-list {
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
        <strong><?php echo htmlspecialchars($current_professional['name'] ?? 'Professional'); ?></strong>
      </div>

      <!-- Professional Profile -->
      <div class="professional-profile">
        <div class="professional-photo">
            <?php 
            $img_src = 'img/default-profile.jpg';
            if (!empty($current_professional['professional_img'])) {
                $possible_paths = [
                    'images/' . $current_professional['professional_img'],
                    'img/' . $current_professional['professional_img'],
                    $current_professional['professional_img']
                ];
                
                foreach ($possible_paths as $path) {
                    if (file_exists($path)) {
                        $img_src = $path;
                        break;
                    }
                }
            }
            ?>
            <img src="<?php echo htmlspecialchars($img_src); ?>" 
                 alt="<?php echo htmlspecialchars($current_professional['name'] ?? 'Professional'); ?>" 
                 style="width:100%; height:100%; object-fit:cover; border-radius:50%;"
                 onerror="this.onerror=null; this.src='img/default-profile.jpg';">
        </div>
        <div class="professional-info">
          <h1 class="professional-name"><?php echo htmlspecialchars($current_professional['name'] ?? 'Professional'); ?></h1>
          <p class="professional-title">Professional Beauty Specialist</p>
          <?php if (!empty($current_professional['bio'])): ?>
          <p class="professional-bio">
            <?php echo htmlspecialchars($current_professional['bio']); ?>
          </p>
          <?php endif; ?>
        </div>
      </div>

      <!-- Tabs -->
      <div class="tabs">
        <button class="tab active" data-tab="services" onclick="showTab('services')">Services</button>
        <button class="tab" data-tab="reviews" onclick="showTab('reviews')">Reviews</button>
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
        // Get services for the specific professional or all services if no professional selected
        $query = "SELECT s.*, u.name as professional_name, p.img as professional_img 
                 FROM Service s 
                 JOIN User u ON s.professional_id = u.user_id
                 JOIN Professional p ON s.professional_id = p.professional_id";
        
        // Add WHERE clause if viewing a specific professional
        if ($viewing_professional_id) {
            $query .= " WHERE s.professional_id = " . intval($viewing_professional_id);
        }
        
        $result = mysqli_query($conn, $query);
        
        if ($result && mysqli_num_rows($result) > 0) {
          while($service = mysqli_fetch_assoc($result)) {
            $formattedPrice = number_format($service['price'], 2);
            $professionalImg = !empty($service['professional_img']) ? 'images/' . $service['professional_img'] : 'images/default-professional.jpg';
            
            // Get average rating for this service
            $rating_query = "SELECT AVG(rating) as avg_rating, COUNT(*) as review_count 
                           FROM Review 
                           WHERE service_id = " . $service['service_id'];
            $rating_result = mysqli_query($conn, $rating_query);
            $rating_data = mysqli_fetch_assoc($rating_result);
            $avg_rating = round($rating_data['avg_rating'] ?? 0, 1);
            $review_count = $rating_data['review_count'] ?? 0;
            
            // Convert rating to stars (1-5)
            $stars = str_repeat('★', floor($avg_rating)) . str_repeat('☆', 5 - floor($avg_rating));
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
                  <?php if ($review_count > 0): ?>
                  <div class="service-rating">
                    <span class="stars" title="<?php echo $avg_rating; ?> out of 5"><?php echo $stars; ?></span>
                    <span>(<?php echo $review_count; ?>)</span>
                  </div>
                  <?php endif; ?>
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
      </div>
      <!-- End Services Tab -->

      <!-- Reviews Tab -->
      <div class="tab-content" id="reviews-content">
        <div class="reviews-grid">
          <div class="reviews-list">
            <!-- Review 1 -->
            <div class="review-item">
              <div class="review-header">
                <div class="reviewer-avatar">S</div>
                <div class="reviewer-info">
                  <div class="reviewer-name">Sarah Johnson</div>
                  <div class="review-rating" title="5 out of 5">★★★★★</div>
                </div>
                <div class="review-date">November 28, 2025</div>
              </div>
              <div class="review-comment">Amazing service! The stylist was very professional and did exactly what I wanted. Highly recommend!</div>
              <div class="review-service">Service: Haircut & Styling</div>
            </div>

            <!-- Review 2 -->
            <div class="review-item">
              <div class="review-header">
                <div class="reviewer-avatar">M</div>
                <div class="reviewer-info">
                  <div class="reviewer-name">Michael Brown</div>
                  <div class="review-rating" title="4 out of 5">★★★★☆</div>
                </div>
                <div class="review-date">November 25, 2025</div>
              </div>
              <div class="review-comment">Great experience overall. The staff was friendly and the service was top-notch. Will definitely come back!</div>
              <div class="review-service">Service: Beard Trim</div>
            </div>

            <!-- Review 3 -->
            <div class="review-item">
              <div class="review-header">
                <div class="reviewer-avatar">A</div>
                <div class="reviewer-info">
                  <div class="reviewer-name">Aisha Al-Farsi</div>
                  <div class="review-rating" title="5 out of 5">★★★★★</div>
                </div>
                <div class="review-date">November 20, 2025</div>
              </div>
              <div class="review-comment">Absolutely loved my new look! The stylist was very attentive to detail and gave me exactly what I wanted. 10/10 would recommend!</div>
              <div class="review-service">Service: Hair Coloring</div>
            </div>

            <!-- Review 4 -->
            <div class="review-item">
              <div class="review-header">
                <div class="reviewer-avatar">D</div>
                <div class="reviewer-info">
                  <div class="reviewer-name">David Wilson</div>
                  <div class="review-rating" title="4 out of 5">★★★★☆</div>
                </div>
                <div class="review-date">November 15, 2025</div>
              </div>
              <div class="review-comment">Good service overall. The stylist was professional and the salon was clean. Would have given 5 stars if the waiting time was shorter.</div>
              <div class="review-service">Service: Haircut</div>
            </div>

            <!-- Review 5 -->
            <div class="review-item">
              <div class="review-header">
                <div class="reviewer-avatar">L</div>
                <div class="reviewer-info">
                  <div class="reviewer-name">Layla Ahmed</div>
                  <div class="review-rating" title="5 out of 5">★★★★★</div>
                </div>
                <div class="review-date">November 10, 2025</div>
              </div>
              <div class="review-comment">I'm so happy with my new hairstyle! The stylist was amazing and really listened to what I wanted. The salon has a great atmosphere too!</div>
              <div class="review-service">Service: Hair Styling</div>
            </div>
          </div>
        </div>
      </div>
      <!-- End Reviews Tab -->

    </div>
  </main>

  <script>
    function showTab(tabName) {
      // Hide all tab contents
      document.querySelectorAll('.tab-content').forEach(tab => {
        tab.style.display = 'none';
      });
      
      // Remove active class from all tabs
      document.querySelectorAll('.tab').forEach(tab => {
        tab.classList.remove('active');
      });
      
      // Show the selected tab content
      document.getElementById(tabName + '-content').style.display = 'block';
      
      // Add active class to the clicked tab
      document.querySelector(`.tab[data-tab="${tabName}"]`).classList.add('active');
    }
    
    // Show services tab by default
    document.addEventListener('DOMContentLoaded', function() {
      showTab('services');
    });
  </script>

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
