<?php
// request_update.php
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

$request_id = (int) ($_POST['request_id'] ?? 0);
$status     = trim($_POST['status'] ?? '');

if ($request_id <= 0 || ($status !== 'accepted' && $status !== 'rejected')) {
    header('Location: professionaldashboard.php');
    exit;
}

// get the request, ensure it belongs to this pro
$q = $conn->prepare("SELECT * FROM BookingRequest WHERE request_id = ? AND professional_id = ?");
$q->bind_param("ii", $request_id, $pro_id);
$q->execute();
$reqRes = $q->get_result();
if ($reqRes->num_rows === 0) {
    header('Location: professionaldashboard.php');
    exit;
}
$req = $reqRes->fetch_assoc();

// update request status
$u = $conn->prepare("UPDATE BookingRequest SET status = ? WHERE request_id = ?");
$u->bind_param("si", $status, $request_id);
$u->execute();

// if accepted → create Booking
if ($status === 'accepted') {
    // combine date + time (expected format: YYYY-MM-DD, HH:MM)
    $date = $req['preferred_date'];
    $time = $req['preferred_time'];
    // make a datetime string
    $datetime = $date . ' ' . $time ;

    $ins = $conn->prepare("
        INSERT INTO Booking (client_id, professional_id, service_id, time, status)
        VALUES (?, ?, ?, ?, 'confirmed')
    ");
    $ins->bind_param("iiis", $req['client_id'], $pro_id, $req['service_id'], $datetime);
    $ins->execute();
}

header('Location: professionaldashboard.php');
exit;
ob_end_flush();
