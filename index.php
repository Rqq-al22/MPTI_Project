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
  /* ================= COLOR SYSTEM (MATCH LOGIN) ================= */
  --tosca:#14b8a6;
  --tosca-dark:#0f766e;
  --tosca-soft:#e6fffb;

  --bg:#f3f4f6;
  --white:#ffffff;

  --dark:#1f2937;
  --dark-soft:#374151;

  --border:#e5e7eb;
  --text-muted:#6b7280;
}

body{
  font-family:'Open Sans',sans-serif;
  background:var(--bg);
  color:var(--dark);
}

.font-head{ font-family:'Montserrat',sans-serif; }

/* ================= NAVBAR ================= */
.navbar-blur{
  background:rgba(255,255,255,.96);
  backdrop-filter:blur(12px);
  border-bottom:1px solid var(--border);
}

.brand-badge{
  width:42px;
  height:42px;
  border-radius:14px;
  background:linear-gradient(135deg,var(--tosca),var(--tosca-dark));
  display:flex;
  align-items:center;
  justify-content:center;
  color:#fff;
  font-weight:800;
  box-shadow:0 10px 30px rgba(20,184,166,.4);
}

/* ================= HERO ================= */
.hero{
  padding:95px 0 80px;
  background:
    radial-gradient(800px 400px at 20% 20%, rgba(20,184,166,.35), transparent 60%),
    linear-gradient(135deg,var(--tosca),var(--tosca-dark));
  color:#fff;
}

.hero .lead{
  color:rgba(255,255,255,.92);
}

.hero-card{
  background:rgba(255,255,255,.12);
  border:1px solid rgba(255,255,255,.28);
  border-radius:22px;
  padding:32px;
  backdrop-filter:blur(8px);
}

.hero-illus{
  background:rgba(255,255,255,.12);
  border:1px solid rgba(255,255,255,.28);
  border-radius:22px;
  padding:24px;
}

/* ================= ILLUSTRATION ================= */
.illus-grid{
  display:grid;
  grid-template-columns:repeat(2,1fr);
  gap:16px;
}

.illus-tile{
  background:rgba(255,255,255,.14);
  border:1px solid rgba(255,255,255,.3);
  border-radius:18px;
  padding:18px;
  min-height:120px;
  color:#fff;
}

.illus-tile i{
  font-size:1.6rem;
}

.illus-tile .t{
  font-weight:800;
  margin-top:10px;
}

.illus-tile .s{
  font-size:.9rem;
  color:rgba(255,255,255,.9);
}

/* ================= SECTION ================= */
.section{ padding:80px 0; }

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
  background:#fff;
  border:1px solid var(--border);
  border-radius:20px;
  padding:22px;
  height:100%;
  box-shadow:0 18px 45px rgba(31,41,55,.06);
}

.icon-pill{
  width:48px;
  height:48px;
  border-radius:16px;
  display:flex;
  align-items:center;
  justify-content:center;
  background:var(--tosca-soft);
  border:1px solid rgba(20,184,166,.35);
  color:var(--tosca);
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
  border-left:3px solid rgba(20,184,166,.45);
  padding-left:22px;
}

.step{
  background:#fff;
  border:1px solid var(--border);
  border-radius:18px;
  padding:18px;
  margin-bottom:16px;
  box-shadow:0 16px 40px rgba(31,41,55,.06);
  position:relative;
}

.step:before{
  content:"";
  position:absolute;
  left:-32px;
  top:20px;
  width:14px;
  height:14px;
  border-radius:50%;
  background:linear-gradient(135deg,var(--tosca),var(--tosca-dark));
}

.step .k{ font-weight:800;margin:0 0 6px 0; }
.step .d{ margin:0;color:var(--text-muted);font-size:.95rem; }

/* ================= TEAM ================= */
.team{
  background:linear-gradient(135deg,var(--tosca),#134e4a);
  color:#fff;
}

.team .section-sub{
  color:rgba(255,255,255,.88);
}

.team-card{
  background:rgba(255,255,255,.12);
  border:1px solid rgba(255,255,255,.28);
  border-radius:22px;
  padding:22px;
}

.member{
  display:flex;
  gap:14px;
  align-items:center;
  padding:14px;
  border-radius:18px;
  background:rgba(255,255,255,.14);
  border:1px solid rgba(255,255,255,.28);
  margin-bottom:12px;
}

.num{
  width:44px;
  height:44px;
  border-radius:50%;
  display:flex;
  align-items:center;
  justify-content:center;
  font-weight:800;
  background:#fff;
  color:var(--tosca);
}

.member .name{ font-weight:800;margin:0; }
.member .nim{ margin:0;color:rgba(255,255,255,.88);font-size:.92rem; }

/* ================= BADGE ================= */
.adv-badge{
  display:inline-flex;
  gap:10px;
  align-items:center;
  padding:10px 16px;
  border-radius:18px;
  background:#fff;
  border:1px solid var(--border);
  box-shadow:0 14px 35px rgba(31,41,55,.06);
  margin:8px 10px 0 0;
  font-weight:700;
}

.adv-badge i{
  color:var(--tosca);
  font-size:1.2rem;
}

/* ================= BUTTON ================= */
.btn-brand{
  background:linear-gradient(135deg,var(--tosca),var(--tosca-dark));
  border:0;
  color:#fff;
  box-shadow:0 14px 35px rgba(20,184,166,.45);
}

.btn-brand:hover{
  filter:brightness(.95);
  color:#fff;
}

.btn-outline-ghost{
  border:1px solid rgba(255,255,255,.6);
  color:#fff;
  background:transparent;
}

.btn-outline-ghost:hover{
  background:rgba(255,255,255,.18);
  color:#fff;
}

/* ================= FOOTER ================= */
.footer{
  background:#134e4a;
  color:#fff;
  padding:32px 0;
}

.footer a{
  color:#fff;
  text-decoration:none;
  font-weight:600;
}

.footer a:hover{
  text-decoration:underline;
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
