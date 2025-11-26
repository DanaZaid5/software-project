<?php
// الاتصال بقاعدة البيانات
$conn = mysqli_connect("localhost", "root", "root","glammd", 8889);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
