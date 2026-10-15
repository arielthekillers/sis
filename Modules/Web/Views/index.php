<?= $this->extend('layout/web') ?>

<?= $this->section('content') ?>

<!-- 1. Hero Carousel -->
<section id="beranda" class="k-carousel">
  <div class="k-slides">
    <div class="k-slide c-sky">
      <div>
        <span class="big">🐜</span>
        <h1>Di Sini, Anak "Bekerja" dengan Gembira</h1>
        <p class="k-lead">Setiap kegiatan kecil adalah kerja besar untuk tumbuh. Itulah cara Montessori di Rumah Semut Kecil.</p>
        <a href="#daftar" class="k-btn c-yellow lg">Yuk Daftar</a>
      </div>
    </div>
    <div class="k-slide c-mint">
      <div>
        <span class="big">🌱</span>
        <h1>Mandiri, Percaya Diri, Berakhlak Baik</h1>
        <p class="k-lead">Fondasi karakter dibangun sejak dini, dengan suasana Islami yang hangat.</p>
        <a href="#tentang" class="k-btn c-pink lg">Kenalan Dulu</a>
      </div>
    </div>
    <div class="k-slide c-pink">
      <div>
        <span class="big">🎒</span>
        <h1>Dari Toddler sampai Kindergarten B</h1>
        <p class="k-lead">Usia 2–6 tahun, dengan pilihan Reguler, Halfday, atau Fullday.</p>
        <a href="#kelas" class="k-btn c-yellow lg">Lihat Kelas</a>
      </div>
    </div>
  </div>
  <button class="k-arrow prev" aria-label="Sebelumnya">‹</button>
  <button class="k-arrow next" aria-label="Berikutnya">›</button>
  <div class="k-dots"></div>
</section>

<!-- 2. Tentang Kami -->
<section id="tentang" class="k-section k-center">
  <div class="k-container">
    <h2>Halo, kami Rumah Semut Kecil! 🐜</h2>
    <p class="k-lead">Semut kecil itu rajin, mandiri, dan kompak. Itu juga yang kami tanamkan: anak belajar dengan metode Montessori, dibimbing nilai-nilai Islam, dan orang tua ikut terlibat dari rumah.</p>
    <div class="k-grid" style="margin-top:34px">
      <article class="k-card c-pink">
        <div class="k-card-img">🧩</div>
        <div class="k-card-body">
          <h4>Montessori</h4>
          <p>Anak memilih dan mengerjakan sendiri aktivitasnya, dengan alat belajar yang nyata dan bisa dipegang.</p>
        </div>
      </article>
      <article class="k-card c-sky">
        <div class="k-card-img">🕌</div>
        <div class="k-card-body">
          <h4>Islami</h4>
          <p>Mengaji Yanbu'a, hafalan surat pendek dan doa harian, plus adab sehari-hari.</p>
        </div>
      </article>
      <article class="k-card c-mint">
        <div class="k-card-img">👨‍👩‍👧</div>
        <div class="k-card-body">
          <h4>Orang Tua Jadi Mitra</h4>
          <p>Perkembangan anak dilaporkan rutin, dan orang tua dilibatkan dalam proses belajar.</p>
        </div>
      </article>
      <article class="k-card c-orange">
        <div class="k-card-img">🩺</div>
        <div class="k-card-body">
          <h4>Tumbuh Kembang Dipantau</h4>
          <p>Ada cek kesehatan berkala bersama guru dan puskesmas.</p>
        </div>
      </article>
    </div>
  </div>
</section>

<!-- 3. Pilihan Kelas -->
<section id="kelas" class="k-section" style="background:#fff3d6">
  <div class="k-container k-center">
    <h2>Pilih Kelas Si Kecil</h2>
    <p class="k-lead">Usia dihitung minimal per bulan Juli.</p>
    <div class="k-grid" style="margin-top:30px">
      <div class="k-card c-yellow">
        <div class="k-card-body">
          <span class="k-stat c-pink">2+</span>
          <h4>Toddler</h4>
          <p>Minimal 2 tahun. Pengenalan sensorik & motorik dini dalam suasana ceria.</p>
        </div>
      </div>
      <div class="k-card c-sky">
        <div class="k-card-body">
          <span class="k-stat c-sky">3+</span>
          <h4>Playgroup</h4>
          <p>Minimal 3 tahun. Melatih kemandirian, sosialisasi, dan eksplorasi lingkungan.</p>
        </div>
      </div>
      <div class="k-card c-mint">
        <div class="k-card-body">
          <span class="k-stat c-mint">4+</span>
          <h4>Kindergarten A</h4>
          <p>Minimal 4 tahun. Pembiasaan adab, pra-literasi, pra-numerasi, & hafalan doa.</p>
        </div>
      </div>
      <div class="k-card c-orange">
        <div class="k-card-body">
          <span class="k-stat c-orange">5+</span>
          <h4>Kindergarten B</h4>
          <p>Minimal 5 tahun. Siap lanjut ke jenjang Sekolah Dasar (SD) di usia 6 tahun.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- 4. Pilihan Jam Sekolah -->
