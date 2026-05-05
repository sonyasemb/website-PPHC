<?php
session_start();
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['psikolog', 'admin'], true)) {
    header("Location: login.php");
    exit;
}

require_once "koneksi.php";

$catatan_id = (int)($_GET["id"] ?? 0);
if ($catatan_id <= 0) {
    echo "Data catatan tidak ditemukan.";
    exit;
}

$current_psikolog = trim((string)($_SESSION["nama"] ?? ""));
$role = $_SESSION["role"] ?? "";

$sql = "SELECT * FROM catatan_klien WHERE id = ?";
if ($role === "psikolog" && $current_psikolog !== "") {
    $sql .= " AND LOWER(TRIM(psikolog)) = LOWER(TRIM(?))";
}
$sql .= " LIMIT 1";

$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    echo "Gagal memuat data catatan.";
    exit;
}
if ($role === "psikolog" && $current_psikolog !== "") {
    mysqli_stmt_bind_param($stmt, "is", $catatan_id, $current_psikolog);
} else {
    mysqli_stmt_bind_param($stmt, "i", $catatan_id);
}
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$catatan = $res ? mysqli_fetch_assoc($res) : null;
mysqli_stmt_close($stmt);

if (!$catatan) {
    echo "Data catatan tidak ditemukan.";
    exit;
}

$psikolog_nama = trim((string)($catatan["psikolog"] ?? ""));
if ($psikolog_nama === "") {
    $psikolog_nama = trim((string)($_SESSION["nama"] ?? "Psikolog"));
}
$psikolog_jabatan = trim((string)($_SESSION["jabatan"] ?? "Psikolog Klinis"));
if ($psikolog_jabatan === "") {
    $psikolog_jabatan = "Psikolog Klinis";
}
$psikolog_initials = "PS";
if ($psikolog_nama !== "") {
    $parts = preg_split("/\s+/", $psikolog_nama);
    $initials = "";
    foreach ($parts as $part) {
        if ($part === "") {
            continue;
        }
        $initials .= strtoupper(substr($part, 0, 1));
    }
    if ($initials !== "") {
        $psikolog_initials = substr($initials, 0, 2);
    }
}

$tanggal_db = (string)($catatan["tanggal"] ?? "");
$tanggal_display = "-";
if ($tanggal_db !== "") {
    $dt = DateTime::createFromFormat("Y-m-d", $tanggal_db);
    $tanggal_display = $dt ? $dt->format("d-m-Y") : $tanggal_db;
}

$status_key = strtolower((string)($catatan["status_sesi"] ?? "selesai"));
$status_label_map = [
    "selesai" => "Selesai",
    "lanjut" => "Perlu Sesi Lanjutan",
    "ditunda" => "Ditunda"
];
$status_class_map = [
    "selesai" => "status-selesai",
    "lanjut" => "status-lanjut",
    "ditunda" => "status-ditunda"
];
$status_label = $status_label_map[$status_key] ?? "Selesai";
$status_class = $status_class_map[$status_key] ?? "status-selesai";
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Catatan Klien</title>
    <link rel="stylesheet" href="assets/css/catatan_cetak.css?v=<?php echo filemtime(__DIR__ . '/assets/css/catatan_cetak.css'); ?>">
</head>
<body>
    <div class="catatan-print">
        <div class="print-topline"></div>
        <div class="print-header">
            <div class="print-title">Catatan Konsultasi</div>
            <div class="print-doctor">
                <div class="print-doctor-meta">
                    <div class="print-doctor-name"><?php echo htmlspecialchars($psikolog_nama); ?></div>
                    <div class="print-doctor-role"><?php echo htmlspecialchars($psikolog_jabatan); ?></div>
                </div>
                <div class="print-doctor-avatar" aria-hidden="true"><?php echo htmlspecialchars($psikolog_initials); ?></div>
            </div>
        </div>

        <div class="print-divider"></div>

        <div class="print-pasien">Pasien: <span><?php echo htmlspecialchars($catatan["pasien"] ?? "-"); ?></span></div>

        <div class="print-meta-grid">
            <div class="print-meta-item">
                <span class="print-meta-label">Tanggal:</span>
                <span><?php echo htmlspecialchars($tanggal_display); ?></span>
            </div>
            <div class="print-meta-item">
                <span class="print-meta-label">Waktu:</span>
                <span><?php echo htmlspecialchars($catatan["waktu"] ?? "-"); ?></span>
            </div>
            <div class="print-meta-item">
                <span class="print-meta-label">Jenis:</span>
                <span><?php echo htmlspecialchars($catatan["jenis"] ?? "-"); ?></span>
            </div>
            <div class="print-meta-item print-meta-item-status">
                <span class="print-badge <?php echo htmlspecialchars($status_class); ?>"><?php echo htmlspecialchars($status_label); ?></span>
            </div>
            <div class="print-meta-item span-2">
                <span class="print-meta-label">ID Sesi:</span>
                <span>#<?php echo htmlspecialchars($catatan["id_sesi"] ?? "-"); ?></span>
            </div>
            <div class="print-meta-item span-2">
                <span class="print-meta-label">Psikolog:</span>
                <span><?php echo htmlspecialchars($psikolog_nama); ?></span>
            </div>
        </div>

        <div class="print-section">
            <h4>Keluhan Utama</h4>
            <p class="print-text"><?php echo htmlspecialchars($catatan["keluhan"] ?? "-"); ?></p>
        </div>

        <div class="print-section">
            <h4>Observasi Psikolog</h4>
            <p class="print-text"><?php echo htmlspecialchars($catatan["observasi"] ?? "-"); ?></p>
        </div>

        <div class="print-section">
            <h4>Diagnosis / Temuan <span class="print-optional">(Opsional)</span></h4>
            <p class="print-text"><?php echo htmlspecialchars($catatan["diagnosis"] ?? "-"); ?></p>
        </div>

        <div class="print-section">
            <h4>Ringkasan Tindak Lanjut</h4>
            <div class="print-pill"><?php echo htmlspecialchars($catatan["ringkasan"] ?? "-"); ?></div>
        </div>

        <div class="print-section">
            <h4>Rencana Tindak Lanjut</h4>
            <p class="print-text"><?php echo htmlspecialchars($catatan["rencana"] ?? "-"); ?></p>
        </div>

        <div class="print-section">
            <h4>Rekomendasi / Terapi</h4>
            <p class="print-text"><?php echo htmlspecialchars($catatan["rekomendasi"] ?? "-"); ?></p>
        </div>

        <div class="print-footer">
            <div class="print-footer-info">
                <div class="print-footer-line">Status Sesi: <span><?php echo htmlspecialchars($status_label); ?></span></div>
            </div>
            <div class="print-signature">
                <img class="print-sign-image" src="" alt="Tanda tangan psikolog">
                <div class="print-sign-name"><?php echo htmlspecialchars($psikolog_nama); ?></div>
                <div class="print-sign-role"><?php echo htmlspecialchars($psikolog_jabatan); ?></div>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener("load", function () {
            window.print();
        });
    </script>
</body>
</html>
