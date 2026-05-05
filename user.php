<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}
$layanan_terpilih = trim((string)($_GET["layanan"] ?? "Konseling"));

?>
<!DOCTYPE html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Booking Konsultasi - PPHC</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/user.css">
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top shadow-sm">
  <div class="container-fluid px-2">
    <a class="navbar-brand nav-brand" href="dashboard.php">
      <img src="ikon/logopphc.png" alt="Logo PPHC">
      <span href="dashboard.php">Psychological Practice Hanna & Consultant </span>
    </a>
    <a href="logout.php" class="btn-nav-cta">Logout</a>
  </div>
</nav>

<div class="container">
  <div class="text-center mb-4">
    <h2 class="fw-bold mb-1">Jadwalkan Konsultasi Anda</h2>
    <p class="text-muted mb-0">Periksa ketersediaan jadwal psikolog kami terlebih dahulu</p>
  </div>

  <div class="row g-4">

    <!-- Kalender -->
    <div class="col-md-7">
      <label class="fw-semibold mb-2">Pilih Tanggal & Jam</label>

      <div class="calendar-box page-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <button id="prev-month" class="btn btn-outline-secondary btn-sm"><</button>
          <h6 id="calendar-title" class="mb-0 fw-bold">Kalender</h6>
          <button id="next-month" class="btn btn-outline-secondary btn-sm">></button>
        </div>

        <!-- Kalender -->
        <div class="calendar-container">
          <table class="table table-bordered text-center mb-0">
            <thead class="table-light">
              <tr>
                <th>Min</th><th>Sen</th><th>Sel</th><th>Rab</th><th>Kam</th><th>Jum</th><th>Sab</th>
              </tr>
            </thead>
            <tbody id="calendar-body"></tbody>
          </table>
        </div>

        <!-- Jam -->
        <div class="text-center mt-3">
          <div class="time-slot">09:00</div>
          <div class="time-slot">10:00</div>
          <div class="time-slot">11:00</div>
          <div class="time-slot">12:00</div>
          <div class="time-slot">13:00</div>
          <div class="time-slot">14:00</div>
          <div class="time-slot">15:00</div>
        </div>
      </div>


      <div id="summary-box" class="mt-3">
        <h6 class="fw-bold mb-1">Ringkasan Pilihan Anda:</h6>
        <p id="summary-text" class="mb-0"></p>
      </div>
    </div>

  
    <div class="col-md-5">
      <label class="fw-semibold mb-2 d-block invisible">Pilih Tanggal & Jam</label>
      <div class="card page-card">
        <div class="card-body">
          <h5 class="fw-bold"> Reservasi</h5>
          <hr>
          <div class="detail-list">
            <div class="detail-row">
              <div class="detail-label">Layanan</div>
              <div class="detail-value"><?php echo htmlspecialchars($layanan_terpilih); ?></div>
            </div>
            <div class="detail-row">
              <div class="detail-label">Biaya</div>
              <div class="detail-value">IDR 250.000 - 350.000</div>
            </div>
            <div class="detail-row">
              <label for="tipeKonseling" class="detail-label mb-0">Tipe</label>
              <div class="detail-value">
                <select id="tipeKonseling" class="form-select form-select-sm">
                  <option value="Tatap Muka" selected>Tatap Muka</option>
                  <option value="Telekonseling">Telekonseling</option>
                </select>
              </div>
            </div>
            <div class="detail-row">
              <div class="detail-label">Durasi</div>
              <div class="detail-value">1 jam</div>
            </div>
          </div>

          <button id="lanjutBtn" type="button" class="btn btn-primary w-100 mt-2">Lanjut</button>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- ================= JAVASCRIPT ================= -->

<script>
  window.USER_BOOKING_CONFIG = {
    layanan: <?php echo json_encode($layanan_terpilih); ?>
  };
</script>
<script src="assets/js/user.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