<section id="jam" class="k-section">
  <div class="k-container" style="max-width:820px">
    <h2 class="k-center">Mau yang Mana: Reguler, Halfday, Fullday?</h2>
    <p class="k-center">Sekolah Senin–Jumat, bel masuk 07.30 WIB.</p>
    <div class="k-tabs c-orange" style="justify-content:center; margin-top:20px;">
      <button class="on" data-tab="#j1">Reguler</button>
      <button data-tab="#j2">Halfday</button>
      <button data-tab="#j3">Fullday</button>
    </div>
    <div class="k-pane" id="j1">
      <h4>🕢 07.30 – 10.00 WIB</h4>
      <p>Belajar dengan metode Montessori dan Kurikulum Merdeka berbasis <em>deep learning</em>. Sangat pas untuk ananda yang baru pertama kali mulai bersekolah.</p>
    </div>
    <div class="k-pane" id="j2" hidden>
      <h4>🍱 07.30 – 13.00 WIB</h4>
      <p>Ditambah Bahasa Inggris, mengaji Yanbu'a, rebana (tiap Selasa), makan siang sehat bersama, dan sholat dzuhur berjamaah.</p>
    </div>
    <div class="k-pane" id="j3" hidden>
      <h4>🌙 07.30 – 17.00 WIB</h4>
      <p>Ditambah tidur siang nyaman, mandi sore dan bersih-bersih, lalu muroja'ah atau tontonan edukatif sambil menunggu jemputan orang tua.</p>
    </div>

    <h4 class="k-center" style="margin-top:36px">Jadwal Sehari-hari di RSK</h4>
    <ul class="k-list">
      <li>🕢 <b>07.30</b> Bel masuk berbunyi, disambut guru lalu main bersama di playground</li>
      <li>📿 <b>08.00</b> Membaca Asmaul Husna dan doa pagi bersama</li>
      <li>🧸 <b>08.10 – 09.30</b> Kegiatan belajar & eksplorasi Montessori di kelas</li>
      <li>🍎 <b>09.30 – 09.45</b> Istirahat, mencuci tangan, dan makan snack sehat</li>
      <li>🎒 <b>09.45 – 10.00</b> Refleksi harian & bersiap pulang (Kelas Reguler)</li>
    </ul>
  </div>
</section>

<!-- 5. Tim Guru & Pendidik (Dinamis dari Database SIS) -->
<section id="guru" class="k-section k-center" style="background:#f0f9ff">
  <div class="k-container">
    <h2>Guru & Pendidik Penuh Kasih Sayang 👩‍🏫</h2>
    <p class="k-lead">Didampingi oleh para ustadzah & miss yang sabar, tersertifikasi, dan penuh dedikasi untuk tumbuh kembang ananda tercinta.</p>
    
    <div class="k-grid" style="margin-top:34px">
      <?php 
        $colors = ['pink', 'sky', 'mint', 'orange', 'purple', 'yellow'];
        $emojis = ['🌸', '✨', '⭐', '🎈', '🎨', '📚', '🌻', '🧸'];
      ?>
      <?php if (!empty($teachers)): ?>
        <?php foreach ($teachers as $idx => $t): ?>
          <?php 
            $c = $colors[$idx % count($colors)];
            $em = $emojis[$idx % count($emojis)];
          ?>
          <article class="k-card c-<?= $c ?>">
            <div class="k-card-img" style="font-size:3rem; padding: 20px;">
              <?php if (!empty($t['foto']) && file_exists(FCPATH . 'uploads/profiles/' . $t['foto'])): ?>
                <img src="<?= base_url('uploads/profiles/' . $t['foto']) ?>" alt="<?= esc($t['nama_lengkap']) ?>" style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid #fff;">
              <?php else: ?>
                <?= $em ?>
              <?php endif; ?>
            </div>
            <div class="k-card-body">
              <span class="k-badge c-<?= $c ?>"><?= esc($t['sapaan'] ?? 'Miss') ?></span>
              <h4 style="margin-top:8px;"><?= esc($t['nama_lengkap']) ?></h4>
              <p style="font-size:0.9rem; font-weight:700; color:var(--ink);"><?= esc($t['jabatan'] ?? 'Pendidik') ?></p>
            </div>
          </article>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="k-center">Data guru sedang diperbarui.</p>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- 6. Kegiatan Seru -->
