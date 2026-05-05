<?php
require_once "koneksi.php";

$error = $_GET["error"] ?? "";
$query = null;
$kandidat_tabel = ["pertanyaan", "skrinning.pertanyaan"];

foreach ($kandidat_tabel as $nama_tabel) {
    try {
        $query = mysqli_query($conn, "SELECT * FROM {$nama_tabel} ORDER BY kode_gejala ASC");
        if ($query) {
            break;
        }
    } catch (mysqli_sql_exception $e) {
        $query = null;
    }
}

$total_pertanyaan = $query ? mysqli_num_rows($query) : 0;
?>

<!DOCTYPE html>
<html>

<head>
    <title>Tes Skrining SRQ-20</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/skrinning.css">
</head>

<body>

    <div class="container">

        <h2>Tes Skrining Kesehatan Mental (SRQ-20)</h2>

        <?php if ($error === "1") { ?>
            <p class="pesan-error pesan-error-jarak">
                Jawaban belum lengkap. Silakan isi semua pertanyaan terlebih dahulu.
            </p>
        <?php } ?>

        <?php if (!$query) { ?>
            <p class="pesan-error">
                Gagal memuat pertanyaan SRQ-20. Coba lagi nanti.
            </p>
        <?php } elseif ($total_pertanyaan === 0) { ?>
            <p class="pesan-error">
                Pertanyaan SRQ-20 belum tersedia.
            </p>
        <?php } else { ?>

            <form method="POST" action="skrinning_proses.php">

                <?php
                while ($row = mysqli_fetch_assoc($query)) {
                    ?>

                    <div class="pertanyaan">

                        <p>
                            <b><?php echo $row['kode_gejala']; ?></b>
                            <?php echo $row['pertanyaan']; ?>
                        </p>

                        <label>
                            <input type="radio" name="jawaban[<?php echo $row['kode_gejala']; ?>]" value="1" required>
                            Ya
                        </label>

                        <label>
                            <input type="radio" name="jawaban[<?php echo $row['kode_gejala']; ?>]" value="0">
                            Tidak
                        </label>

                    </div>

                <?php } ?>

                <div class="aksi-submit">
                    <button type="submit" class="btn-hasil-skrinning">Lihat Hasil</button>
                </div>

            </form>

        <?php } ?>

    </div>
</body>

</html>
