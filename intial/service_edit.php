<?php
// service_edit.php
ob_start();
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'professional') {
    header('Location: login.php');
    exit;
}

$pro_id = (int) $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: professionaldashboard.php');
    exit;
}

$service_id  = (int) ($_POST['service_id'] ?? 0);
$category    = trim($_POST['category'] ?? '');
$title       = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$duration = intval($_POST["duration"]);
$price       = (float) ($_POST['price'] ?? 0);
$tags        = trim($_POST['tags'] ?? '');

if ($service_id <= 0 || $category === '' || $title === '' || $duration <= 0) {
    header('Location: professionaldashboard.php');
    exit;
}

// ensure ownership
$check = $conn->prepare("SELECT service_id FROM Service WHERE service_id = ? AND professional_id = ?");
$check->bind_param("ii", $service_id, $pro_id);
$check->execute();
$res = $check->get_result();
if ($res->num_rows === 0) {
    header('Location: professionaldashboard.php');
    exit;
}

// update
$upd = $conn->prepare("
    UPDATE Service
    SET category = ?, title = ?, description = ?, duration = ?, price = ?, tags = ?
    WHERE service_id = ?
");
$upd->bind_param("sssidsi", $category, $title, $description, $duration, $price, $tags, $service_id);
$upd->execute();

header('Location: professionaldashboard.php');
exit;
ob_end_flush();