<section id="kegiatan" class="k-section k-center" style="background:#e9f6ff">
  <div class="k-container">
    <h2>Seru-seruan di RSK 🎉</h2>
    <p class="k-lead">Biar belajar nggak terasa membosankan, setiap hari ada petualangan baru!</p>
    <div style="display:flex;flex-wrap:wrap;gap:12px;justify-content:center;margin-top:28px">
      <span class="k-badge c-pink lg">🚌 Outing class</span>
      <span class="k-badge c-sky lg">🍳 Cooking class</span>
      <span class="k-badge c-mint lg">🌻 Berkebun & Menanam</span>
      <span class="k-badge c-orange lg">🎶 Music & movement</span>
      <span class="k-badge c-pink">🔤 English Day</span>
      <span class="k-badge c-sky">⚽ Fun Sports</span>
      <span class="k-badge c-mint">🥁 Rebana</span>
      <span class="k-badge c-orange">🎼 Angklung Tradisional</span>
      <span class="k-badge c-pink">🗣️ Bahasa Jawa tiap Jumat</span>
      <span class="k-badge c-yellow">👘 Baju Adat tiap Selasa Legi</span>
      <span class="k-badge c-sky">🎓 Guest Teacher & Profesi</span>
      <span class="k-badge c-mint">📖 Pendampingan Baca & Hitung</span>
    </div>
    <p style="margin-top:28px; max-width:680px; margin-inline:auto;">
      Perkembangan anak dapat dipantau orang tua secara transparan melalui portal evaluasi, dilengkapi laporan berkala dan rapor tiap akhir semester.
    </p>
  </div>
</section>

<!-- 7. Fasilitas Sekolah -->
<section id="fasilitas" class="k-section">
  <div class="k-container">
    <h2 class="k-center">Fasilitas yang Bikin Nyaman & Tenang</h2>
    <p class="k-lead k-center">Semua sudut sekolah dirancang aman, bersih, dan menstimulasi kreativitas anak.</p>
    <div class="k-grid" style="margin-top:28px">
      <div class="k-alert c-sky">🏫 6 ruang kelas ber-AC dan WiFi</div>
      <div class="k-alert c-pink">🛝 Indoor & outdoor playground</div>
      <div class="k-alert c-mint">📚 Pojok baca & mini library</div>
      <div class="k-alert c-orange">🍽️ Ruang makan bersih & higienis</div>
      <div class="k-alert c-sky">🚰 Wastafel ramah anak & dispenser higienis</div>
      <div class="k-alert c-pink">🩹 Perlengkapan P3K & ruang istirahat</div>
      <div class="k-alert c-mint">📹 Kamera pengawas (CCTV) 24 jam</div>
      <div class="k-alert c-yellow">💬 Konsultasi tumbuh kembang & parenting</div>
    </div>
  </div>
</section>

<!-- 8. Alur & Cara Pendaftaran (PPDB) -->
<section id="daftar" class="k-section" style="background:#fff3d6">
  <div class="k-container" style="max-width:760px">
    <h2 class="k-center">Cara Daftar, Gampang Kok 📝</h2>
    <p class="k-lead k-center">Penerimaan Peserta Didik Baru (PPDB) dibuka setiap semester!</p>
    
    <details class="k-acc c-pink" name="d" open style="margin-top:24px;">
      <summary>📄 Berkas yang Perlu Disiapkan</summary>
      <div>
        <ul style="margin:0; padding-left:20px;">
          <li>Formulir pendaftaran yang telah diisi lengkap</li>
          <li>Pas foto ananda ukuran 3x4 background merah/biru (3 lembar)</li>
          <li>Fotokopi akta kelahiran ananda (3 lembar)</li>
          <li>Fotokopi Kartu Keluarga (KK) dan KTP kedua orang tua (3 lembar)</li>
          <li>Untuk siswa pindahan: lampirkan surat keterangan pindah dari sekolah asal</li>
        </ul>
      </div>
    </details>
    
    <details class="k-acc c-sky" name="d">
      <summary>🔎 Alur Observasi & Wawancara Ringan</summary>
      <div>
        Ananda akan diajak bermain bersama guru dalam sesi observasi ramah anak untuk mengenal kebiasaan, kesiapan, dan kebutuhan khususnya. Orang tua juga dapat berdiskusi santai dengan kepala sekolah mengenai target perkembangan si kecil.
      </div>
    </details>
    
    <p class="k-center" style="margin-top:24px;">
      <a href="#kontak" class="k-btn c-pink lg">Tanya Info Pendaftaran</a>
    </p>
  </div>
