<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title ?? 'RSK Preschool & Kindergarten – Rumah Semut Kecil' ?></title>
    <meta name="description" content="<?= $description ?? 'KB Rumah Semut Kecil: sekolah Islamic Montessori untuk Toddler, Playgroup, dan Kindergarten.' ?>">
    <link rel="icon" type="image/png" href="<?= base_url('web/img/logo.png') ?>">
    <link rel="stylesheet" href="<?= base_url('web/css/style.css') ?>">
    <?= $this->renderSection('styles') ?>
</head>
<body>

<header class="k-navbar">
  <div class="k-container k-nav-in">
    <a class="k-brand" href="<?= base_url() ?>">
      <img src="<?= base_url('web/img/logo.png') ?>" alt="Logo RSK Preschool and Kindergarten">
    </a>
    <button class="k-burger" data-toggle="#menu" aria-label="Menu">☰</button>
    <nav class="k-menu" id="menu">
      <a href="<?= base_url('#beranda') ?>">Beranda</a>
      <a href="<?= base_url('#tentang') ?>">Tentang</a>
      <div class="k-dd"><a href="<?= base_url('#kelas') ?>">Kelas ▾</a>
        <div class="k-dd-menu">
          <a href="<?= base_url('#kelas') ?>">Toddler · Playgroup</a>
          <a href="<?= base_url('#kelas') ?>">Kindergarten A · B</a>
          <div class="k-dd"><a href="<?= base_url('#jam') ?>">Pilihan Jam ▸</a>
            <div class="k-dd-menu">
              <a href="<?= base_url('#jam') ?>">Reguler</a>
              <a href="<?= base_url('#jam') ?>">Halfday</a>
              <a href="<?= base_url('#jam') ?>">Fullday</a>
            </div>
          </div>
        </div>
      </div>
      <a href="<?= base_url('#guru') ?>">Guru Kami</a>
      <a href="<?= base_url('#kegiatan') ?>">Kegiatan</a>
      <a href="<?= base_url('#fasilitas') ?>">Fasilitas</a>
      <a href="<?= base_url('#daftar') ?>">Pendaftaran</a>
      <a href="<?= base_url('#faq') ?>">Tanya Jawab</a>
      <a href="<?= base_url('sis') ?>" target="_blank" rel="noopener noreferrer" class="k-btn sm c-sky" title="Masuk ke Sistem Informasi Sekolah">Portal SIS 🔐</a>
    </nav>
  </div>
</header>

<main>
    <?= $this->renderSection('content') ?>
</main>

<footer class="k-footer">
  <div class="k-container">
    <h4><?= esc(app_setting('school_name', 'KB Rumah Semut Kecil · Islamic Montessori')) ?></h4>
    <p>Preschool & Kindergarten Ramah Anak dan Menyenangkan · Info WhatsApp: <?= esc(app_setting('school_whatsapp', '0812-3456-7890')) ?></p>
    <div style="margin: 12px 0; display:flex; gap:8px; justify-content:center; flex-wrap:wrap;">
      <a href="<?= base_url('sis') ?>" target="_blank" rel="noopener noreferrer" style="color:var(--yellow);text-decoration:none;font-weight:700;font-size:0.9rem;background:rgba(255,255,255,0.1);padding:6px 16px;border-radius:99px;display:inline-block;">
        🔐 Masuk ke Portal Guru & Staf (SIS)
      </a>
    </div>
    <small>© <?= date('Y') ?> <?= esc(app_setting('school_name', 'RSK Preschool and Kindergarten')) ?> · Seluruh Hak Cipta Dilindungi</small>
  </div>
</footer>

<!-- Flying Back to Top Button -->
<button id="btnBackToTop" aria-label="Kembali ke atas" title="Kembali ke atas" 
        onclick="window.scrollTo({top:0,behavior:'smooth'})"
        style="position:fixed; bottom:26px; right:26px; z-index:90; width:48px; height:48px; border-radius:50%; background:var(--sky, #1c9bd1); color:#fff; border:3px solid #fff; box-shadow:0 6px 18px rgba(0,0,0,0.18); cursor:pointer; display:flex; align-items:center; justify-content:center; opacity:0; visibility:hidden; transform:translateY(14px); transition:all 0.25s cubic-bezier(0.16,1,0.3,1);">
  <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
    <path d="m18 15-6-6-6 6"/>
  </svg>
</button>

<style>
  #btnBackToTop:hover {
    transform: translateY(-3px) scale(1.05) !important;
    background: var(--pink, #f2503e) !important;
    box-shadow: 0 8px 22px rgba(242,80,62,0.35) !important;
  }
</style>

<script>
  window.addEventListener('scroll', () => {
    const btn = document.getElementById('btnBackToTop');
    if (!btn) return;
    if (window.scrollY > 280) {
      btn.style.opacity = '1';
      btn.style.visibility = 'visible';
      btn.style.transform = 'translateY(0)';
    } else {
      btn.style.opacity = '0';
      btn.style.visibility = 'hidden';
      btn.style.transform = 'translateY(14px)';
    }
  });
</script>

<script src="<?= base_url('web/js/app.js') ?>"></script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
