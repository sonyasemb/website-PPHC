<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'psikolog') {
    header("Location: login.php");
    exit;
}

require_once "koneksi.php";

$pasien = [];
$nama_psikolog = $_SESSION['nama'];
$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM reservasi
     WHERE status IN ('pending','confirmed')
       AND LOWER(TRIM(psikolog)) = LOWER(TRIM(?))
     ORDER BY id DESC"
);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $nama_psikolog);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $pasien[] = $row;
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
    <title>Klien Saya - Psikolog</title>
    <link rel="stylesheet" href="assets/css/admincss.css?v=<?php echo filemtime(__DIR__ . '/assets/css/admincss.css'); ?>">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <div class="sidebar">
        <div class="brand">
            <img src="ikon/logopphc.png" alt="Logo PPHC">
            <span>PPHC</span>
        </div>

        <div class="menu-title">Menu Psikolog</div>

        <a class="menu-item" href="dashboard.php">
            <span>Dashboard</span>
        </a>

        <div class="menu-item">
            <span>Jadwal Konseling</span>
        </div>

        <a class="menu-item" href="klien.php">
            <span>Klien Saya</span>
        </a>

        <div class="menu-item">
            <span>Catatan Sesi</span>
        </div>

        <div class="menu-item">
            <span>Pesan</span>
        </div>

        <div class="menu-item">
            <span>Profil Saya</span>
        </div>

        <a class="menu-item" href="logout.php">
            <span>Logout</span>
        </a>
    </div>

    <div class="content">
        <div class="header">
            <h1>Klien Saya</h1>
        </div>

        <div class="table-card">
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
                        <th>Waktu</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($pasien) === 0): ?>
                        <tr>
                            <td colspan="8">Belum ada data klien yang ditugaskan.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($pasien as $row): ?>
                            <tr>
                                <td>
                                    <a href="catatan_sesi.php?pasien=<?php echo urlencode($row["nama"] ?? ""); ?>&tanggal=<?php echo urlencode($row["tanggal"] ?? ""); ?>&waktu=<?php echo urlencode($row["waktu"] ?? ""); ?>&id_sesi=<?php echo urlencode($row["id"] ?? ""); ?>" style="color: #2c7be5; text-decoration: none; font-weight: 500;">
                                        <?php echo htmlspecialchars($row["nama"] ?? "-"); ?>
                                    </a>
                                </td>
                                <td><?php echo htmlspecialchars($row["jenis_kelamin"] ?? "-"); ?></td>
                                <td><?php echo htmlspecialchars($row["email"] ?? "-"); ?></td>
                                <td><?php echo htmlspecialchars($row["telepon"] ?? "-"); ?></td>
                                <td><?php echo htmlspecialchars($row["organisasi"] ?? "-"); ?></td>
                                <td><?php echo htmlspecialchars($row["keluhan_kebutuhan"] ?? ($row["sumber_info"] ?? "-")); ?></td>
                                <td><?php echo htmlspecialchars($row["tanggal"] ?? "-"); ?></td>
                            <td><?php echo htmlspecialchars($row["waktu"] ?? "-"); ?></td>
                            <td><?php echo htmlspecialchars($row["status"] ?? "-"); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
