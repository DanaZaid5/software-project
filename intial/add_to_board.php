<?php
session_start();
require_once 'db.php';

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];

// Make sure this user is a CLIENT
$is_client = false;
$client_id = null;

if ($stmt = mysqli_prepare($conn, "SELECT client_id FROM Client WHERE client_id = ?")) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($res)) {
        $is_client = true;
        $client_id = (int)$row['client_id'];
    }
    mysqli_stmt_close($stmt);
}

if (!$is_client || !$client_id) {
    // Not a client account
    header("Location: login.php");
    exit;
}

// Read POST data
$service_id     = isset($_POST['service_id']) ? (int)$_POST['service_id'] : 0;
$list_id        = isset($_POST['list_id']) ? (int)$_POST['list_id'] : 0;
$new_board_name = trim($_POST['new_board_name'] ?? '');

if ($service_id <= 0) {
    // No service – just go back
    header("Location: services.php");
    exit;
}

// If a new board name is provided, create it and use it
if ($new_board_name !== '') {
    if ($stmt = mysqli_prepare($conn, "INSERT INTO List (client_id, name) VALUES (?, ?)")) {
        mysqli_stmt_bind_param($stmt, "is", $client_id, $new_board_name);
        mysqli_stmt_execute($stmt);
        $list_id = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
    }
}

// If still no list_id, nothing to save to
if ($list_id <= 0) {
    // You could show an error, but for now just go back
    header("Location: services.php");
    exit;
}

// Make sure this board belongs to this client
if ($stmt = mysqli_prepare($conn, "SELECT list_id FROM List WHERE list_id = ? AND client_id = ?")) {
    mysqli_stmt_bind_param($stmt, "ii", $list_id, $client_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $valid_board = ($res && mysqli_num_rows($res) === 1);
    mysqli_stmt_close($stmt);
} else {
    $valid_board = false;
}

if (!$valid_board) {
    header("Location: services.php");
    exit;
}

// Insert into ListItem (ignore duplicates)
if ($stmt = mysqli_prepare($conn, "INSERT IGNORE INTO ListItem (list_id, service_id) VALUES (?, ?)")) {
    mysqli_stmt_bind_param($stmt, "ii", $list_id, $service_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

// Send user back to where they came from
$redirect = $_SERVER['HTTP_REFERER'] ?? 'services.php';
header("Location: " . $redirect);
exit;
?>