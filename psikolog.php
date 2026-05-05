<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'psikolog') {
    header("Location: login.php");
    exit;
}

require_once "koneksi.php";

$page = $_GET["page"] ?? "dashboard";
$allowed_pages = ["dashboard", "klien", "catatan", "pesan"];
if (!in_array($page, $allowed_pages, true)) {
    $page = "dashboard";
}
$current_psikolog = trim((string)($_SESSION["nama"] ?? ""));
$page_title_map = [
    "dashboard" => "Dashboard Psikolog",
    "klien" => "Klien Saya",
    "catatan" => "Catatan Sesi",
    "pesan" => "Catatan Klien"
];
$page_title = $page_title_map[$page] ?? "Dashboard Psikolog";
$active_menu = $page !== "" ? $page : "dashboard";
$today = date("Y-m-d");

if (!function_exists("normalize_catatan_date")) {
    function normalize_catatan_date(string $value): ?string
    {
        $value = trim($value);
        if ($value === "") {
            return null;
        }
        if (preg_match("/^\d{4}-\d{2}-\d{2}$/", $value)) {
            return $value;
        }
        if (preg_match("/^\d{2}-\d{2}-\d{4}$/", $value)) {
            $dt = DateTime::createFromFormat("d-m-Y", $value);
            if ($dt !== false) {
                return $dt->format("Y-m-d");
            }
        }
        return null;
    }
}

if (!function_exists("format_catatan_date")) {
    function format_catatan_date(?string $value): string
    {
        if (!$value) {
            return "-";
        }
        $dt = DateTime::createFromFormat("Y-m-d", $value);
        if ($dt !== false) {
            return $dt->format("d-m-Y");
        }
        return $value;
    }
}

