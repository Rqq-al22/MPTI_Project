<?php
// index.php - Landing Page
// Sistem Informasi Rekap Kerja Praktik (KP) Mahasiswa Berbasis Web
// Aman untuk XAMPP + Alias /MPTI_Project
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Sistem Informasi Rekap Kerja Praktik (KP) Mahasiswa</title>

  <!-- BASE URL (XAMPP Alias) -->
  <base href="/MPTI_Project/">

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800&family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">

  <!-- Bootstrap 5 -->
  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/bootstrap-icons.css" rel="stylesheet">

  <!-- Optional: your global style (if exists) -->
  <link href="assets/css/style.css" rel="stylesheet">

<style>
  :root{
    /* === COLOR SYSTEM === */
    --tosca-1:#0ea5a4;     /* tosca utama */
    --tosca-2:#14b8a6;     /* tosca cerah */
    --tosca-soft:#e6fffb;  /* tosca sangat lembut */

    --dark:#1f2933;        /* abu gelap */
    --dark-soft:#374151;

    --white:#ffffff;
    --bg:#f8fafc;
    --border:#e5e7eb;

    --text-dark:#1f2937;
    --text-muted:#6b7280;
  }

  body{
    font-family:'Open Sans',sans-serif;
    background:var(--bg);
    color:var(--text-dark);
  }

  .font-head{ font-family:'Montserrat',sans-serif; }

  /* ================= NAVBAR ================= */
  .navbar-blur{
    background:rgba(255,255,255,.95);
    backdrop-filter:blur(10px);
    border-bottom:1px solid var(--border);
  }

  .brand-badge{
    width:42px;height:42px;border-radius:12px;
    background:linear-gradient(135deg,var(--tosca-1),var(--tosca-2));
    display:flex;align-items:center;justify-content:center;
    color:#fff;font-weight:800;
    box-shadow:0 10px 25px rgba(20,184,166,.35);
  }

  /* ================= HERO ================= */
  .hero{
    padding:90px 0 70px;
    background:
      radial-gradient(900px 400px at 15% 20%, rgba(20,184,166,.25), transparent 60%),
      linear-gradient(135deg,#0f766e,#115e59);
    color:#ffffff;
  }

  .hero .lead{
    color:rgba(255,255,255,.9);
  }

  .hero-card{
    background:rgba(255,255,255,.10);
    border:1px solid rgba(255,255,255,.25);
    border-radius:20px;
    padding:28px;
    backdrop-filter:blur(6px);
  }

  .hero-illus{
    background:rgba(255,255,255,.10);
    border:1px solid rgba(255,255,255,.25);
    border-radius:20px;
    padding:20px;
  }

  .illus-grid{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:14px;
  }

  .illus-tile{
    background:rgba(255,255,255,.12);
    border:1px solid rgba(255,255,255,.25);
    border-radius:16px;
    padding:16px;
    min-height:120px;
    color:#ffffff;
  }

  .illus-tile i{
    font-size:1.6rem;
    color:#ffffff;
  }

  .illus-tile .t{ font-weight:800;margin-top:8px; }
  .illus-tile .s{ font-size:.9rem;color:rgba(255,255,255,.85);margin:0; }

  /* ================= SECTIONS ================= */
  .section{ padding:70px 0; }

  .section-title{
    font-weight:800;
    letter-spacing:-.3px;
  }

  .section-sub{
    color:var(--text-muted);
    margin:0;
  }

  /* ================= FEATURE ================= */
  .feature-card{
    background:#ffffff;
    border:1px solid var(--border);
    border-radius:18px;
    padding:20px;
    height:100%;
    box-shadow:0 16px 40px rgba(31,41,55,.06);
  }

  .icon-pill{
    width:46px;height:46px;border-radius:14px;
    display:flex;align-items:center;justify-content:center;
    background:var(--tosca-soft);
    border:1px solid rgba(20,184,166,.35);
    color:var(--tosca-1);
    font-size:1.35rem;
  }

  .feature-card h6{
    font-weight:800;
    margin-top:14px;
  }

  .feature-card p{
    color:var(--text-muted);
    font-size:.95rem;
    margin:0;
  }

  /* ================= TIMELINE ================= */
  .steps{
    border-left:3px solid rgba(20,184,166,.35);
    padding-left:20px;
  }

  .step{
    background:#ffffff;
    border:1px solid var(--border);
    border-radius:16px;
    padding:16px;
    margin-bottom:14px;
    box-shadow:0 14px 35px rgba(31,41,55,.06);
    position:relative;
  }

  .step:before{
    content:"";
    position:absolute;
    left:-30px;top:18px;
    width:14px;height:14px;
    border-radius:50%;
    background:linear-gradient(135deg,var(--tosca-1),var(--tosca-2));
  }

  .step .k{ font-weight:800;margin:0 0 4px 0; }
  .step .d{ margin:0;color:var(--text-muted);font-size:.95rem; }

  /* ================= TEAM ================= */
  .team{
    background:linear-gradient(135deg,#0f766e,#134e4a);
    color:#ffffff;
  }

  .team .section-sub{ color:rgba(255,255,255,.85); }

  .team-card{
    background:rgba(255,255,255,.10);
    border:1px solid rgba(255,255,255,.25);
    border-radius:20px;
    padding:20px;
  }

  .member{
    display:flex;
    gap:12px;
    align-items:center;
    padding:12px;
    border-radius:16px;
    background:rgba(255,255,255,.10);
    border:1px solid rgba(255,255,255,.25);
    margin-bottom:10px;
  }

  .num{
    width:42px;height:42px;border-radius:50%;
    display:flex;align-items:center;justify-content:center;
    font-weight:800;
    background:#ffffff;
    color:var(--tosca-1);
  }

  .member .name{ font-weight:800;margin:0;color:#fff; }
  .member .nim{ margin:0;color:rgba(255,255,255,.85);font-size:.92rem; }

  /* ================= ADV ================= */
  .adv-badge{
    display:inline-flex;
    gap:10px;
    align-items:center;
    padding:10px 14px;
    border-radius:16px;
    background:#ffffff;
    border:1px solid var(--border);
    box-shadow:0 12px 30px rgba(31,41,55,.06);
    margin:8px 10px 0 0;
    font-weight:700;
    color:var(--text-dark);
  }

  .adv-badge i{ color:var(--tosca-1);font-size:1.2rem; }

  /* ================= FOOTER ================= */
  .footer{
    background:#1f2933;
    color:#ffffff;
    padding:28px 0;
  }

  .footer a{
    color:#ffffff;
    text-decoration:none;
    font-weight:600;
  }

  .footer a:hover{ text-decoration:underline; }

  /* ================= BUTTON ================= */
  .btn-brand{
    background:linear-gradient(135deg,var(--tosca-1),var(--tosca-2));
    border:0;
    color:#ffffff;
    box-shadow:0 12px 30px rgba(20,184,166,.45);
  }

  .btn-brand:hover{
    filter:brightness(.95);
    color:#ffffff;
  }

  .btn-outline-ghost{
    border:1px solid rgba(255,255,255,.55);
    color:#ffffff;
    background:transparent;
  }

  .btn-outline-ghost:hover{
    background:rgba(255,255,255,.15);
    color:#ffffff;
  }
</style>

</head>

<body>

<!-- ================= NAVBAR ================= -->
<nav class="navbar navbar-expand-lg navbar-blur">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
      <div class="brand-badge">KP</div>
      <div>
        <div class="fw-bold font-head" style="line-height:1.1">Sistem Informasi Rekap KP</div>
        <small class="text-muted" style="font-size:.85rem">Kerja Praktik Mahasiswa</small>
      </div>
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain" aria-controls="navMain" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navMain">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center">
        <li class="nav-item"><a class="nav-link fw-semibold" href="#beranda">Beranda</a></li>
        <li class="nav-item"><a class="nav-link fw-semibold" href="#fitur">Fitur</a></li>
        <li class="nav-item"><a class="nav-link fw-semibold" href="#alur">Alur KP</a></li>
        <li class="nav-item"><a class="nav-link fw-semibold" href="#tim">Tim Pengembang</a></li>
        <li class="nav-item"><a class="nav-link fw-semibold" href="#tentang">Tentang Sistem</a></li>
        <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
          <a class="btn btn-outline-primary me-2" href="auth/login_form.php">
            <i class="bi bi-box-arrow-in-right me-1"></i>Login
          </a>
          <a class="btn btn-brand" href="auth/register_form.php">
            <i class="bi bi-person-plus me-1"></i>Daftar
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>
<!-- ================= END NAVBAR ================= -->


<!-- ================= HERO ================= -->
<section id="beranda" class="hero">
  <div class="container">
    <div class="row align-items-center g-4">
      <div class="col-lg-6">
        <div class="hero-card">
          <h1 class="font-head mb-3">
            Sistem Informasi Rekap Kerja Praktik Mahasiswa
          </h1>
          <p class="lead mb-4">
            Solusi digital terintegrasi untuk pengelolaan Kerja Praktik mahasiswa:
            pengajuan, persetujuan, absensi, pelaporan mingguan, penilaian dosen, hingga upload laporan akhir & presentasi.
          </p>

          <div class="d-flex flex-wrap gap-2">
            <a href="auth/login_form.php" class="btn btn-light btn-lg fw-semibold">
              <i class="bi bi-box-arrow-in-right me-1"></i>Login
            </a>
            <a href="auth/register_form.php" class="btn btn-outline-ghost btn-lg fw-semibold">
              <i class="bi bi-person-plus me-1"></i>Daftar Mahasiswa
            </a>
          </div>

          <div class="mt-4">
            <div class="d-flex flex-wrap gap-2">
              <span class="badge rounded-pill text-bg-light text-dark">
                <i class="bi bi-mortarboard me-1"></i>Mahasiswa
              </span>
              <span class="badge rounded-pill text-bg-light text-dark">
                <i class="bi bi-person-badge me-1"></i>Dosen Pembimbing
              </span>
              <span class="badge rounded-pill text-bg-light text-dark">
                <i class="bi bi-gear me-1"></i>Admin Prodi
              </span>
            </div>
          </div>

        </div>
      </div>

      <div class="col-lg-6">
        <div class="hero-illus">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="fw-bold font-head">Ilustrasi Sistem</div>
            <span class="badge rounded-pill text-bg-light text-dark">Academic • Modern • Paperless</span>
          </div>

          <div class="illus-grid">
            <div class="illus-tile">
              <i class="bi bi-file-earmark-check"></i>
              <div class="t">Pengajuan KP</div>
              <p class="s">Pengajuan online oleh mahasiswa</p>
            </div>
            <div class="illus-tile">
              <i class="bi bi-person-check"></i>
              <div class="t">Persetujuan</div>
              <p class="s">Instansi & dosen pembimbing</p>
            </div>
            <div class="illus-tile">
              <i class="bi bi-calendar2-check"></i>
              <div class="t">Absensi</div>
              <p class="s">Presensi selama pelaksanaan KP</p>
            </div>
            <div class="illus-tile">
              <i class="bi bi-star"></i>
              <div class="t">Penilaian</div>
              <p class="s">Evaluasi dosen pembimbing</p>
            </div>
          </div>

          <div class="mt-3 text-white-50 small">
            Sistem ini dirancang untuk proses KP yang lebih rapi, terukur, dan terdokumentasi.
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
<!-- ================= END HERO ================= -->


<!-- ================= FITUR UTAMA ================= -->
<section id="fitur" class="section">
  <div class="container">
    <div class="row align-items-end mb-4">
      <div class="col-lg-7">
        <h2 class="section-title font-head mb-2">Fitur Utama Sistem</h2>
        <p class="section-sub">
          Semua proses Kerja Praktik terkelola dalam satu platform, dari awal sampai akhir.
        </p>
      </div>
      <div class="col-lg-5 text-lg-end mt-3 mt-lg-0">
        <a href="auth/login_form.php" class="btn btn-brand">
          <i class="bi bi-speedometer2 me-1"></i>Mulai Sekarang
        </a>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-md-6 col-lg-4">
        <div class="feature-card">
          <div class="icon-pill"><i class="bi bi-file-earmark-arrow-up"></i></div>
          <h6>Pengajuan Kerja Praktik Online</h6>
          <p>Mahasiswa mengajukan KP secara digital, rapi, dan terdokumentasi.</p>
        </div>
      </div>

      <div class="col-md-6 col-lg-4">
        <div class="feature-card">
          <div class="icon-pill"><i class="bi bi-journal-text"></i></div>
          <h6>Laporan Mingguan Mahasiswa</h6>
          <p>Pelaporan progres KP setiap minggu untuk monitoring dan evaluasi.</p>
        </div>
      </div>

      <div class="col-md-6 col-lg-4">
        <div class="feature-card">
          <div class="icon-pill"><i class="bi bi-calendar2-check"></i></div>
          <h6>Absensi Kerja Praktik</h6>
          <p>Presensi harian KP dengan pencatatan terstruktur dan mudah diverifikasi.</p>
        </div>
      </div>

      <div class="col-md-6 col-lg-4">
        <div class="feature-card">
          <div class="icon-pill"><i class="bi bi-star-fill"></i></div>
          <h6>Penilaian Dosen Pembimbing</h6>
          <p>Dosen menilai laporan, presensi, dan performa mahasiswa selama KP.</p>
        </div>
      </div>

      <div class="col-md-6 col-lg-4">
        <div class="feature-card">
          <div class="icon-pill"><i class="bi bi-cloud-upload"></i></div>
          <h6>Upload Laporan & Presentasi Akhir</h6>
          <p>Mahasiswa mengunggah laporan akhir dan file presentasi untuk penilaian akhir.</p>
        </div>
      </div>

      <div class="col-md-6 col-lg-4">
        <div class="feature-card">
          <div class="icon-pill"><i class="bi bi-graph-up-arrow"></i></div>
          <h6>Monitoring dan Rekap KP</h6>
          <p>Admin memantau status KP, bimbingan, dan kelengkapan dokumen secara real-time.</p>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- ================= END FITUR UTAMA ================= -->


<!-- ================= ALUR KP (TIMELINE) ================= -->
<section id="alur" class="section pt-0">
  <div class="container">
    <div class="row g-4 align-items-start">
      <div class="col-lg-5">
        <div class="soft-card p-4">
          <h2 class="section-title font-head mb-2">Alur Kerja Praktik</h2>
          <p class="section-sub mb-3">
            Alur ringkas proses Kerja Praktik mahasiswa yang dikelola sistem.
          </p>

          <div class="d-flex flex-wrap gap-2">
            <span class="badge rounded-pill text-bg-primary">Ringkas</span>
            <span class="badge rounded-pill text-bg-secondary">Terstruktur</span>
            <span class="badge rounded-pill text-bg-success">Paperless</span>
          </div>

          <hr class="my-4">

          <p class="mb-0 text-muted">
            Dengan alur yang jelas, proses KP menjadi lebih transparan dan mudah dipantau
            oleh mahasiswa, dosen pembimbing, dan admin prodi.
          </p>
        </div>
      </div>

      <div class="col-lg-7">
        <div class="steps">
          <div class="step">
            <p class="k">1. Pengajuan KP oleh Mahasiswa</p>
            <p class="d">Mahasiswa menginput data KP dan memilih instansi tujuan.</p>
          </div>
          <div class="step">
            <p class="k">2. Persetujuan Instansi dan Dosen</p>
            <p class="d">Admin memverifikasi dan menetapkan dosen pembimbing.</p>
          </div>
          <div class="step">
            <p class="k">3. Pelaksanaan Kerja Praktik</p>
            <p class="d">Mahasiswa menjalankan KP pada instansi sesuai periode.</p>
          </div>
          <div class="step">
            <p class="k">4. Laporan Mingguan dan Absensi</p>
            <p class="d">Mahasiswa mengisi presensi dan mengirim laporan tiap minggu.</p>
          </div>
          <div class="step">
            <p class="k">5. Penilaian Dosen Pembimbing</p>
            <p class="d">Dosen mengevaluasi progres, presensi, dan kinerja mahasiswa.</p>
          </div>
          <div class="step">
            <p class="k">6. Upload Laporan Akhir dan Presentasi</p>
            <p class="d">Mahasiswa unggah laporan akhir & file presentasi untuk penilaian akhir.</p>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
<!-- ================= END ALUR KP ================= -->


<!-- ================= TIM PENGEMBANG ================= -->
<section id="tim" class="section team">
  <div class="container">
    <div class="row align-items-end mb-4">
      <div class="col-lg-7">
        <h2 class="section-title font-head mb-2 text-white">Tim Pengembang</h2>
        <p class="section-sub">
          Tim Pengembang Sistem Informasi Rekap Kerja Praktik Mahasiswa
        </p>
      </div>
      <div class="col-lg-5 text-lg-end mt-3 mt-lg-0">
        <span class="badge rounded-pill text-bg-light text-dark">
          Proyek Akademik • Teknik Informatika
        </span>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-lg-6">
        <div class="team-card">
          <div class="member">
            <div class="num">01</div>
            <div>
              <p class="name mb-0">La Ode Muhamad Dirga</p>
              <p class="nim mb-0">E1E124007</p>
            </div>
          </div>

          <div class="member">
            <div class="num">02</div>
            <div>
              <p class="name mb-0">Muh. Nur Alam Syahrir</p>
              <p class="nim mb-0">E1E124043</p>
            </div>
          </div>

          <div class="member">
            <div class="num">03</div>
            <div>
              <p class="name mb-0">Rezki Alya</p>
              <p class="nim mb-0">E1E124015</p>
            </div>
          </div>

          <div class="member">
            <div class="num">04</div>
            <div>
              <p class="name mb-0">Annisa Nurul Faizah</p>
              <p class="nim mb-0">E1E124057</p>
            </div>
          </div>

          <div class="member">
            <div class="num">05</div>
            <div>
              <p class="name mb-0">Muh Fildan Patama</p>
              <p class="nim mb-0">E1E124069</p>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="team-card h-100">
          <h5 class="font-head mb-3">Identitas Proyek</h5>
          <p class="mb-3" style="color:rgba(255,255,255,.85)">
            Landing page ini dibuat untuk memperkenalkan sistem dan memudahkan pengguna
            memahami alur serta fitur utama pengelolaan Kerja Praktik.
          </p>

          <div class="row g-2">
            <div class="col-12">
              <div class="p-3 rounded-4" style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12)">
                <div class="d-flex align-items-center gap-2">
                  <i class="bi bi-shield-check"></i>
                  <div>
                    <div class="fw-bold">Aman & Terstruktur</div>
                    <div class="small" style="color:rgba(255,255,255,.78)">Bersiap diintegrasikan dengan PHP & MySQL multi-role.</div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-12">
              <div class="p-3 rounded-4" style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12)">
                <div class="d-flex align-items-center gap-2">
                  <i class="bi bi-easel2"></i>
                  <div>
                    <div class="fw-bold">Profesional & Akademik</div>
                    <div class="small" style="color:rgba(255,255,255,.78)">Clean UI, responsif, dan fokus pada kebutuhan kampus.</div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-12">
              <div class="p-3 rounded-4" style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12)">
                <div class="d-flex align-items-center gap-2">
                  <i class="bi bi-lightning-charge"></i>
                  <div>
                    <div class="fw-bold">Siap Dikembangkan</div>
                    <div class="small" style="color:rgba(255,255,255,.78)">Mudah ditambah modul panduan, pengumuman, dan dashboard.</div>
                  </div>
                </div>
              </div>
            </div>

          </div>

        </div>
      </div>

    </div>
  </div>
