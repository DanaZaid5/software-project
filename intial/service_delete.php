<?php
// service_delete.php
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

$service_id = (int) ($_POST['service_id'] ?? 0);
if ($service_id <= 0) {
    header('Location: professionaldashboard.php');
    exit;
}

// check ownership
$check = $conn->prepare("SELECT service_id FROM Service WHERE service_id = ? AND professional_id = ?");
$check->bind_param("ii", $service_id, $pro_id);
$check->execute();
$res = $check->get_result();
if ($res->num_rows === 0) {
    header('Location: professionaldashboard.php');
    exit;
}

// delete
$del = $conn->prepare("DELETE FROM Service WHERE service_id = ?");
$del->bind_param("i", $service_id);
$del->execute();

header('Location: professionaldashboard.php');
exit;
ob_end_flush();
