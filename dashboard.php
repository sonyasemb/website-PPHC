<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PPHC - Halaman Utama</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/dashboard.css?v=<?php echo filemtime(__DIR__ . '/assets/css/dashboard.css'); ?>">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
</head>

<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light fixed-top navbar-pphc">
    <div class="container-fluid nav-shell px-4">
      <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="#">
        <img src="ikon/logopphc.png" alt="Logo PPHC" class="pphc-logo" width="34" height="34">
      </a>
      <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <ul class="navbar-nav nav-center nav-desktop d-none d-lg-flex">
        <li class="nav-item"><a class="nav-link active" href="#home">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="#tentang">Tentang PPHC</a></li>
        <li class="nav-item"><a class="nav-link" href="#tes-gratis">Tes Gratis</a></li>
        <li class="nav-item"><a class="nav-link" href="#komunitas">Komunitas</a></li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#layanan" id="layananDropdownDesktop" role="button"
            data-bs-toggle="dropdown" aria-expanded="false">Layanan</a>
          <ul class="dropdown-menu" aria-labelledby="layananDropdownDesktop">
            <li><a class="dropdown-item" href="#layanan-psikoterapi">Psikoterapi</a></li>
            <li><a class="dropdown-item" href="#layanan-psikotes">Tes Psikotes</a></li>
            <li><a class="dropdown-item" href="#layanan-mental-checkup">Mental Health Check Up</a></li>
            <li><a class="dropdown-item" href="#layanan-konseling">Konseling</a></li>
            <li><a class="dropdown-item" href="#layanan-seminar">Seminar,Training,Workshop</a></li>
          </ul>
        </li>
      </ul>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav nav-center nav-mobile mb-2 mb-lg-0 d-lg-none">
          <li class="nav-item"><a class="nav-link active" href="#home">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="#tentang">Tentang PPHC</a></li>
          <li class="nav-item"><a class="nav-link" href="#tes-gratis">Tes Gratis</a></li>
          <li class="nav-item"><a class="nav-link" href="#komunitas">Komunitas</a></li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#layanan" id="layananDropdownMobile" role="button"
              data-bs-toggle="dropdown" aria-expanded="false">Layanan</a>
            <ul class="dropdown-menu" aria-labelledby="layananDropdownMobile">
              <li><a class="dropdown-item" href="#layanan-psikoterapi">Psikoterapi</a></li>
              <li><a class="dropdown-item" href="#layanan-psikotes">Tes Psikotes</a></li>
              <li><a class="dropdown-item" href="#layanan-mental-checkup">Mental Health Check Up</a></li>
              <li><a class="dropdown-item" href="#layanan-konseling">Konseling</a></li>
              <li><a class="dropdown-item" href="#layanan-seminar">Seminar,Training,Workshop</a></li>
            </ul>
          </li>
        </ul>
        <a class="btn-nav-cta d-lg-none w-100 mt-3" href="login.php">Daftar</a>
      </div>
      <a class="btn-nav-cta d-none d-lg-inline-flex" href="login.php">Daftar</a>
    </div>
  </nav>

  <!-- Hero Section -->
  <section id="home" class="py-5 text-center">
    <div class="container">
      <h1 class="hero-title mb-3">Selamat Datang di  Psychological Practice Hanna & Consultant</h1>
      <p class="hero-text mb-4">Kami Menyediakan Layanan Psikologi, Konseling, Psikoterapi, Pengembangan Diri.</p>
      <a href="#layanan" class="btn-pphc">Jelajahi Layanan Kami</a>
    </div>
  </section>

  <section id="tentang" class="py-5">
    <div class="container-fluid px-4">
      <div class="row align-items-center gy-4">

        <!-- KOLOM TEKS -->
        <div class="col-lg-8 tentang-text">
          <h1 class="fw-bold mb-8">Tentang PPHC</h1>

          <div class="tentang-box">
            <p>
              Kami Memiliki Tujuan Memberikan Layanan Yanng Mengedepankan Etika dan Kualitas Terbaik
             <b><i>"Melayani dengan profesionalitas"</i> </b>
            </p>
          </div>

          <a href="#komunitas" class="btn-pphc btn-pphc-pink mt-2">
            Kenali Kami Lebih Lanjut
          </a>
        </div>

        <!-- KOLOM GAMBAR -->
        <div class="col-lg-5 tentang-visual">
          <img src="ikon/founder.png" alt="Tentang PPHC" class="img-fluid w-100">
        </div>

      </div>
    </div>
  </section>

  <section id="layanan" class="py-5">
    <div class="container">
      <h1 class="section-title text-center mb-5">Layanan Kami</h1>

      <!-- ROW UTAMA -->
      <div class="row g-4 justify-content-center">

        <!-- Psikoterapi -->
        <div class="col-md-4" id="layanan-konseling">
          <div class="card layanan-card h-100">
            <div class="layanan-media">
              <img src="ikon/konseling.jpg" alt="Konseling">
            </div>
            <div class="layanan-body">
              <div class="layanan-icon" aria-hidden="true">
                <i data-lucide="puzzle"></i>
              </div>
              <h5 class="layanan-title">Konseling</h5>
              <div class="layanan-divider"></div>
              <p class="layanan-text">Membantu pasangan dan individu membangun hubungan yang lebih sehat.</p>
              <a href="user.php?layanan=Konseling" class="btn-nav-cta">Booking sekarang</a>
            </div>
          </div>
        </div>

        <!-- Tes Psikotes -->
        <div class="col-md-4" id="layanan-psikoterapi">
          <div class="card layanan-card h-100">
            <div class="layanan-media">
              <img src="ikon/psyco.jpg" alt="Psikoterapi">
            </div>
            <div class="layanan-body">
              <div class="layanan-icon" aria-hidden="true">
                <i data-lucide="message-circle-heart"></i>
              </div>
              <h5 class="layanan-title">Psikoterapi</h5>
              <div class="layanan-divider"></div>
              <p class="layanan-text">Layanan asesmen psikologi profesional.</p>
              <a href="user.php?layanan=Psikoterapi" class="btn-nav-cta">Booking sekarang</a>
            </div>
          </div>
        </div>

        <!-- Konsultasi Psikologi -->
        <div class="col-md-4" id="layanan-psikotes">
          <div class="card layanan-card h-100">
            <div class="layanan-media">
              <img src="ikon/psikotes.jpg" alt="Tes Psikotes">
            </div>
            <div class="layanan-body">
              <div class="layanan-icon" aria-hidden="true">
                <i data-lucide="star"></i>
              </div>
              <h5 class="layanan-title">Tes Psikotes</h5>
              <div class="layanan-divider"></div>
              <p class="layanan-text">Ruang aman untuk individu dan keluarga.</p>
              <a href="user.php?layanan=Tes%20Psikotes" class="btn-nav-cta">Booking sekarang</a>
            </div>
          </div>
        </div>

        <!-- LAYANAN TAMBAHAN -->
        <div class="collapse mt-4" id="layananTambahan">
          <div class="row g-4 justify-content-center">
            <div class="col-md-4" id="layanan-mental-checkup">
              <div class="card layanan-card h-100">
                <div class="layanan-media">
                  <img src="ikon/mental.jpg" alt="Mental Health Check Up">
                </div>
                <div class="layanan-body">
                  <div class="layanan-icon" aria-hidden="true">
                    <i data-lucide="ribbon"></i>
                  </div>
                  <h5 class="layanan-title">Mental Health Check Up</h5>
                  <div class="layanan-divider"></div>
                  <p class="layanan-text">Asesmen psikologi profesional.</p>
                  <a href="user.php?layanan=Mental%20Health%20Check%20Up" class="btn-nav-cta">Booking
                    sekarang</a>
                </div>
              </div>
            </div>

            <div class="col-md-4" id="layanan-seminar">
              <div class="card layanan-card h-100">
                <div class="layanan-media">
                  <img src="ikon/seminar.jpg" alt="Seminar, Training, Workshop">
                </div>
                <div class="layanan-body">
                  <div class="layanan-icon" aria-hidden="true">
                    <i data-lucide="clipboard-plus"></i>
                  </div>
                  <h5 class="layanan-title">Seminar, Training, Workshop</h5>
                  <div class="layanan-divider"></div>
                  <p class="layanan-text">Program edukasi dan pelatihan psikologi.</p>
                  <a href="user.php?layanan=Seminar%2C%20Training%2C%20Workshop" class="btn-nav-cta">Booking
                    sekarang</a>
                </div>
              </div>
            </div>

          </div>
        </div>

        <div class="col-12 d-flex justify-content-end mt-4">
          <button class="btn btn-light border px-4 py-2 btn-layanan-more" data-bs-toggle="collapse"
            data-bs-target="#layananTambahan" aria-label="Lihat semua layanan">
            <span class="btn-layanan-more-text">More</span>
            <i data-lucide="square-arrow-right" aria-hidden="true"></i>
          </button>
        </div>

      </div>
  </section>

  <!-- Tes Skrinning -->
  <section id="tes-gratis" class="py-5">
    <div class="container">
      <h2 class="tes-gratis-title text-center">Yuk.. Tes Skrinning perasaan kamu</h2>
      <div class="tes-gratis-box d-flex align-items-center justify-content-between">
        <img src="ikon/tes.png" alt="Ilustrasi Tes" class="tes-gratis-image">