</section>

<!-- 9. Tanya Jawab (FAQ) -->
<section id="faq" class="k-section">
  <div class="k-container" style="max-width:760px">
    <h2 class="k-center">Yang Sering Ditanyakan Orang Tua ❓</h2>
    
    <details class="k-acc c-purple" name="f" open style="margin-top:24px;">
      <summary>Anak perlu membawa perlengkapan apa saja ke sekolah?</summary>
      <div>Baju ganti (atas, bawah, dalaman), botol minum pribadi, kotak makan kosong/bekal, kaos kaki, dan perlengkapan sholat sendiri. Khusus program Fullday ditambahkan 2 pasang baju ganti cadangan, handuk kecil, dan sikat gigi.</div>
    </details>
    
    <details class="k-acc c-pink" name="f">
      <summary>Bagaimana ketentuan seragam sekolah?</summary>
      <div>Senin: Biru Navy, Selasa: Batik Bebas (khusus Selasa Legi memakai baju adat), Rabu: Seragam Cream, Kamis: Batik Bebas, Jumat: Pakaian Olahraga ceria. Topi kuning identitas RSK dikenakan setiap hari.</div>
    </details>
    
    <details class="k-acc c-mint" name="f">
      <summary>Bagaimana sistem keamanan saat penjemputan anak?</summary>
      <div>Penjemputan dilakukan di area sekolah dengan mengisi absensi di meja resepsionis. Demi keamanan, ananda hanya boleh dijemput oleh orang tua atau wali yang telah terdaftar dalam data penjemput resmi sekolah.</div>
    </details>
    
    <details class="k-acc c-orange" name="f">
      <summary>Kapan orang tua bisa berkonsultasi mengenai perkembangan ananda?</summary>
      <div>Konsultasi rutin dibuka setiap Senin–Kamis pukul 08.00–12.00 WIB dengan membuat janji temu terlebih dahulu melalui Staff Tata Usaha sekolah.</div>
    </details>
  </div>
</section>

<!-- 10. Form Kontak -->
<section id="kontak" class="k-section" style="background:#e9f6ff">
  <div class="k-container" style="max-width:620px">
    <h2 class="k-center">Ngobrol Bareng Kami 💌</h2>
    <p class="k-center">Dipimpin oleh Ibu Nu'tih Kamalia, M.Pd.I bersama tim guru & staf yang hangat dan ramah.</p>
    
    <form onsubmit="event.preventDefault();document.getElementById('terima').showModal()" style="margin-top:24px;">
      <input class="k-input" placeholder="Nama Lengkap Orang Tua / Wali" required>
      <input class="k-input" type="tel" placeholder="Nomor WhatsApp Aktif" required>
      <textarea class="k-input" rows="3" placeholder="Nama & usia ananda, serta kelas yang diminati (Toddler / Playgroup / KG-A / KG-B)"></textarea>
      <div class="k-center" style="margin-top:12px;">
        <button type="submit" class="k-btn c-pink lg">Kirim Pesan Sekarang</button>
      </div>
    </form>
  </div>
</section>

<!-- Modal Notifikasi Pengiriman -->
<dialog id="terima">
  <h3>Terima kasih Ayah & Bunda! 💖</h3>
  <p>Pesan Anda telah kami terima dengan baik. Tim resepsionis dan guru kami akan segera menghubungi nomor WhatsApp Anda.</p>
  <div style="text-align:right; margin-top:16px;">
    <button class="k-btn c-mint" data-close>Tutup</button>
  </div>
</dialog>

<?= $this->endSection() ?>
