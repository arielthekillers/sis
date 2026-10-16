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
      <a href="<?= base_url('spp') ?>" class="k-btn sm" style="background:#10b981;color:#fff;" title="SPP Siswa">SPP 💳</a>
      <a href="<?= base_url('sis') ?>" class="k-btn sm c-sky" title="Masuk ke Sistem Informasi Sekolah">Portal SIS 🔐</a>
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
      <a href="<?= base_url('spp') ?>" style="color:#fff;text-decoration:none;font-weight:700;font-size:0.9rem;background:#10b981;padding:6px 16px;border-radius:99px;display:inline-block;">
        💳 SPP Siswa
      </a>
      <a href="<?= base_url('sis') ?>" style="color:var(--yellow);text-decoration:none;font-weight:700;font-size:0.9rem;background:rgba(255,255,255,0.1);padding:6px 16px;border-radius:99px;display:inline-block;">
        🔐 Masuk ke Portal Guru & Staf (SIS)
      </a>
    </div>
    <small>© <?= date('Y') ?> RSK Preschool and Kindergarten · Seluruh Hak Cipta Dilindungi</small>
  </div>
</footer>

<script src="<?= base_url('web/js/app.js') ?>"></script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
