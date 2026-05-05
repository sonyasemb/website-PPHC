<?php
$layanan = trim((string)($_GET["layanan"] ?? ""));
if ($layanan === "") {
  $layanan = "-";
}
$tipe = trim((string)($_GET["tipe"] ?? ""));
if ($tipe === "") {
  $tipe = "-";
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Booking Form - PPHC</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/form.css">
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top shadow-sm">
  <div class="container-fluid px-2">
    <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="dashboard.php">
      <img src="ikon/logopphc.png" alt="Logo PPHC" width="60" height="60" style="object-fit:cover; border-radius:6px;">
<span style="color: #08327b !important;"> Psychological Practice Hanna & Consultant </span>
    </a>
  </div>
</nav>

<div class="container">

  <div class="text-center mb-4">
    <h2 class="fw-bold mb-1">Booking Form</h2>
     <p class="text-muted mb-0">silahkan isi form box yang tesedia untuk memenuhi data reservasi</p>
  </div>

  <form class="row g-4" method="POST" action="reservasi_simpan.php">

    <!-- Kolom Kiri -->
    <div class="col-md-7">
      <div class="form-box shadow-sm">

        <h5 class="fw-bold mb-3">Detail Klien</h5>

        <div class="mb-3">
          <label class="form-label">Nama Lengkap</label>
          <input type="text" class="form-control" name="nama" placeholder="Masukkan nama Anda" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Jenis Kelamin</label>
          <select class="form-select" name="jenis_kelamin" required>
            <option value="" selected disabled>Pilih jenis kelamin</option>
            <option value="Laki-laki">Laki-laki</option>
            <option value="Perempuan">Perempuan</option>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" class="form-control" name="email" placeholder="email@example.com" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Telepon (08xxxx)</label>
          <input type="text" class="form-control" name="telepon" placeholder="Nomor telepon" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Organisasi / Perusahaan (optional)</label>
          <input type="text" class="form-control" name="organisasi" placeholder="Jika dipesan oleh perusahaan">
        </div>

        <div class="mb-3">
          <label class="form-label">Keluhan/kebutuhan</label>
          <textarea class="form-control" name="keluhan_kebutuhan" rows="3" placeholder="sertakan kebutuhan anda(*jika perlu)"></textarea>
        </div>
  
        <input type="hidden" name="layanan" value="<?php echo htmlspecialchars($layanan); ?>">
        <input type="hidden" name="tipe" value="<?php echo htmlspecialchars($tipe); ?>">
        <input type="hidden" name="tanggal" id="date-input" value="">
        <input type="hidden" name="waktu" id="time-input" value="">

        <button id="simpanBtn" type="submit" class="btn btn-primary w-100 mt-2">
          Simpan Reservasi
        </button>

      </div>
    </div>

    <!-- Kolom Kanan -->
    <div class="col-md-5">
      <div class="summary-box shadow-sm">

        <h5 class="fw-bold mb-2">Detail Reservasi</h5>
        <hr>

        <p><strong>Layanan:</strong> <?php echo htmlspecialchars($layanan); ?></p>
        <p><strong>Tanggal:</strong> <span id="date-text">-</span></p>
        <p><strong>Waktu:</strong> <span id="time-text">-</span></p>

        <div class="accordion" id="acc">
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" 
                      data-bs-toggle="collapse" data-bs-target="#collapse1">
                More Details
              </button>
            </h2>
            <div id="collapse1" class="accordion-collapse collapse">
              <div class="accordion-body">
                <p>Pembayaran: Transfer Bank</p>
                <p><strong>Notes: </strong>Hallo temakasih telah melakukan reservasi, Pembayaran dan detail Psikolog akan di arahkan ke WhatsApp oleh Admin PPHC</p>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
  const params = new URLSearchParams(window.location.search);
  const dateVal = params.get("date") || "-";
  const timeVal = params.get("time") || "-";
  document.getElementById("date-text").textContent = dateVal;
  document.getElementById("time-text").textContent = timeVal;
  document.getElementById("date-input").value = dateVal;
  document.getElementById("time-input").value = timeVal;
</script>

</body>
</html>
