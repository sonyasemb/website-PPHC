<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

date_default_timezone_set('Asia/Makassar');

$host = "localhost";
$user = "root";
$pass = "";
$db   = "webpphc";

$conn = mysqli_connect($host, $user, $pass, $db, 3307);


// cek koneksi
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
  
