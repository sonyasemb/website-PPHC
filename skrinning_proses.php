<?php
require_once "koneksi.php";

function getPertanyaanTable(mysqli $conn): ?string
{
    $kandidat_tabel = ["pertanyaan", "skrinning.pertanyaan"];

    foreach ($kandidat_tabel as $nama_tabel) {
        try {
            $uji_query = mysqli_query($conn, "SELECT 1 FROM {$nama_tabel} LIMIT 1");
            if ($uji_query !== false) {
                mysqli_free_result($uji_query);
                return $nama_tabel;
            }
        } catch (mysqli_sql_exception $e) {
            
        }
    }

    return null;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: /webpphc2/skrinning.php");
    exit;
}

$jawaban = $_POST["jawaban"] ?? null;
if (!is_array($jawaban) || count($jawaban) === 0) {
    header("Location: /webpphc2/skrinning.php?error=1");
    exit;
}

$expected = 0;
$pertanyaan_tabel = getPertanyaanTable($conn);

if ($pertanyaan_tabel !== null) {
    $count_query = mysqli_query($conn, "SELECT COUNT(*) AS jumlah FROM {$pertanyaan_tabel}");
    if ($count_query) {
        $row = mysqli_fetch_assoc($count_query);
        $expected = (int) ($row["jumlah"] ?? 0);
        mysqli_free_result($count_query);
    }
}

if ($expected > 0 && count($jawaban) !== $expected) {
    header("Location: /webpphc2/skrinning.php?error=1");
    exit;
}

$total = 0;
foreach ($jawaban as $nilai) {
    $total += ($nilai === "1" || $nilai === 1) ? 1 : 0;
}

if ($total >= 6) {
    $judul_hasil = "Perlu Perhatian";
    $hasil = "Hasil skrining ini menunjukkan bahwa kamu mungkin sedang mengalami beberapa tekanan emosional atau kelelahan mental.\n\nPerlu diingat, hasil ini bukan diagnosis, melainkan gambaran awal dari kondisi yang sedang kamu rasakan.
    Jika kamu membutuhkan ruang aman untuk bercerita dan mendapat dukungan, psikolog kami siap membantu dengan aman dan tanpa menghakimi.";
} else {
    $judul_hasil = "Hasil Bagus";
    $hasil = "Hasil skrining menunjukkan kamu tidak terindikasi gangguan mental emosional.\n\nTetap jaga pola hidup sehat, istirahat cukup, dan jangan ragu mencari bantuan jika suatu saat merasa terbebani.";
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Hasil Skrining</title>
    <link rel="stylesheet" href="assets/css/skrinning.css">
</head>

<body class="hasil-srq">

    <div class="hasil-card">
        <h2>Hasil Tes Skrining SRQ-20</h2>

        <h3 class="hasil-judul"><?php echo $judul_hasil; ?></h3>
        <p class="hasil-pesan"><?php echo nl2br(htmlspecialchars($hasil, ENT_QUOTES, "UTF-8")); ?></p>

        <div class="aksi-hasil">
            <a href="/webpphc2/skrinning.php" class="btn-hasil-skrinning">Ulangi Tes Saya</a>
            <a href="/webpphc2/dashboard.php#layanan" class="btn-hasil-skrinning">Dapatkan Bantuan Professional</a>
        </div>
    </div>

</body>

</html>
