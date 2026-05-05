<?php
require_once "koneksi.php";

$nama = trim($_POST["nama"] ?? "");
$jenis_kelamin = trim($_POST["jenis_kelamin"] ?? "");
$email = trim($_POST["email"] ?? "");
$telepon = trim($_POST["telepon"] ?? "");
$organisasi = trim($_POST["organisasi"] ?? "");
$keluhan_kebutuhan = trim($_POST["keluhan_kebutuhan"] ?? ($_POST["sumber_info"] ?? ""));
$layanan = trim($_POST["layanan"] ?? "");
$tipe = trim($_POST["tipe"] ?? "");
$psikolog = trim($_POST["psikolog"] ?? "");
$tanggal = trim($_POST["tanggal"] ?? "");
$waktu = trim($_POST["waktu"] ?? "");

if ($nama === "" || $jenis_kelamin === "" || $email === "" || $telepon === "" || $tanggal === "" || $waktu === "") {
    die("Data belum lengkap. Silakan kembali dan lengkapi form.");
}

$has_layanan_column = false;
$check_col = mysqli_query($conn, "SHOW COLUMNS FROM reservasi LIKE 'layanan'");
if ($check_col && mysqli_num_rows($check_col) > 0) {
    $has_layanan_column = true;
}
$detail_column = "sumber_info";
$check_detail_col = mysqli_query($conn, "SHOW COLUMNS FROM reservasi LIKE 'keluhan_kebutuhan'");
if ($check_detail_col && mysqli_num_rows($check_detail_col) > 0) {
    $detail_column = "keluhan_kebutuhan";
}

// Fallback hanya untuk skema lama (tanpa kolom layanan).
if (!$has_layanan_column && $psikolog === "" && $layanan !== "") {
    $psikolog = $layanan;
}

if ($has_layanan_column) {
    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO reservasi (nama, jenis_kelamin, email, telepon, organisasi, {$detail_column}, layanan, psikolog, tanggal, waktu, status)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')"
    );
} else {
    $psikolog = $layanan !== "" ? $layanan : $psikolog;
    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO reservasi (nama, jenis_kelamin, email, telepon, organisasi, {$detail_column}, psikolog, tanggal, waktu, status)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')"
    );
}

if (!$stmt) {
    die("Gagal menyiapkan query: " . mysqli_error($conn));
}

if ($has_layanan_column) {
    mysqli_stmt_bind_param(
        $stmt,
        "ssssssssss",
        $nama,
        $jenis_kelamin,
        $email,
        $telepon,
        $organisasi,
        $keluhan_kebutuhan,
        $layanan,
        $psikolog,
        $tanggal,
        $waktu
    );
} else {
    mysqli_stmt_bind_param(
        $stmt,
        "sssssssss",
        $nama,
        $jenis_kelamin,
        $email,
        $telepon,
        $organisasi,
        $keluhan_kebutuhan,
        $psikolog,
        $tanggal,
        $waktu
    );
}

if (!mysqli_stmt_execute($stmt)) {
    die("Gagal menyimpan reservasi: " . mysqli_stmt_error($stmt));
}

mysqli_stmt_close($stmt);

$wa_number = "6289522277505";
$pesan =
    "Halo, saya ingin booking konsultasi.\n" .
    "Layanan: $layanan\n" .
    "Tanggal: $tanggal\n" .
    "Waktu: $waktu\n" .
    "Tipe: $tipe\n" .
    "========\n" .
    "Nama: $nama\n" .
    "Jenis Kelamin: $jenis_kelamin\n" .
    "Email: $email\n" .
    "Telepon: $telepon\n" .
    "Organisasi: " . ($organisasi !== "" ? $organisasi : "-") . "\n" .
    "Keluhan/Kebutuhan: " . ($keluhan_kebutuhan !== "" ? $keluhan_kebutuhan : "-");

header("Location: https://wa.me/$wa_number?text=" . rawurlencode($pesan));
exit;