</section>
<!-- ================= END TIM PENGEMBANG ================= -->


<!-- ================= KEUNGGULAN ================= -->
<section class="section" id="keunggulan">
  <div class="container">
    <div class="row align-items-end mb-3">
      <div class="col-lg-8">
        <h2 class="section-title font-head mb-2">Keunggulan Sistem</h2>
        <p class="section-sub">Dirancang untuk kampus: terukur, transparan, dan paperless.</p>
      </div>
    </div>

    <div class="d-flex flex-wrap">
      <div class="adv-badge"><i class="bi bi-diagram-3"></i>Terintegrasi</div>
      <div class="adv-badge"><i class="bi bi-eye"></i>Transparan</div>
      <div class="adv-badge"><i class="bi bi-file-earmark"></i>Paperless</div>
      <div class="adv-badge"><i class="bi bi-activity"></i>Monitoring Real-time</div>
      <div class="adv-badge"><i class="bi bi-lock"></i>Aman dan Terstruktur</div>
    </div>
  </div>
</section>
<!-- ================= END KEUNGGULAN ================= -->


<!-- ================= TENTANG SISTEM ================= -->
<section id="tentang" class="section pt-0">
  <div class="container">
    <div class="soft-card p-4 p-lg-5">
      <div class="row g-4 align-items-center">
        <div class="col-lg-8">
          <h2 class="section-title font-head mb-2">Tentang Sistem</h2>
          <p class="section-sub mb-3">
            Sistem ini dikembangkan sebagai proyek akademik mahasiswa Program Studi Teknik Informatika.
            Tujuannya membantu kampus dalam pengelolaan Kerja Praktik mahasiswa secara digital, terintegrasi, dan terdokumentasi.
          </p>

          <div class="row g-2">
            <div class="col-md-6">
              <div class="p-3 rounded-4 border" style="border-color:var(--border); background:#fff;">
                <div class="d-flex align-items-center gap-2">
                  <i class="bi bi-database text-primary"></i>
                  <div>
                    <div class="fw-bold">Backend PHP & MySQL</div>
                    <div class="text-muted small">Siap integrasi sistem multi-role.</div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="p-3 rounded-4 border" style="border-color:var(--border); background:#fff;">
                <div class="d-flex align-items-center gap-2">
                  <i class="bi bi-layout-text-window-reverse text-primary"></i>
                  <div>
                    <div class="fw-bold">UI Responsif</div>
                    <div class="text-muted small">Optimal untuk desktop dan mobile.</div>
                  </div>
                </div>
              </div>
            </div>

          </div>

        </div>

        <div class="col-lg-4">
          <div class="p-4 rounded-4" style="background:linear-gradient(135deg, rgba(91,46,255,.10), rgba(11,94,215,.08)); border:1px solid rgba(91,46,255,.15);">
            <div class="font-head fw-bold mb-2">Mulai Gunakan Sistem</div>
            <p class="text-muted mb-3">Login untuk akses dashboard sesuai role Anda.</p>
            <div class="d-grid gap-2">
              <a href="auth/login_form.php" class="btn btn-brand">
                <i class="bi bi-box-arrow-in-right me-1"></i>Login
              </a>
              <a href="auth/register_form.php" class="btn btn-outline-primary">
                <i class="bi bi-person-plus me-1"></i>Daftar Mahasiswa
              </a>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</section>
<!-- ================= END TENTANG SISTEM ================= -->


<!-- ================= FOOTER ================= -->
<footer class="footer">
  <div class="container">
    <div class="row g-3 align-items-center">
      <div class="col-md-6">
        <div class="fw-bold font-head">Sistem Informasi Rekap Kerja Praktik</div>
        <div class="small text-white-50">© <?= date('Y'); ?> — Program Studi Teknik Informatika</div>
      </div>
      <div class="col-md-6 text-md-end">
        <a class="me-3" href="auth/login_form.php">Login</a>
        <a class="me-3" href="auth/register_form.php">Daftar</a>
        <a href="#alur">Panduan</a>
      </div>
    </div>
  </div>
</footer>
<!-- ================= END FOOTER ================= -->


<!-- JS -->
<script src="assets/js/bootstrap.bundle.min.js"></script>

</body>
</html>
