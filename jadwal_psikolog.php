<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require_once "koneksi.php";

if (!function_exists("normalize_reservasi_date")) {
    function normalize_reservasi_date(string $value): ?string
    {
        $value = trim($value);
        if ($value === "") {
            return null;
        }
        if (preg_match("/^\d{4}-\d{2}-\d{2}$/", $value)) {
            return $value;
        }
        if (preg_match("/^\d{1,2}[\/-]\d{1,2}[\/-]\d{4}$/", $value)) {
            $clean = str_replace("/", "-", $value);
            $dt = DateTime::createFromFormat("d-m-Y", $clean);
            if ($dt !== false) {
                return $dt->format("Y-m-d");
            }
        }
        if (preg_match("/^(\d{1,2})\s+([[:alpha:].]+)\s+(\d{4})$/u", $value, $match)) {
            $bulan_map = [
                "januari" => 1,
                "februari" => 2,
                "maret" => 3,
                "april" => 4,
                "mei" => 5,
                "juni" => 6,
                "juli" => 7,
                "agustus" => 8,
                "september" => 9,
                "oktober" => 10,
                "november" => 11,
                "desember" => 12
            ];
            $day = (int)$match[1];
            $month_key = strtolower(str_replace(".", "", trim($match[2])));
            $year = (int)$match[3];
            $month = $bulan_map[$month_key] ?? 0;
            if ($day > 0 && $month > 0 && $year > 0) {
                return sprintf("%04d-%02d-%02d", $year, $month, $day);
            }
        }
        return null;
    }
}

if (!function_exists("normalize_reservasi_time")) {
    function normalize_reservasi_time(string $value): ?string
    {
        $value = trim($value);
        if ($value === "") {
            return null;
        }
        if (preg_match("/(\d{1,2})\s*[:.]\s*(\d{2})/", $value, $match)) {
            $hour = (int)$match[1];
            if ($hour >= 0 && $hour <= 23) {
                return sprintf("%02d:00", $hour);
            }
        }
        if (preg_match("/^\d{1,2}$/", $value)) {
            $hour = (int)$value;
            if ($hour >= 0 && $hour <= 23) {
                return sprintf("%02d:00", $hour);
            }
        }
        return null;
    }
}

// Ambil list psikolog dari tabel users
$psikologList = [];
$resP = mysqli_query($conn, "SELECT nama FROM users WHERE role='psikolog' ORDER BY nama ASC");
if ($resP) {
    while ($row = mysqli_fetch_assoc($resP)) {
        $psikologList[] = $row["nama"];
    }
}

$jadwal_filter_psikolog = trim((string)($_GET["jadwal_psikolog"] ?? ""));
$jadwal_ref_raw = trim((string)($_GET["jadwal_ref"] ?? date("Y-m-d")));
$jadwal_ref = preg_match("/^\d{4}-\d{2}-\d{2}$/", $jadwal_ref_raw) ? $jadwal_ref_raw : date("Y-m-d");
$jadwal_ref_dt = DateTime::createFromFormat("Y-m-d", $jadwal_ref);
if ($jadwal_ref_dt === false) {
    $jadwal_ref_dt = new DateTime("today");
}
$jadwal_week_start = clone $jadwal_ref_dt;
$day_number = (int)$jadwal_week_start->format("N");
if ($day_number > 1) {
    $jadwal_week_start->modify("-" . ($day_number - 1) . " days");
}
$jadwal_week_end = clone $jadwal_week_start;
$jadwal_week_end->modify("+6 days");
$jadwal_week_start_iso = $jadwal_week_start->format("Y-m-d");
$jadwal_week_end_iso = $jadwal_week_end->format("Y-m-d");
$jadwal_range_text = $jadwal_week_start->format("d/m/Y") . " - " . $jadwal_week_end->format("d/m/Y");

$jadwal_hours = [];
for ($hour = 9; $hour <= 15; $hour++) {
    $jadwal_hours[] = sprintf("%02d:00", $hour);
}

$jadwal_hari_labels = ["Sen", "Sel", "Rab", "Kam", "Jum", "Sab", "Min"];
$jadwal_day_headers = [];
for ($i = 0; $i < 7; $i++) {
    $date_item = clone $jadwal_week_start;
    if ($i > 0) {
        $date_item->modify("+" . $i . " days");
    }
    $jadwal_day_headers[] = [
        "label" => $jadwal_hari_labels[$i],
        "iso" => $date_item->format("Y-m-d"),
        "display" => $date_item->format("d/m")
    ];
}

