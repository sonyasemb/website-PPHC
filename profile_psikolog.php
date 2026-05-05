<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'psikolog') {
    header("Location: login.php");
    exit;
}

require_once "koneksi.php";

$page_title = "Profil Saya";
$page = "profil";
$active_menu = "profil";
$today = date("Y-m-d");

$profile_defaults = [
    "nama_lengkap" => trim((string)($_SESSION["nama"] ?? "Dr. Andi Setiawan")),
    "jenis_kelamin" => "Laki-laki",
    "tanggal_lahir" => "1985-05-12",
    "email" => "andi.psikolog@mail.com",
    "telepon" => "08123456789",
    "spesialisasi" => "Kecemasan & Depresi",
    "no_str" => "STR-10293847",
    "pendidikan" => "S2 Psikologi Klinis",
    "pengalaman" => "8 Tahun",
    "metode_terapi" => "CBT, Mindfulness",
    "lokasi_praktik" => "Klinik Sehat Mental",
    "hari_praktik" => "Senin - Jumat",
    "jam_praktik" => "09:00 - 17:00",
    "durasi_sesi" => "60 Menit",
    "layanan" => "Offline & Online"
];

if (!isset($_SESSION["psikolog_profile"]) || !is_array($_SESSION["psikolog_profile"])) {
    $_SESSION["psikolog_profile"] = $profile_defaults;
}
$psikolog_profile = array_merge($profile_defaults, $_SESSION["psikolog_profile"]);
$header_name = trim((string)($_SESSION["nama"] ?? "Dr. Andi Setiawan"));
$header_role = trim((string)($psikolog_profile["spesialisasi"] ?? "Psikolog Klinis"));
$header_email = trim((string)($_SESSION["email"] ?? ($psikolog_profile["email"] ?? "-")));
$header_username = trim((string)($_SESSION["username"] ?? "-"));

$profil_alert_error = "";
$profil_alert_success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = trim((string)($_POST["action"] ?? ""));

    if ($action === "save_profile") {
        $updated_profile = [
            "nama_lengkap" => trim((string)($_POST["nama_lengkap"] ?? $psikolog_profile["nama_lengkap"])),
            "jenis_kelamin" => trim((string)($_POST["jenis_kelamin"] ?? $psikolog_profile["jenis_kelamin"])),
            "tanggal_lahir" => trim((string)($_POST["tanggal_lahir"] ?? $psikolog_profile["tanggal_lahir"])),
            "email" => trim((string)($_POST["email"] ?? $psikolog_profile["email"])),
            "telepon" => trim((string)($_POST["telepon"] ?? $psikolog_profile["telepon"])),
            "spesialisasi" => trim((string)($_POST["spesialisasi"] ?? $psikolog_profile["spesialisasi"])),
            "no_str" => trim((string)($_POST["no_str"] ?? $psikolog_profile["no_str"])),
            "pendidikan" => trim((string)($_POST["pendidikan"] ?? $psikolog_profile["pendidikan"])),
            "pengalaman" => trim((string)($_POST["pengalaman"] ?? $psikolog_profile["pengalaman"])),
            "metode_terapi" => trim((string)($_POST["metode_terapi"] ?? $psikolog_profile["metode_terapi"])),
            "lokasi_praktik" => trim((string)($_POST["lokasi_praktik"] ?? $psikolog_profile["lokasi_praktik"])),
            "hari_praktik" => trim((string)($_POST["hari_praktik"] ?? $psikolog_profile["hari_praktik"])),
            "jam_praktik" => trim((string)($_POST["jam_praktik"] ?? $psikolog_profile["jam_praktik"])),
            "durasi_sesi" => trim((string)($_POST["durasi_sesi"] ?? $psikolog_profile["durasi_sesi"])),
            "layanan" => trim((string)($_POST["layanan"] ?? $psikolog_profile["layanan"]))
        ];

        if ($updated_profile["nama_lengkap"] === "") {
            $profil_alert_error = "Nama lengkap wajib diisi.";
        } else {
            $_SESSION["psikolog_profile"] = $updated_profile;
            $psikolog_profile = $updated_profile;
            $_SESSION["nama"] = $updated_profile["nama_lengkap"];

            $id_user = (int)($_SESSION["id_user"] ?? 0);
            if ($id_user > 0) {
                $stmt = mysqli_prepare($conn, "UPDATE users SET nama=? WHERE id_user=?");
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "si", $updated_profile["nama_lengkap"], $id_user);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }
            }

            header("Location: profile_psikolog.php?saved=1");
            exit;
        }
    }
}

