<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require_once "koneksi.php";

function isoToTanggalIndo(string $isoDate): string
{
    $dt = DateTime::createFromFormat("Y-m-d", $isoDate);
    if ($dt === false) {
        return "";
    }

    $bulan_indo = [
        1 => "Januari",
        2 => "Februari",
        3 => "Maret",
        4 => "April",
        5 => "Mei",
        6 => "Juni",
        7 => "Juli",
        8 => "Agustus",
        9 => "September",
        10 => "Oktober",
        11 => "November",
        12 => "Desember"
    ];

    $bulan = $bulan_indo[(int)$dt->format("n")] ?? "";
    if ($bulan === "") {
        return "";
    }

   
    return ((int)$dt->format("j")) . " " . $bulan . " " . $dt->format("Y");
}


$psikologList = [];
$resP = mysqli_query($conn, "SELECT nama FROM users WHERE role='psikolog' ORDER BY nama ASC");
if ($resP) {
    while ($row = mysqli_fetch_assoc($resP)) {
        $namaPsikolog = trim((string)($row["nama"] ?? ""));
        if ($namaPsikolog !== "") {
            $psikologList[] = $namaPsikolog;
        }
    }
}


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"])) {
    $action = (string)($_POST["action"] ?? "");
    $redirectQs = str_replace(["\r", "\n"], "", (string)($_POST["redirect_qs"] ?? ""));
    $redirectUrl = "reservasi.php";
    if ($redirectQs !== "") {
        $redirectUrl .= "?" . ltrim($redirectQs, "?");
    }

    if ($action === "update_status") {
        $id = intval($_POST["id"] ?? 0);
        $status = $_POST["status"] ?? "pending";

        if ($id > 0 && in_array($status, ["pending", "confirmed", "canceled"], true)) {
            $stmt = mysqli_prepare($conn, "UPDATE reservasi SET status=? WHERE id=?");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "si", $status, $id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
    } elseif ($action === "update_psikolog") {
        $id = intval($_POST["id"] ?? 0);
        $psikolog = trim((string)($_POST["psikolog"] ?? ""));

        if ($id > 0 && $psikolog !== "" && in_array($psikolog, $psikologList, true)) {
            $stmt = mysqli_prepare($conn, "UPDATE reservasi SET psikolog=? WHERE id=?");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "si", $psikolog, $id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
    }

    header("Location: " . $redirectUrl);
    exit;
}


$tanggalFilter = $_GET["tanggal"] ?? "";
$layananFilter = $_GET["layanan"] ?? "";
$redirectParams = [];
if ($tanggalFilter !== "") {
    $redirectParams["tanggal"] = $tanggalFilter;
}
if ($layananFilter !== "") {
    $redirectParams["layanan"] = $layananFilter;
}
$redirectQs = http_build_query($redirectParams);
$has_layanan_column = false;
$check_col = mysqli_query($conn, "SHOW COLUMNS FROM reservasi LIKE 'layanan'");
if ($check_col && mysqli_num_rows($check_col) > 0) {
    $has_layanan_column = true;
}


$layananList = [];
$resL = mysqli_query($conn, "SELECT nama_layanan FROM layanan ORDER BY nama_layanan ASC");
if ($resL) {
    while ($row = mysqli_fetch_assoc($resL)) {
        $layananList[] = $row["nama_layanan"];
    }
}


$tanggalList = [];
$resT = mysqli_query($conn, "SELECT DISTINCT tanggal FROM reservasi ORDER BY tanggal ASC");
if ($resT) {
    while ($row = mysqli_fetch_assoc($resT)) {
        $tanggalList[] = $row["tanggal"];
    }
}


$query = "SELECT * FROM reservasi WHERE 1=1";
$params = [];
$types = "";

if ($tanggalFilter !== "") {
    $query .= " AND (tanggal = ?";
    $params[] = $tanggalFilter;
    $types .= "s";

    $tanggalFilterIndo = isoToTanggalIndo($tanggalFilter);
    if ($tanggalFilterIndo !== "" && $tanggalFilterIndo !== $tanggalFilter) {
        $query .= " OR tanggal = ?";
        $params[] = $tanggalFilterIndo;
        $types .= "s";
    }

    $query .= ")";
}

if ($layananFilter !== "") {
    $query .= $has_layanan_column
        ? " AND COALESCE(NULLIF(layanan, ''), psikolog) = ?"
        : " AND psikolog = ?";
    $params[] = $layananFilter;
    $types .= "s";
}

$query .= " ORDER BY id DESC";

$stmt = mysqli_prepare($conn, $query);
if ($stmt && $types !== "") {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

$reservasi = [];
if ($stmt) {
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $reservasi[] = $row;
        }
    }
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservasi - Admin</title>
    <link rel="stylesheet" href="assets/css/admincss.css?v=<?php echo filemtime(__DIR__ . '/assets/css/admincss.css'); ?>">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <?php
        require "admin_sidebar.php";
    ?>

    <div class="content">
        <div class="header">
            <h1>Daftar Reservasi</h1>
        </div>

        <div class="filter-box">
            <h2>Filter Reservasi</h2>
            <form class="filter-grid" method="GET" action="reservasi.php">
                <div class="filter-item">
                    <label>Layanan</label>
                    <select name="layanan">
                        <option value="">Semua</option>
                        <?php foreach ($layananList as $layanan_item): ?>
                            <option value="<?php echo htmlspecialchars($layanan_item); ?>" <?php echo $layanan_item === $layananFilter ? "selected" : ""; ?>>
                                <?php echo htmlspecialchars($layanan_item); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-item">
                    <label>Tanggal</label>
                    <input type="date" name="tanggal" value="<?php echo htmlspecialchars($tanggalFilter); ?>">
                </div>

                <div class="filter-item">
                    <label>&nbsp;</label>
                    <button type="submit">Filter</button>
                </div>
            </form>
        </div>

        <div class="table-card">
            <table class="table-reservasi">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Layanan</th>
                        <th>Telepon</th>
                        <th style="white-space: nowrap;">Psikolog</th>
                        <th style="white-space: nowrap;">Tanggal</th>
                        <th>Waktu</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($reservasi) === 0): ?>
                        <tr>
                            <td colspan="8">Belum ada data reservasi.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($reservasi as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row["nama"]); ?></td>
                                <td><?php echo htmlspecialchars($row["layanan"] ?? $row["psikolog"]); ?></td>
                                <td><?php echo htmlspecialchars($row["telepon"]); ?></td>
                                <td style="white-space: nowrap;">
                                    <?php $currentPsikolog = trim((string)($row["psikolog"] ?? "")); ?>
                                    <form method="POST" action="reservasi.php">
                                        <input type="hidden" name="action" value="update_psikolog">
                                        <input type="hidden" name="id" value="<?php echo (int)$row["id"]; ?>">
                                        <input type="hidden" name="redirect_qs" value="<?php echo htmlspecialchars($redirectQs, ENT_QUOTES); ?>">
                                        <select name="psikolog" onchange="this.form.submit()" style="min-width: 200px; white-space: nowrap;" <?php echo count($psikologList) === 0 ? "disabled" : ""; ?>>
                                            <?php if (count($psikologList) === 0): ?>
                                                <option value="" selected>Belum ada psikolog</option>
                                            <?php else: ?>
                                                <option value="" <?php echo $currentPsikolog === "" ? "selected" : ""; ?>>Pilih psikolog</option>
                                                <?php if ($currentPsikolog !== "" && !in_array($currentPsikolog, $psikologList, true)): ?>
                                                    <option value="<?php echo htmlspecialchars($currentPsikolog); ?>" selected>
                                                        <?php echo htmlspecialchars($currentPsikolog); ?> (tidak terdaftar)
                                                    </option>
                                                <?php endif; ?>
                                                <?php foreach ($psikologList as $ps): ?>
                                                    <option value="<?php echo htmlspecialchars($ps); ?>" <?php echo $currentPsikolog === $ps ? "selected" : ""; ?>>
                                                        <?php echo htmlspecialchars($ps); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </form>
                                </td>
                                <td style="white-space: nowrap;"><?php echo htmlspecialchars($row["tanggal"]); ?></td>
                                <td><?php echo htmlspecialchars($row["waktu"]); ?></td>
                                <td>
                                    <?php if ($row["status"] === "confirmed"): ?>
                                        <span class="status-badge status-confirmed">confirmed</span>
                                    <?php else: ?>
                                        <span class="status-badge status-pending">pending</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($row["status"] !== "confirmed"): ?>
                                        <form method="POST" action="reservasi.php">
                                            <input type="hidden" name="action" value="update_status">
                                            <input type="hidden" name="id" value="<?php echo (int)$row["id"]; ?>">
                                            <input type="hidden" name="status" value="confirmed">
                                            <input type="hidden" name="redirect_qs" value="<?php echo htmlspecialchars($redirectQs, ENT_QUOTES); ?>">
                                            <button class="btn-small" type="submit">Confirm</button>
                                        </form>
                                    <?php else: ?>
                                        <span>-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
