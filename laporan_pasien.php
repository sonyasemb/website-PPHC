<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require_once "koneksi.php";

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

$filter_psikolog = trim((string)($_GET["psikolog"] ?? ""));
$filter_tanggal_raw = trim((string)($_GET["tanggal"] ?? ""));
$filter_jenis = trim((string)($_GET["jenis"] ?? ""));
$is_print = (($_GET["print"] ?? "") === "1");

$filter_tanggal_default = $filter_tanggal_raw !== "" ? $filter_tanggal_raw : date("Y-m-d");
$apply_filter = isset($_GET["psikolog"]) || isset($_GET["tanggal"]) || isset($_GET["jenis"]) || $is_print;
$filter_tanggal = $apply_filter ? normalize_catatan_date($filter_tanggal_raw) : null;

$psikolog_options = [];
$jenis_options = [];

$sql_psikolog_options = <<<SQL
SELECT DISTINCT psikolog
FROM catatan_klien
WHERE psikolog IS NOT NULL
  AND psikolog <> ''
ORDER BY psikolog ASC
SQL;
$result = mysqli_query($conn, $sql_psikolog_options);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $psikolog_options[] = $row["psikolog"];
    }
    mysqli_free_result($result);
}

$sql_jenis_options = <<<SQL
SELECT DISTINCT jenis
FROM catatan_klien
WHERE jenis IS NOT NULL
  AND jenis <> ''
ORDER BY jenis ASC
SQL;
$result = mysqli_query($conn, $sql_jenis_options);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $jenis_options[] = $row["jenis"];
    }
    mysqli_free_result($result);
}

$laporan_list = [];
$sql = <<<SQL
SELECT
    id,
    pasien,
    psikolog,
    tanggal,
    waktu,
    jenis,
    status_sesi,
    created_at
FROM catatan_klien
SQL;
$where = [];
$params = [];
$types = "";
if ($filter_psikolog !== "") {
    $where[] = "psikolog = ?";
    $params[] = $filter_psikolog;
    $types .= "s";
}
if ($filter_tanggal) {
    $where[] = "tanggal = ?";
    $params[] = $filter_tanggal;
    $types .= "s";
}
if ($filter_jenis !== "") {
    $where[] = "jenis = ?";
    $params[] = $filter_jenis;
    $types .= "s";
}
if ($where) {
    $sql .= "\nWHERE " . implode(" AND ", $where);
}
$sql .= "\nORDER BY tanggal DESC, waktu DESC, id DESC";

$stmt = mysqli_prepare($conn, $sql);
if ($stmt) {
    switch (count($params)) {
        case 1:
            mysqli_stmt_bind_param($stmt, $types, $params[0]);
            break;
        case 2:
            mysqli_stmt_bind_param($stmt, $types, $params[0], $params[1]);
            break;
        case 3:
            mysqli_stmt_bind_param($stmt, $types, $params[0], $params[1], $params[2]);
            break;
    }
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $laporan_list[] = $row;
        }
    }
    mysqli_stmt_close($stmt);
}
?><!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Laporan Pasien</title>
    <link rel="stylesheet" href="assets/css/admincss.css?v=<?php echo filemtime(__DIR__ . '/assets/css/admincss.css'); ?>">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        @media print {
            .sidebar,
            .header,
            .filter-box {
                display: none !important;
            }
            .content {
                margin: 0 !important;
                padding: 0 !important;
            }
            body {
                background: #fff !important;
            }
        }
    </style>
</head>
<body>
    <?php
        require "admin_sidebar.php";
    ?>

    <div class="content">
        <div class="header">
            <h1>Laporan Pasien</h1>
        </div>

        <div class="filter-box" id="laporan">
            <h2>Filter Laporan</h2>
            <form class="filter-grid" method="get" action="laporan_pasien.php">
                <div class="filter-item" style="position:relative;">
                    <label>Psikolog</label>
                    <select name="psikolog" style="padding-left:35px;">
                        <option value="">Pilih</option>
                        <?php foreach ($psikolog_options as $ps): ?>
                            <option value="<?php echo htmlspecialchars($ps); ?>" <?php echo $filter_psikolog === $ps ? "selected" : ""; ?>>
                                <?php echo htmlspecialchars($ps); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-item" style="position:relative;">
                    <label>Tanggal</label>
                    <input type="date" name="tanggal" value="<?php echo htmlspecialchars($filter_tanggal_default); ?>" style="padding-left:35px;" />
                </div>

                <div class="filter-item" style="position:relative;">
                    <label>Layanan</label>
                    <select name="jenis" style="padding-left:35px;">
                        <option value="">Pilih</option>
                        <?php foreach ($jenis_options as $jenis): ?>
                            <option value="<?php echo htmlspecialchars($jenis); ?>" <?php echo $filter_jenis === $jenis ? "selected" : ""; ?>>
                                <?php echo htmlspecialchars($jenis); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-item" style="position:relative;">
                    <label>&nbsp;</label>
                    <button type="submit" style="padding-left:35px; text-align:left;">Filter</button>
                </div>

                <div class="filter-item" style="position:relative;">
                    <label>&nbsp;</label>
                    <button type="submit" name="print" value="1" style="background:#e53935; padding-left:35px; text-align:left;">Download PDF</button>
                </div>
            </form>
        </div>

        <div class="table-card">
            <table class="table-reservasi">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Pasien</th>
                        <th>Psikolog</th>
                        <th>Jenis</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($laporan_list) === 0): ?>
                        <tr>
                            <td colspan="7">Belum ada laporan catatan pasien.</td>
                        </tr>
                    <?php else: ?>
                        <?php
                            $statusLabelMap = [
                                "selesai" => "Selesai",
                                "lanjut" => "Perlu Sesi Lanjutan",
                                "ditunda" => "Ditunda"
                            ];
                        ?>
                        <?php foreach ($laporan_list as $row): ?>
                            <?php
                                $statusKey = strtolower((string)($row["status_sesi"] ?? "selesai"));
                                $statusLabel = $statusLabelMap[$statusKey] ?? "Selesai";
                                $printUrl = "catatan_cetak.php?id=" . urlencode((string)($row["id"] ?? ""));
                                $tanggalDisplay = $row["tanggal"] ?? "";
                                if ($tanggalDisplay === "" && !empty($row["created_at"])) {
                                    $tanggalDisplay = date("Y-m-d", strtotime((string)$row["created_at"]));
                                }
                                if ($tanggalDisplay === "") {
                                    $tanggalDisplay = date("Y-m-d");
                                }
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars(format_catatan_date($tanggalDisplay)); ?></td>
                                <td><?php echo htmlspecialchars($row["waktu"] ?? "-"); ?></td>
                                <td><?php echo htmlspecialchars($row["pasien"] ?? "-"); ?></td>
                                <td><?php echo htmlspecialchars($row["psikolog"] ?? "-"); ?></td>
                                <td><?php echo htmlspecialchars($row["jenis"] ?? "-"); ?></td>
                                <td><?php echo htmlspecialchars($statusLabel); ?></td>
                                <td>
                                    <a class="btn-small" href="<?php echo htmlspecialchars($printUrl); ?>" target="_blank" rel="noopener">Cetak PDF</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($is_print): ?>
            <script>
                window.addEventListener("load", function () {
                    window.print();
                });
            </script>
        <?php endif; ?>
    </div>
</body>
</html>