// Simpan catatan sesi
if ($_SERVER["REQUEST_METHOD"] === "POST" && (($_POST["action"] ?? "") === "save_catatan")) {
    $catatan_id = (int)($_POST["catatan_id"] ?? 0);
    $id_sesi = trim((string)($_POST["id_sesi"] ?? ""));
    if ($id_sesi === "") {
        $id_sesi = "SESI-" . date("YmdHis");
    }
    $pasien = trim((string)($_POST["pasien"] ?? ""));
    $tanggal_raw = trim((string)($_POST["tanggal"] ?? ""));
    $tanggal = normalize_catatan_date($tanggal_raw);
    if (!$tanggal) {
        $tanggal = $today;
    }
    $waktu = trim((string)($_POST["waktu"] ?? ""));
    $jenis = trim((string)($_POST["jenis"] ?? ""));
    $keluhan = trim((string)($_POST["keluhan"] ?? ""));
    $observasi = trim((string)($_POST["observasi"] ?? ""));
    $ringkasan = trim((string)($_POST["ringkasan"] ?? ""));
    $diagnosis = trim((string)($_POST["diagnosis"] ?? ""));
    $rencana = trim((string)($_POST["rencana"] ?? ""));
    $rekomendasi = trim((string)($_POST["rekomendasi"] ?? ""));
    $status_sesi = trim((string)($_POST["status_sesi"] ?? "selesai"));
    $psikolog_nama = $current_psikolog !== "" ? $current_psikolog : trim((string)($_SESSION["nama"] ?? ""));

    $saved_ok = false;

    if ($catatan_id > 0) {
        $stmt = mysqli_prepare(
            $conn,
            "UPDATE catatan_klien
             SET id_sesi=?, pasien=?, tanggal=?, waktu=?, jenis=?, keluhan=?, observasi=?, ringkasan=?, diagnosis=?, rencana=?, rekomendasi=?, status_sesi=?, psikolog=?, updated_at=NOW()
             WHERE id=?"
        );
        if ($stmt) {
            mysqli_stmt_bind_param(
                $stmt,
                "sssssssssssssi",
                $id_sesi,
                $pasien,
                $tanggal,
                $waktu,
                $jenis,
                $keluhan,
                $observasi,
                $ringkasan,
                $diagnosis,
                $rencana,
                $rekomendasi,
                $status_sesi,
                $psikolog_nama,
                $catatan_id
            );
            mysqli_stmt_execute($stmt);
            $saved_ok = mysqli_stmt_affected_rows($stmt) >= 0;
            mysqli_stmt_close($stmt);
        }
    } else {
        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO catatan_klien
            (id_sesi, pasien, tanggal, waktu, jenis, keluhan, observasi, ringkasan, diagnosis, rencana, rekomendasi, status_sesi, psikolog, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ON DUPLICATE KEY UPDATE
                pasien=VALUES(pasien),
                tanggal=VALUES(tanggal),
                waktu=VALUES(waktu),
                jenis=VALUES(jenis),
                keluhan=VALUES(keluhan),
                observasi=VALUES(observasi),
                ringkasan=VALUES(ringkasan),
                diagnosis=VALUES(diagnosis),
                rencana=VALUES(rencana),
                rekomendasi=VALUES(rekomendasi),
                status_sesi=VALUES(status_sesi),
                psikolog=VALUES(psikolog),
                updated_at=NOW()"
        );
        if ($stmt) {
            mysqli_stmt_bind_param(
                $stmt,
                "sssssssssssss",
                $id_sesi,
                $pasien,
                $tanggal,
                $waktu,
                $jenis,
                $keluhan,
                $observasi,
                $ringkasan,
                $diagnosis,
                $rencana,
                $rekomendasi,
                $status_sesi,
                $psikolog_nama
            );
            mysqli_stmt_execute($stmt);
            $saved_ok = mysqli_stmt_errno($stmt) === 0;
            mysqli_stmt_close($stmt);
        }
    }

    $saved_flag = $saved_ok ? "1" : "0";
    header("Location: psikolog.php?page=pesan&saved={$saved_flag}");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && (($_POST["action"] ?? "") === "cancel_jadwal")) {
    $id = (int)($_POST["id"] ?? 0);
    $canceled_ok = false;
    if ($id > 0 && $current_psikolog !== "") {
        $stmt = mysqli_prepare(
            $conn,
            "UPDATE reservasi
             SET status='canceled'
             WHERE id=? AND status='confirmed' AND LOWER(TRIM(psikolog)) = LOWER(TRIM(?))"
        );
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "is", $id, $current_psikolog);
            mysqli_stmt_execute($stmt);
            $canceled_ok = mysqli_stmt_affected_rows($stmt) > 0;
            mysqli_stmt_close($stmt);
        }
    }
    $cancel_flag = $canceled_ok ? "1" : "0";
    header("Location: psikolog.php?page=dashboard&canceled={$cancel_flag}#jadwal-konseling");
    exit;
}


$jadwal = [];
$count_total = 0;
$count_upcoming = 0;
$count_done = 0;
if ($page === "dashboard") {
    $stmt = mysqli_prepare($conn, "SELECT COUNT(*) AS c FROM reservasi WHERE status='confirmed'");
    if ($stmt) {
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if ($res && ($row = mysqli_fetch_assoc($res))) {
            $count_total = (int)$row["c"];
        }
        mysqli_stmt_close($stmt);
    }

    $count_done = 0;
    $countDoneSql = "SELECT COUNT(*) AS c FROM catatan_klien WHERE LOWER(TRIM(status_sesi)) = 'selesai'";
    if ($current_psikolog !== "") {
        $countDoneSql .= " AND LOWER(TRIM(psikolog)) = LOWER(TRIM(?))";
    }
    $stmt = mysqli_prepare($conn, $countDoneSql);
    if ($stmt) {
        if ($current_psikolog !== "") {
            mysqli_stmt_bind_param($stmt, "s", $current_psikolog);
        }
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if ($res && ($row = mysqli_fetch_assoc($res))) {
            $count_done = (int)$row["c"];
        }
        mysqli_stmt_close($stmt);
    }

    $stmt = mysqli_prepare($conn, "SELECT COUNT(*) AS c FROM reservasi WHERE status='confirmed' AND tanggal >= ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $today);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if ($res && ($row = mysqli_fetch_assoc($res))) {
            $count_upcoming = (int)$row["c"];
        }
        mysqli_stmt_close($stmt);
    }

    $keluhan_column = "sumber_info";
    $check_keluhan_col = mysqli_query($conn, "SHOW COLUMNS FROM reservasi LIKE 'keluhan_kebutuhan'");
    if ($check_keluhan_col && mysqli_num_rows($check_keluhan_col) > 0) {
        $keluhan_column = "keluhan_kebutuhan";
    }

    $jadwalSql = "SELECT id, nama, tanggal, waktu, {$keluhan_column} AS keluhan_kebutuhan, status, psikolog
         FROM reservasi
         WHERE status IN ('confirmed', 'canceled') AND tanggal >= ?
         ORDER BY tanggal ASC, waktu ASC
         LIMIT 5";
    $stmt = mysqli_prepare($conn, $jadwalSql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $today);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $jadwal[] = $row;
            }
        }
        mysqli_stmt_close($stmt);
    }
}

