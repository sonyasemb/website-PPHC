<?php
$active_menu = $active_menu ?? "";
if ($active_menu === "") {
    $current_file = basename($_SERVER["PHP_SELF"]);
    if ($current_file === "admin.php") {
        $page_param = $page ?? ($_GET["page"] ?? "dashboard");
        $active_menu = $page_param === "profil" ? "profil" : "dashboard";
    } elseif ($current_file === "reservasi.php") {
        $active_menu = "reservasi";
    } elseif ($current_file === "jadwal_psikolog.php") {
        $active_menu = "jadwal";
    } elseif ($current_file === "data_pasien.php") {
        $active_menu = "pasien";
    } elseif ($current_file === "laporan_pasien.php") {
        $active_menu = "laporan";
    } elseif ($current_file === "manajemen_staff.php") {
        $active_menu = "staff";
    }
}
?>
<div class="sidebar">
    <h3>Psychological Practice Hanna & Consultant</h3>

    <div class="menu-title">Dashboard</div>
    <a class="menu-item <?php echo $active_menu === "dashboard" ? "active" : ""; ?>" href="admin.php?page=dashboard">
        <span class="icon"><img src="ikon/beranda.png" alt="beranda"></span>
        <span>Beranda</span>
    </a>

    <div class="menu-title">Reservasi</div>
    <a class="menu-item <?php echo $active_menu === "reservasi" ? "active" : ""; ?>" href="reservasi.php">
        <span class="icon"><img src="ikon/reservasi.png" alt="reservasi"></span>
        <span>Reservasi</span>
    </a>

    <div class="menu-title">Jadwal Psikolog</div>
    <a class="menu-item <?php echo $active_menu === "jadwal" ? "active" : ""; ?>" href="jadwal_psikolog.php">
        <span class="icon"><img src="ikon/jadwal.png" alt="jadwal"></span>
        <span>Jadwal Psikolog</span>
    </a>
    <a class="menu-item <?php echo $active_menu === "pasien" ? "active" : ""; ?>" href="data_pasien.php">
        <span class="icon"><img src="ikon/pasien.png" alt="pasien"></span>
        <span>Data Pasien</span>
    </a>

    <div class="menu-title">Reports</div>
    <a class="menu-item <?php echo $active_menu === "laporan" ? "active" : ""; ?>" href="laporan_pasien.php">
        <span class="icon"><img src="ikon/laporan.png" alt="laporan"></span>
        <span>Laporan Pasien</span>
    </a>
    <a class="menu-item <?php echo $active_menu === "staff" ? "active" : ""; ?>" href="manajemen_staff.php">
        <span class="icon"><img src="ikon/staff.png" alt="manajemen"></span>
        <span>Manajemen Staff</span>
    </a>

    <div class="menu-title">Akun</div>
    <a class="menu-item <?php echo $active_menu === "profil" ? "active" : ""; ?>" href="admin.php?page=profil">
        <span class="icon"><img src="ikon/management.png" alt="profil"></span>
        <span>Profil</span>
    </a>
    <a class="menu-item" href="logout.php">
        <span class="icon"><img src="ikon/logout.png" alt="logout"></span>
        <span>Logout</span>
    </a>
</div>
<script src="assets/js/admin_select.js"></script>
