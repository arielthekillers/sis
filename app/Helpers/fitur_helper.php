<?php

/**
 * fitur_helper.php – Kumpulan komponen UI (gaya lucu & imut) untuk website TK/Playgroup/Preschool.
 * Warna tersedia: pink, yellow, mint, sky, purple, orange
 */

if (!class_exists('Fitur')) {
    class Fitur
    {
        private static function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

        // Heading 1-6:  Fitur::heading('Halo', 2)
        public static function heading($t, $n = 2) { $n = max(1, min(6, (int)$n)); return "<h$n>" . self::e($t) . "</h$n>"; }

        // Button:  Fitur::btn('Kirim','mint','sm|lg','out', '#link')
        public static function btn($t, $c = 'pink', $size = '', $out = '', $href = '') {
            $cls = trim("k-btn c-$c $size $out"); $t = self::e($t);
            return $href ? "<a class=\"$cls\" href=\"" . self::e($href) . "\">$t</a>" : "<button class=\"$cls\">$t</button>";
        }

        // Button group:  Fitur::btnGroup([['Kiri','pink'],['Tengah','sky']])
        public static function btnGroup(array $items) {
            return '<div class="k-btns">' . implode('', array_map(fn($i) => self::btn($i[0], $i[1] ?? 'pink'), $items)) . '</div>';
        }

        public static function badge($t, $c = 'yellow') { return "<span class=\"k-badge c-$c\">" . self::e($t) . "</span>"; }

        // Avatar emoji / inisial:  Fitur::avatar('👧','pink',80)
        public static function avatar($t, $c = 'sky', $size = 64) { return "<span class=\"k-avatar c-$c\" style=\"--s:{$size}px\">" . self::e($t) . "</span>"; }

        public static function close($target = '') { return '<button class="k-close" data-close aria-label="Tutup">×</button>'; }

        // Alert dengan tombol tutup
        public static function alert($t, $c = 'sky') { return "<div class=\"k-alert c-$c\"><span>" . self::e($t) . "</span>" . self::close() . "</div>"; }

        // Breadcrumb:  Fitur::breadcrumb(['Beranda'=>'/', 'Program'=>'/program', 'TK A'=>''])
        public static function breadcrumb(array $links) {
            $o = '<ul class="k-crumb">';
            foreach ($links as $t => $u) $o .= '<li>' . ($u ? '<a href="' . self::e($u) . '">' . self::e($t) . '</a>' : self::e($t)) . '</li>';
            return $o . '</ul>';
        }

        // Card:  Fitur::card('Judul','Isi','🐣','pink', 'Tombol','#')
        public static function card($judul, $isi, $emoji = '🧸', $c = 'pink', $btn = '', $href = '#') {
            return "<article class=\"k-card c-$c\"><div class=\"k-card-img\">" . self::e($emoji) . "</div><div class=\"k-card-body\"><h4>" . self::e($judul) . "</h4><p>" . self::e($isi) . "</p>"
                . ($btn ? self::btn($btn, $c, 'sm', '', $href) : '') . "</div></article>";
        }

        // Accordion:  Fitur::accordion([['Tanya','Jawab'], ...], 'nama-grup')
        public static function accordion(array $items, $group = 'acc') {
            $cols = ['purple', 'pink', 'mint', 'sky', 'orange']; $o = '';
            foreach ($items as $i => $it) $o .= "<details class=\"k-acc c-{$cols[$i % 5]}\" name=\"" . self::e($group) . "\"" . ($i ? '' : ' open') . "><summary>" . self::e($it[0]) . "</summary><div>" . self::e($it[1]) . "</div></details>";
            return $o;
        }

        // Collapse: tombol + isi yang bisa dibuka/tutup
        public static function collapse($id, $btn, $isi) { return "<button class=\"k-btn c-mint\" data-toggle=\"#" . self::e($id) . "\">" . self::e($btn) . "</button><div class=\"k-collapse\" id=\"" . self::e($id) . "\"><p>" . self::e($isi) . "</p></div>"; }

        // List group:  Fitur::listGroup(['Satu','Dua'])  atau ['Teks'=>'link']
        public static function listGroup(array $items) {
            $o = '<ul class="k-list">';
            foreach ($items as $k => $v) $o .= '<li>' . (is_int($k) ? self::e($v) : '<a href="' . self::e($v) . '">' . self::e($k) . '</a>') . '</li>';
            return $o . '</ul>';
        }

        // Pagination
        public static function pagination($now, $total, $base = '?page=') {
            $o = '<ul class="k-pages">';
            for ($i = 1; $i <= $total; $i++) $o .= '<li' . ($i == $now ? ' class="on"' : '') . "><a href=\"" . self::e($base . $i) . "\">$i</a></li>";
            return $o . '</ul>';
        }

        // Nav tabs + panel:  Fitur::tabs('t1', ['Senin'=>'Isi senin','Selasa'=>'Isi selasa'])
        public static function tabs($id, array $tabs, $scroll = false) {
            $b = $p = ''; $i = 0;
            foreach ($tabs as $t => $isi) {
                $b .= "<button class=\"" . ($i ? '' : 'on') . "\" data-tab=\"#$id-$i\">" . self::e($t) . "</button>";
                $p .= "<div class=\"k-pane\" id=\"$id-$i\"" . ($i ? ' hidden' : '') . ">" . self::e($isi) . "</div>"; $i++;
            }
            return "<div class=\"k-tabs c-orange" . ($scroll ? ' scroll' : '') . "\">$b</div>$p";
        }

        // Menu dropdown (popup) :  Fitur::menu('m1','Pilih ▾',['Satu'=>'#a','Dua'=>'#b'])
        public static function menu($id, $label, array $links) {
            $o = '';
            foreach ($links as $t => $u) $o .= '<a href="' . self::e($u) . '">' . self::e($t) . '</a>';
            return "<div class=\"k-pop\" id=\"$id\"><button class=\"k-btn c-sky\" data-toggle=\"#$id\">" . self::e($label) . "</button><div class=\"k-dd-menu\">$o</div></div>";
        }

        // Navbar dengan dropdown & submenu:
        public static function navbar($brand, array $items) {
            $r = function ($arr) use (&$r) {
                $o = '';
                foreach ($arr as $t => $v) {
                    if (is_array($v)) $o .= '<div class="k-dd"><a href="#">' . self::e($t) . ' ▸</a><div class="k-dd-menu">' . $r($v) . '</div></div>';
                    else $o .= '<a href="' . self::e($v) . '">' . self::e($t) . '</a>';
                }
                return $o;
            };
            return '<header class="k-navbar"><div class="k-container k-nav-in"><a class="k-brand" href="' . base_url() . '">' . $brand . '</a>'
                . '<button class="k-burger" data-toggle="#menu" aria-label="Menu">☰</button><nav class="k-menu" id="menu">' . $r($items) . '</nav></div></header>';
        }

        // Dialog (modal): buka dengan tombol Fitur::dialogBtn('id','Buka')
        public static function dialog($id, $judul, $isi) {
            return "<dialog id=\"" . self::e($id) . "\"><h3>" . self::e($judul) . "</h3><p>" . self::e($isi) . "</p><button class=\"k-btn c-mint\" data-close>Tutup</button></dialog>";
        }
        public static function dialogBtn($id, $t, $c = 'pink') { return "<button class=\"k-btn c-$c\" data-open=\"#" . self::e($id) . "\">" . self::e($t) . "</button>"; }

        // Drawer (panel samping)
        public static function drawer($id, $judul, array $links) {
            return "<aside class=\"k-drawer\" id=\"" . self::e($id) . "\"><div style=\"display:flex;justify-content:space-between\"><h4>" . self::e($judul) . "</h4>" . self::close() . "</div>" . self::listGroup($links) . "</aside>"
                . "<button class=\"k-btn c-purple\" data-toggle=\"#" . self::e($id) . "\">Buka Drawer</button>";
        }

        // Carousel:  Fitur::carousel([['🧸','Judul','Teks','pink'], ...])
        public static function carousel(array $slides) {
            $o = '';
            foreach ($slides as $s) $o .= "<div class=\"k-slide c-" . ($s[3] ?? 'pink') . "\"><div><span class=\"big\">" . self::e($s[0]) . "</span><h2>" . self::e($s[1]) . "</h2><p>" . self::e($s[2]) . "</p></div></div>";
            return "<div class=\"k-carousel\" style=\"border-radius:var(--r)\"><div class=\"k-slides\">$o</div><button class=\"k-arrow prev\">‹</button><button class=\"k-arrow next\">›</button><div class=\"k-dots\"></div></div>";
        }
    }
}