$pasien = [];
if ($page === "klien") {
    $stmt = mysqli_prepare(
        $conn,
        "SELECT * FROM reservasi
         WHERE status = 'confirmed'
         ORDER BY id DESC"
    );
    if ($stmt) {
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $pasien[] = $row;
            }
        }
        mysqli_stmt_close($stmt);
    }
}

// Catatan Klien (psikolog)
$catatan_klien = [];
if ($page === "pesan") {
    $catatan_sql = "SELECT id, id_sesi, pasien, tanggal, waktu, jenis, status_sesi, psikolog, created_at
        FROM catatan_klien";
    if ($current_psikolog !== "") {
        $catatan_sql .= " WHERE LOWER(TRIM(psikolog)) = LOWER(TRIM(?))";
    }
    $catatan_sql .= " ORDER BY tanggal DESC, waktu DESC, id DESC";
    $stmt = mysqli_prepare($conn, $catatan_sql);
    if ($stmt) {
        if ($current_psikolog !== "") {
            mysqli_stmt_bind_param($stmt, "s", $current_psikolog);
        }
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $catatan_klien[] = $row;
            }
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!doctype html > 
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/psikolog.css?v=<?php echo filemtime(__DIR__ . '/assets/css/psikolog.css'); ?>">
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h3>Psychological Practice Hanna & Consultant</h3>

    <div class="menu-title">Menu Psikolog</div>

    <a class="menu-item <?php echo $active_menu === "dashboard" ? "active" : ""; ?>" href="psikolog.php?page=dashboard">
        <span class="icon"><img src="ikon/beranda.png" alt="dashboard"></span>
        <span>Dashboard</span>
    </a>

    <a class="menu-item <?php echo $active_menu === "klien" ? "active" : ""; ?>" href="psikolog.php?page=klien">
        <span class="icon"><img src="ikon/pasien.png" alt="klien"></span>
        <span>Klien Saya</span>
    </a>

    <a class="menu-item <?php echo $active_menu === "catatan" ? "active" : ""; ?>" href="psikolog.php?page=catatan">
        <span class="icon"><img src="ikon/catatan_sesi.png" alt="catatan sesi"></span>
        <span>Catatan Sesi</span>
    </a>

    <a class="menu-item <?php echo $active_menu === "pesan" ? "active" : ""; ?>" href="psikolog.php?page=pesan">
        <span class="icon"><img src="ikon/catatan_klien.png" alt="catatan klien"></span>
        <span>Catatan Klien</span>
    </a>

    <div class="menu-title">Akun</div>
    <a class="menu-item" href="profile_psikolog.php">
        <span class="icon"><img src="ikon/profil.png" alt="profil"></span>
        <span>Profil Saya</span>
    </a>

    <a class="menu-item" href="logout.php">
        <span class="icon"><img src="ikon/logout.png" alt="logout"></span>
        <span>Logout</span>
    </a>
</div>

<!-- CONTENT -->
<div class="content">

    <!-- HEADER -->
    <div class="header">
        <h2><?php echo htmlspecialchars($page_title); ?></h2>
    </div>

    <!-- KONTEN UTAMA -->
    <?php if ($page === "catatan"): ?>
        <?php include "catatan_sesi.php"; ?>
    <?php elseif ($page === "pesan"): ?>
        <div class="table-card" style="margin-top:24px;">
            <h3 style="margin:0 0 12px 0;">Daftar Catatan Klien</h3>
            <?php if (($_GET["saved"] ?? "") === "1"): ?>
                <div class="action-alert success" style="margin-bottom:12px;">Catatan berhasil disimpan.</div>
            <?php elseif (($_GET["saved"] ?? "") === "0"): ?>
                <div class="action-alert error" style="margin-bottom:12px;">Gagal menyimpan catatan.</div>
            <?php endif; ?>

            <?php
                $statusLabelMap = [
                    "selesai" => "Selesai",
                    "lanjut" => "Perlu Sesi Lanjutan",
                    "ditunda" => "Ditunda"
                ];
            ?>

            <table class="table-reservasi">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Pasien</th>
                        <th>Jenis</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($catatan_klien) === 0): ?>
                        <tr>
                            <td colspan="6">Belum ada catatan klien.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($catatan_klien as $row): ?>
                            <?php
                                $statusKey = strtolower((string)($row["status_sesi"] ?? "selesai"));
                                $statusLabel = $statusLabelMap[$statusKey] ?? "Selesai";
                                $tanggalRaw = (string)($row["tanggal"] ?? "");
                                if ($tanggalRaw === "" && !empty($row["created_at"])) {
                                    $tanggalRaw = date("Y-m-d", strtotime((string)$row["created_at"]));
                                }
                                if ($tanggalRaw === "") {
                                    $tanggalRaw = $today;
                                }
                                $tanggalDisplay = function_exists("format_catatan_date")
                                    ? format_catatan_date($tanggalRaw)
                                    : $tanggalRaw;
                                $editUrl = "psikolog.php?page=catatan&catatan_id=" . urlencode((string)($row["id"] ?? ""));
                                $printUrl = "catatan_cetak.php?id=" . urlencode((string)($row["id"] ?? ""));
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($tanggalDisplay); ?></td>
                                <td><?php echo htmlspecialchars($row["waktu"] ?? "-"); ?></td>
                                <td><?php echo htmlspecialchars($row["pasien"] ?? "-"); ?></td>
                                <td><?php echo htmlspecialchars($row["jenis"] ?? "-"); ?></td>
                                <td><?php echo htmlspecialchars($statusLabel); ?></td>
                                <td class="cell-action">
                                    <div class="action-wrap">
                                        <a class="btn-action btn-view" href="<?php echo htmlspecialchars($editUrl); ?>">Lihat</a>
                                        <a class="btn-action btn-print" href="<?php echo htmlspecialchars($printUrl); ?>" target="_blank" rel="noopener">Cetak PDF</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php elseif ($page === "klien"): ?>
        <div class="table-card" style="margin-top:24px;">
            <h3 style="margin:0 0 12px 0;">Daftar Klien Saya</h3>
            <table class="table-reservasi">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Jenis Kelamin</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th>Organisasi</th>
                        <th>Keluhan/Kebutuhan</th>
                        <th>Tanggal</th>
                        <th>Catatan Sesi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($pasien) === 0): ?>
                        <tr>
                        <td colspan="8">Belum ada data klien yang ditugaskan.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($pasien as $row): ?>
                            <?php
                                $catatanUrl = "psikolog.php?page=catatan&pasien=" . urlencode($row["nama"] ?? "")
                                    . "&tanggal=" . urlencode($row["tanggal"] ?? "")
                                    . "&waktu=" . urlencode($row["waktu"] ?? "")
                                    . "&id_sesi=" . urlencode($row["id"] ?? "");
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row["nama"] ?? "-"); ?></td>
                                <td><?php echo htmlspecialchars($row["jenis_kelamin"] ?? "-"); ?></td>
                                <td><?php echo htmlspecialchars($row["email"] ?? "-"); ?></td>
                                <td><?php echo htmlspecialchars($row["telepon"] ?? "-"); ?></td>
                                <td><?php echo htmlspecialchars($row["organisasi"] ?? "-"); ?></td>
                                <td><?php echo htmlspecialchars($row["keluhan_kebutuhan"] ?? ($row["sumber_info"] ?? "-")); ?></td>
                                <td><?php echo htmlspecialchars($row["tanggal"] ?? "-"); ?></td>
                                <td>
                                    <a class="btn-small" href="<?php echo htmlspecialchars($catatanUrl); ?>">Buka</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="dashboard-hero">
            <h2>Selamat Datang, <?php echo htmlspecialchars($_SESSION['nama']); ?></h2>
            <p>Jabatan: Psikolog</p>
        </div>

        <?php if (($_GET["canceled"] ?? "") === "1"): ?>
            <div class="action-alert success">Jadwal berhasil dibatalkan.</div>
        <?php elseif (($_GET["canceled"] ?? "") === "0"): ?>
            <div class="action-alert error">Gagal membatalkan jadwal.</div>
        <?php endif; ?>

        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-title">Jadwal Mendatang</div>
                <div class="stat-value"><?php echo $count_upcoming; ?></div>
                <div class="stat-sub">Sesi Mendatang</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Pasien Terjadwal</div>
                <div class="stat-value"><?php echo $count_total; ?></div>
                <div class="stat-sub">Pasien Terjadwal</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Sesi Selesai</div>
                <div class="stat-value"><?php echo $count_done; ?></div>
                <div class="stat-sub">Sesi Selesai</div>
            </div>
        </div>

        <div class="dashboard-grid no-side-panel">
            <div class="main-panel">
                <div class="table-card" id="jadwal-konseling">
                    <h3 style="margin:0 0 12px 0;">Jadwal Terdekat Konsultasi Anda</h3>
                    <table class="table-reservasi">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Waktu</th>
                                <th>Pasien</th>
                                <th>Keluhan/Kebutuhan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($jadwal) === 0): ?>
                                <tr>
                                    <td colspan="6">Belum ada jadwal terkonfirmasi.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($jadwal as $row): ?>
                                    <?php
                                        $detailUrl = "psikolog.php?page=catatan&pasien=" . urlencode($row["nama"] ?? "")
                                            . "&tanggal=" . urlencode($row["tanggal"] ?? "")
                                            . "&waktu=" . urlencode($row["waktu"] ?? "")
                                            . "&id_sesi=" . urlencode($row["id"] ?? "");
                                        $rowStatus = (string)($row["status"] ?? "confirmed");
                                        $statusLabel = $rowStatus === "canceled" ? "Dibatalkan" : "Dijadwalkan";
                                        $statusClass = $rowStatus === "canceled" ? "status-canceled" : "status-scheduled";
                                        $rowPsikolog = trim((string)($row["psikolog"] ?? ""));
                                        $canCancel = $rowStatus === "confirmed"
                                            && $current_psikolog !== ""
                                            && strcasecmp($rowPsikolog, $current_psikolog) === 0;
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row["tanggal"] ?? "-"); ?></td>
                                        <td><?php echo htmlspecialchars($row["waktu"] ?? "-"); ?></td>
                                        <td><?php echo htmlspecialchars($row["nama"] ?? "-"); ?></td>
                                        <td><?php echo htmlspecialchars($row["keluhan_kebutuhan"] ?? ($row["sumber_info"] ?? "-")); ?></td>
                                        <td><span class="status-pill <?php echo $statusClass; ?>"><?php echo htmlspecialchars($statusLabel); ?></span></td>
                                        <td class="cell-action">
                                            <div class="action-wrap">
                                                <?php if ($rowStatus !== 'canceled'): ?>
                                                    <a class="btn-action btn-view" href="<?php echo htmlspecialchars($detailUrl); ?>">Lihat Detail</a>
                                                <?php endif; ?>
                                                <?php if ($canCancel): ?>
                                                    <form method="POST" action="psikolog.php?page=dashboard" onsubmit="return confirm('Batalkan jadwal ini?');">
                                                        <input type="hidden" name="action" value="cancel_jadwal">
                                                        <input type="hidden" name="id" value="<?php echo (int)($row["id"] ?? 0); ?>">
                                                        <button type="submit" class="btn-action btn-cancel">Batalkan</button>
                                                    </form>
                                                <?php else: ?>
                                                    <span>-</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>

                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    <?php endif; ?>

</div>

</body>
</html>
