<?php
// service_add.php
ob_start();
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'db.php';

// Auth
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'professional') {
    header('Location: login.php');
    exit;
}

$pro_id = (int) $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: professionaldashboard.php');
    exit;
}

// Collect and validate
$category    = trim($_POST['category'] ?? '');
$title       = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$duration = intval($_POST["duration"]);

$price       = (float)($_POST['price'] ?? 0);
$tags        = trim($_POST['tags'] ?? '');

if ($category === '' || $title === '' || $duration <= 0 || $price < 0) {
    // you could set a session flash here
    header('Location: professionaldashboard.php');
    exit;
}

// Insert
$stmt = $conn->prepare("
    INSERT INTO Service (professional_id, category, title, description, duration, price, tags)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");
$stmt->bind_param("isssids", $pro_id, $category, $title, $description, $duration, $price, $tags);
$stmt->execute();

header('Location: professionaldashboard.php');
exit;
ob_end_flush();