$jadwal_psikolog_options = [];
$psikolog_map = [];
foreach ($psikologList as $psikolog_nama) {
    $nama = trim((string)$psikolog_nama);
    if ($nama !== "") {
        $psikolog_map[strtolower($nama)] = $nama;
    }
}
$resAssigned = mysqli_query($conn, "SELECT DISTINCT psikolog FROM reservasi WHERE psikolog IS NOT NULL AND psikolog <> '' ORDER BY psikolog ASC");
if ($resAssigned) {
    while ($row = mysqli_fetch_assoc($resAssigned)) {
        $nama = trim((string)($row["psikolog"] ?? ""));
        if ($nama !== "") {
            $psikolog_map[strtolower($nama)] = $nama;
        }
    }
}
$jadwal_psikolog_options = array_values($psikolog_map);
natcasesort($jadwal_psikolog_options);

$jadwal_booked = [];
$jadwal_sql = "SELECT psikolog, tanggal, waktu, status
    FROM reservasi
    WHERE psikolog IS NOT NULL
      AND psikolog <> ''
      AND status <> 'canceled'";
$jadwal_params = [];
$jadwal_types = "";
if ($jadwal_filter_psikolog !== "") {
    $jadwal_sql .= " AND LOWER(TRIM(psikolog)) = LOWER(TRIM(?))";
    $jadwal_params[] = $jadwal_filter_psikolog;
    $jadwal_types .= "s";
}
$jadwal_stmt = mysqli_prepare($conn, $jadwal_sql);
if ($jadwal_stmt) {
    if ($jadwal_types === "s") {
        mysqli_stmt_bind_param($jadwal_stmt, "s", $jadwal_params[0]);
    }
    mysqli_stmt_execute($jadwal_stmt);
    $jadwal_result = mysqli_stmt_get_result($jadwal_stmt);
    if ($jadwal_result) {
        while ($row = mysqli_fetch_assoc($jadwal_result)) {
            $date_iso = normalize_reservasi_date((string)($row["tanggal"] ?? ""));
            $hour_slot = normalize_reservasi_time((string)($row["waktu"] ?? ""));
            if ($date_iso === null || $hour_slot === null) {
                continue;
            }
            if ($date_iso < $jadwal_week_start_iso || $date_iso > $jadwal_week_end_iso) {
                continue;
            }
            if (!in_array($hour_slot, $jadwal_hours, true)) {
                continue;
            }
            $jadwal_booked[$hour_slot][$date_iso] = true;
        }
    }
    mysqli_stmt_close($jadwal_stmt);
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Psikolog - Admin</title>
    <link rel="stylesheet" href="assets/css/admincss.css?v=<?php echo filemtime(__DIR__ . '/assets/css/admincss.css'); ?>">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <?php
        require "admin_sidebar.php";
    ?>

    <div class="content">
        <div class="header">
            <h1>Jadwal Psikolog</h1>
        </div>

        <div class="table-card jadwal-card" id="jadwal-grid">
            <h2>Jadwal Psikolog Mingguan</h2>
            <div class="jadwal-toolbar">
                <form class="jadwal-filter" method="get" action="jadwal_psikolog.php#jadwal-grid">
                    <div class="jadwal-filter-item">
                        <label>Psikolog</label>
                        <select name="jadwal_psikolog">
                            <option value="">Semua Psikolog</option>
                            <?php foreach ($jadwal_psikolog_options as $ps): ?>
                                <option value="<?php echo htmlspecialchars($ps); ?>" <?php echo $jadwal_filter_psikolog === $ps ? "selected" : ""; ?>>
                                    <?php echo htmlspecialchars($ps); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="jadwal-filter-item">
                        <label>Tanggal Acuan</label>
                        <input type="date" name="jadwal_ref" value="<?php echo htmlspecialchars($jadwal_ref); ?>">
                    </div>
                    <div class="jadwal-filter-item">
                        <label>&nbsp;</label>
                        <button type="submit">Tampilkan</button>
                    </div>
                </form>
                <div class="jadwal-week-label">Minggu: <?php echo htmlspecialchars($jadwal_range_text); ?></div>
            </div>

            <div class="jadwal-table-wrap">
                <table class="jadwal-grid">
                    <thead>
                        <tr>
                            <th>Jam</th>
                            <?php foreach ($jadwal_day_headers as $day): ?>
                                <th><?php echo htmlspecialchars($day["label"]); ?><br><small><?php echo htmlspecialchars($day["display"]); ?></small></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($jadwal_hours as $hour): ?>
                            <tr>
                                <td class="jadwal-hour"><?php echo htmlspecialchars($hour); ?></td>
                                <?php foreach ($jadwal_day_headers as $day): ?>
                                    <?php $is_booked = !empty($jadwal_booked[$hour][$day["iso"]]); ?>
                                    <td class="jadwal-slot-cell">
                                        <span class="jadwal-slot <?php echo $is_booked ? "is-booked" : ""; ?>">
                                            <?php echo $is_booked ? "&#10003;" : ""; ?>
                                        </span>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>