<a href="javascript:void(0)" onclick="bukaPopupSkrining()" class="btn-pphc tes-gratis-btn d-inline-flex align-items-center gap-3 text-nowrap">
          Ikuti Tes
          <i data-lucide="smile" aria-hidden="true" class="tes-gratis-icon"></i>
        </a>

        <!-- Popup Validasi Usia -->
        <div id="popupUsia" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999;">
            <div style="background:white; padding:20px; border-radius:8px; max-width:400px; margin:15% auto; text-align:center;">
                <h3>Konfirmasi Usia</h3>
                <p>Apakah Anda berusia 15 tahun atau lebih?</p>
                <button onclick="konfirmasiUsia()" class="btn btn-primary">Ya</button>
                <button onclick="tolakUsia()" class="btn btn-secondary">Tidak</button>
            </div>
        </div>

        <!-- Popup Informasi Skrining -->
        <div id="popupSkrining" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999;">
            <div style="background:white; padding:20px; border-radius:8px; max-width:400px; margin:15% auto; text-align:center;">
                <h3>Skrining Kesehatan Mental</h3>
                <p>Skrining ini bertujuan untuk mengetahui kondisi kesehatan mental Anda berdasarkan pengalaman dalam 30 hari terakhir.</p>
                <p>Jawablah setiap pertanyaan dengan jujur agar hasil lebih akurat.</p>
                <button onclick="masukHalamanSkrining()" class="btn btn-primary">Ikuti Tes</button>
                <button onclick="batalSkrining()" class="btn btn-secondary">Batal</button>
            </div>
        </div>

        <!-- Popup Pesan Error -->
        <div id="popupPesan" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999;">
            <div style="background:white; padding:20px; border-radius:8px; max-width:400px; margin:15% auto; text-align:center;">
                <h3>Informasi</h3>
                <p id="isiPesan"></p>
                <button onclick="tutupPopup()" class="btn btn-primary">OK</button>
            </div>
        </div>

        <script>
        function bukaPopupSkrining() {
            document.getElementById('popupUsia').style.display = 'block';
        }

        function konfirmasiUsia() {
            document.getElementById('popupUsia').style.display = 'none';
            document.getElementById('popupSkrining').style.display = 'block';
        }

        function tolakUsia() {
            document.getElementById('popupUsia').style.display = 'none';
            document.getElementById('isiPesan').innerText = 'Maaf, layanan ini diperuntukkan bagi pengguna usia 15 tahun ke atas.';
            document.getElementById('popupPesan').style.display = 'block';
        }

        function masukHalamanSkrining() {
            document.getElementById('popupSkrining').style.display = 'none';
            window.location.href = 'skrinning.php';
        }

        function batalSkrining() {
            document.getElementById('popupSkrining').style.display = 'none';
        }

        function tutupPopup() {
            document.getElementById('popupPesan').style.display = 'none';
        }
        </script>
      </div>
    </div>
  </section>

  <!-- Komunitas Section -->
  <section id="komunitas" class="py-5">
    <div class="container text-center">
      <h1 class="section-title mb-4">Komunitas Cegah Bunuh Diri</h1>
      <p class="mb-5">Kami percaya bahwa dukungan sosial dan komunitas adalah bagian penting dari pemulihan dan
        pertumbuhan pribadi. Bergabunglah dengan berbagai program komunitas yang kami adakan secara rutin.</p>
      <div class="row g-4">

        <div class="col-md-4">
          <div class="card h-80 rounded-3 shadow-sm">
            <div class="card-body text-center">
              <div class="komunitas-slider" aria-label="Galeri Support Group">
                <div class="komunitas-slide is-active">
                  <img src="ikon/kc.jpg" alt="Support Group">
                </div>
                <div class="komunitas-slide">
                  <img src="ikon/kolab.jpg" alt="Support Group">
                </div>
                <div class="komunitas-dots" aria-hidden="true">
                  <span class="komunitas-dot is-active"></span>
                  <span class="komunitas-dot"></span>
                </div>
              </div>
              <h5 class="card-title">
                <a href="https://www.instagram.com/komunitascegahbunuhdiri/?__pwa=1" class="text-decoration-none">
                  Support Group
                </a>
              </h5>
              <p class="card-text">
                Kelompok diskusi terbuka untuk berbagi pengalaman dan saling mendukung
                dalam proses pemulihan.
              </p>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card h-100 rounded-4 shadow-sm">
            <div class="card-body text-center">
              <div class="komunitas-slider" aria-label="Galeri Seminar, Workshop, Training">
                <div class="komunitas-slide is-active">
                  <img src="ikon/odsk.jpeg" alt="Seminar, Workshop, Training">
                </div>
                <div class="komunitas-slide">
                  <img src="ikon/online.jpg" alt="Seminar, Workshop, Training">
                </div>
                <div class="komunitas-dots" aria-hidden="true">
                  <span class="komunitas-dot is-active"></span>
                  <span class="komunitas-dot"></span>
                </div>
              </div>
              <h5 class="card-title">
                <a href="https://www.instagram.com/reel/DTpEdPijZtR/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA=="
                  class="text-decoration-none">
                  Seminar, Workshop, Training
                </a>
              </h5>
              <p class="card-text">Pelatihan dan seminar mengenai pengembangan diri, manajemen stres, dan komunikasi
                efektif.</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card h-100 rounded-4 shadow-sm">
            <div class="card-body text-center">
              <div class="komunitas-slider" aria-label="Galeri Relawan PPHC">
                <div class="komunitas-slide is-active">
                  <img src="ikon/run.jpeg" alt="Relawan PPHC">
                </div>
                <div class="komunitas-slide">
                  <img src="ikon/gtc.jpg" alt="Relawan PPHC">
                </div>
                <div class="komunitas-dots" aria-hidden="true">
                  <span class="komunitas-dot is-active"></span>
                  <span class="komunitas-dot"></span>
                </div>
              </div>
              <h5 class="card-title">
                <a href="https://www.instagram.com/p/DTZK08YD9K6/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA=="
                  class="text-decoration-none">
                  Relawan PPHC
                </a>
              </h5>
              <p class="card-text">Kesempatan menjadi bagian dari tim yang membantu masyarakat melalui kegiatan sosial
                dan edukatif.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!--Daftar PSIKOLOG -->
  <section id="psikolog" class="psikolog-section py-5">
    <div class="container">
      <div class="psikolog-header text-center mb-5">
        <h1 class="psikolog-title">Yuk Kenalan Dengan <span>Psikolog Kami</span></h1>
        <div class="psikolog-rating" aria-label="Rating 5 bintang di Google">
          <div class="rating-stars" aria-hidden="true">
            <i data-lucide="star" class="rating-star"></i>
            <i data-lucide="star" class="rating-star"></i>
            <i data-lucide="star" class="rating-star"></i>
            <i data-lucide="star" class="rating-star"></i>
            <i data-lucide="star" class="rating-star"></i>
          </div>
          <span class="rating-text">5.0 •</span>
        </div>
        <p class="psikolog-subtitle">Psikolog berpengalaman yang siap mendampingi dan menangani banyak klien dengan
          pendekatan yang empatik.</p>
      </div>

      <div class="swiper psikologSwiper">
        <div class="swiper-wrapper">

          <!-- CARD 1 -->
          <div class="swiper-slide">
            <div class="psikolog-card" onclick="openPsikolog()">
              <div class="psikolog-photo">
                <img src="ikon/aryani.png" alt="Ariany, M.Psi., Psikolog">
              </div>
              <div class="psikolog-body">
                <h5>Ariany, M.Psi., Psikolog</h5>
                <span class="badge">Psikolog Klinis</span>
                <div class="praktik">
                  <i data-lucide="calendar-heart" aria-hidden="true"></i>
                  <span>4 Tahun Praktik</span>
                </div>
                <div class="sesi">
                  <span>By Appointment</span>
                  <span>Offline</span>
                  <span>Telekonseling</span>
                </div>
                <button class="btn-detail">Lihat Detail</button>
              </div>
            </div>
          </div>

          <!-- CARD 2 -->
          <div class="swiper-slide">
            <div class="psikolog-card" onclick="openPsikolog()">
              <div class="psikolog-photo">
                <img src="ikon/vera.png" alt=<"Vera, M.Psi., Psikolog">
              </div>
             <div class="psikolog-body">
                <h5>Vera, M.Psi., Psikolog</h5>
                <span class="badge">Psikolog Klinis</span>
                <div class="praktik">
                  <i data-lucide="calendar-heart" aria-hidden="true"></i>
                  <span>8 Tahun Praktik</span>
                </div>
                <div class="sesi">
                  <span>By Appointment</span>
                </div>
                <button class="btn-detail">Lihat Detail</button>
              </div>
            </div>
          </div>

          <!-- CARD 3 -->
          <div class="swiper-slide">
            <div class="psikolog-card" onclick="openPsikolog()">
              <div class="psikolog-photo">
                <img src="ikon/hanna.png" alt="Hanna, M.Psi., Psikolog">
              </div>
              <div class="psikolog-body">
                <h5>Hanna, M.Psi., Psikolog</h5>
                <span class="badge">Psikolog Klinis</span>
                <div class="praktik">
                  <i data-lucide="calendar-heart" aria-hidden="true"></i>
                  <span>14 Tahun Praktik</span>
                </div>
                <div class="sesi">
                  <span>By Appointment</span>
                  <span>Offline</span>
                  <span>Telekonseling</span>
                </div>
                <button class="btn-detail">Lihat Detail</button>
              </div>
            </div>
          </div>

          <!-- CARD 4 -->
          <div class="swiper-slide">
            <div class="psikolog-card" onclick="openPsikolog()">
              <div class="psikolog-photo">
                <img src="ikon/grace.png" alt="Grace, M.Psi., Psikolog">
              </div>
              <div class="psikolog-body">
                <h5>Grace, M.Psi., Psikolog</h5>
                <span class="badge">Psikolog Klinis</span>
                <div class="praktik">
                  <i data-lucide="calendar-heart" aria-hidden="true"></i>
                  <span>7 Tahun Praktik</span>
                </div>
                <div class="sesi">
                  <span>Telekonseling</span>
                </div>
                <button class="btn-detail">Lihat Detail</button>
              </div>
            </div>
          </div>

          <!-- CARD 6 -->
          <div class="swiper-slide">
            <div class="psikolog-card" onclick="openPsikolog()">
              <div class="psikolog-photo">
                <img src="ikon/ridha.png" alt="Ridha, M.Psi., Psikolog">
              </div>
              <div class="psikolog-body">
                <h5>Ridha, M.Psi., Psikolog</h5>
                <span class="badge">Psikolog Klinis</span>
                <div class="praktik">
                  <i data-lucide="calendar-heart" aria-hidden="true"></i>
                  <span>2 Tahun Praktik</span>
                </div>
                <div class="sesi">
                  <span>Offline</span>
                  <span>Telekonseling</span>
                </div>
                <button class="btn-detail">Lihat Detail</button>
              </div>
            </div>
          </div>
        </div>

        <!-- NAVIGATION -->
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
      </div>
    </div>
  </section>

  <!-- ===============================
     MODAL DETAIL PSIKOLOG
