<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require_once "koneksi.php";
$pasien = [];
$stmt = mysqli_prepare($conn, "SELECT * FROM reservasi WHERE status='confirmed' ORDER BY id DESC");
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
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pasien - Admin</title>
    <link rel="stylesheet" href="assets/css/admincss.css?v=<?php echo filemtime(__DIR__ . '/assets/css/admincss.css'); ?>">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <?php
        require "admin_sidebar.php";
    ?>

    <div class="content">
        <div class="header">
            <h1>Data Pasien</h1>
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
                        <th>Psikolog</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($pasien) === 0): ?>
                        <tr>
                            <td colspan="9">Belum ada data pasien (konfirmasi).</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($pasien as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row["nama"] ?? "-"); ?></td>
                                <td><?php echo htmlspecialchars($row["jenis_kelamin"] ?? "-"); ?></td>
                                <td><?php echo htmlspecialchars($row["email"] ?? "-"); ?></td>
                                <td><?php echo htmlspecialchars($row["telepon"] ?? "-"); ?></td>
                                <td><?php echo htmlspecialchars($row["organisasi"] ?? "-"); ?></td>
                                <td><?php echo htmlspecialchars($row["keluhan_kebutuhan"] ?? ($row["sumber_info"] ?? "-")); ?></td>
                                <td><?php echo htmlspecialchars($row["psikolog"] ?? "-"); ?></td>
                                <td><?php echo htmlspecialchars($row["tanggal"] ?? "-"); ?></td>
                                <td><?php echo htmlspecialchars($row["waktu"] ?? "-"); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