$profile_edit_mode = (($_GET["edit"] ?? "") === "1" || $profil_alert_error !== "");
$profile_saved = (($_GET["saved"] ?? "") === "1");
$profile_stats = [
    "total_klien" => 0,
    "total_sesi" => 0,
    "sesi_bulan_ini" => 0,
    "follow_up_aktif" => 0
];

$nama_psikolog = trim((string)($_SESSION["nama"] ?? ""));
$bulan_ini = date("Y-m");

$stmt = mysqli_prepare(
    $conn,
    "SELECT COUNT(DISTINCT nama) AS total_klien, COUNT(*) AS total_sesi
     FROM reservasi
     WHERE status='confirmed' AND LOWER(TRIM(psikolog)) = LOWER(TRIM(?))"
);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $nama_psikolog);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result && ($row = mysqli_fetch_assoc($result))) {
        $profile_stats["total_klien"] = (int)($row["total_klien"] ?? 0);
        $profile_stats["total_sesi"] = (int)($row["total_sesi"] ?? 0);
    }
    mysqli_stmt_close($stmt);
}

$stmt = mysqli_prepare(
    $conn,
    "SELECT COUNT(*) AS c
     FROM reservasi
     WHERE status='confirmed' AND DATE_FORMAT(tanggal, '%Y-%m') = ? AND LOWER(TRIM(psikolog)) = LOWER(TRIM(?))"
);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "ss", $bulan_ini, $nama_psikolog);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result && ($row = mysqli_fetch_assoc($result))) {
        $profile_stats["sesi_bulan_ini"] = (int)($row["c"] ?? 0);
    }
    mysqli_stmt_close($stmt);
}

$stmt = mysqli_prepare(
    $conn,
    "SELECT COUNT(*) AS c
     FROM reservasi
     WHERE status='pending' AND LOWER(TRIM(psikolog)) = LOWER(TRIM(?))"
);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $nama_psikolog);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result && ($row = mysqli_fetch_assoc($result))) {
        $profile_stats["follow_up_aktif"] = (int)($row["c"] ?? 0);
    }
    mysqli_stmt_close($stmt);
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/psikolog.css?v=<?php echo filemtime(__DIR__ . '/assets/css/psikolog.css'); ?>">
</head>
<body>
<div class="sidebar">
    <h3>Psychological Practice Hanna & Consultant</h3>

    <div class="menu-title">Menu Psikolog</div>
    <a class="menu-item" href="psikolog.php?page=dashboard">
        <span class="icon"><img src="ikon/beranda.png" alt="dashboard"></span>
        <span>Dashboard</span>
    </a>
    </a>
    <a class="menu-item" href="psikolog.php?page=klien">
        <span class="icon"><img src="ikon/pasien.png" alt="klien"></span>
        <span>Klien Saya</span>
    </a>
    <a class="menu-item" href="psikolog.php?page=catatan">
        <span class="icon"><img src="ikon/catatan_sesi.png" alt="catatan sesi"></span>
        <span>Catatan Sesi</span>

    </a>
    <a class="menu-item" href="psikolog.php?page=pesan">
        <span class="icon"><img src="ikon/catatan_klien.png" alt="catatan klien"></span>
        <span>Catatan Klien</span>

    </a>

    <div class="menu-title">Akun</div>
    <a class="menu-item active" href="profile_psikolog.php">
        <span class="icon"><img src="ikon/profil.png" alt="profil"></span>
        <span>Profil Saya</span>

    </a>
    <a class="menu-item" href="logout.php">
        <span class="icon"><img src="ikon/logout.png" alt="logout"></span>
        <span>Logout</span>

    </a>
</div>

<div class="content">
    <div class="header">
        <h2>Profil Saya</h2>
    </div>
    <?php include "ui_psikolog.php"; ?>
</div>
</body>
</html>