================================ -->
  <div class="modal-psikolog" id="modalPsikolog" onclick="closePsikolog()">
    <div class="modal-box" onclick="event.stopPropagation()">
      <button class="close-btn" onclick="closePsikolog()">×</button>

      <h3>Hanna, M.Psi., Psikolog</h3>
      <p><strong>Lama Praktik:</strong> 14 Tahun</p>

      <p><strong>Bidang & Kendala Ditangani:</strong></p>
      <ul>
        <li>Gangguan kecemasan</li>
        <li>Depresi ringan – sedang</li>
        <li>Masalah relasi & keluarga</li>
        <li>Trauma psikologis</li>
      </ul>

    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

  <script>
    const swiper = new Swiper(".psikologSwiper", {
      slidesPerView: 4,
      spaceBetween: 24,
      loop: true,
      navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
      },
      breakpoints: {
        0: {
          slidesPerView: 1,
        },
        576: {
          slidesPerView: 2,
        },
        992: {
          slidesPerView: 4,
        },
      },
    });

    function openPsikolog() {
      document.getElementById("modalPsikolog").style.display = "flex";
    }

    function closePsikolog() {
      document.getElementById("modalPsikolog").style.display = "none";
    }
  </script>


  <!-- Daftar Section -->
  <section id="daftar" class="py-5 bg-light">
    <div class="container text-center">
      <h1 class="fw-bold mb-4">Daftar Sekarang</h1>
      <h4 class="mb-4"><i>Ingin bergabung dengan layanan kami? Silakan isi formulir pendaftaran untuk memulai perjalanan
          Anda bersama PPHC.</i></h4>
      <a href="login.php" class="btn-nav-cta">Daftar Sekarang</a>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-dark text-light py-4">
    <div class="container text-center">
      <p class="mb-0">&copy; 2025 PPHC. Semua Hak Dilindungi.</p>
      <small class="text-secondary">Dikembangkan dengan cinta dan kepedulian terhadap kesehatan mental.</small>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <script>
    if (window.lucide) {
      window.lucide.createIcons();
    }

    (function () {
      var sliders = document.querySelectorAll("#komunitas .komunitas-slider");
      sliders.forEach(function (slider) {
        var slides = slider.querySelectorAll(".komunitas-slide");
        var dots = slider.querySelectorAll(".komunitas-dot");
        if (slides.length < 2) {
          return;
        }

        var index = 0;
        setInterval(function () {
          slides[index].classList.remove("is-active");
          if (dots[index]) {
            dots[index].classList.remove("is-active");
          }

          index = (index + 1) % slides.length;
          slides[index].classList.add("is-active");
          if (dots[index]) {
            dots[index].classList.add("is-active");
          }
        }, 3500);
      });
    })();
  </script>
</body>

</html>
