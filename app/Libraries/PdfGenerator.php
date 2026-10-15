<?php

namespace App\Libraries;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfGenerator
{
    /**
     * Generate PDF dari string HTML
     *
     * @param string $html Konten HTML yang akan di-render
     * @param string $filename Nama file output (tanpa .pdf)
     * @param string $paper Ukuran kertas (A4, letter, dll)
     * @param string $orientation Orientasi kertas (portrait / landscape)
     * @param bool $stream Jika true, file akan langsung diunduh/ditampilkan di browser. Jika false, mengembalikan string/output mentah PDF
     */
    public function generate(string $html, string $filename = 'document', string $paper = 'A4', string $orientation = 'portrait', bool $stream = true)
    {
        $options = new Options();
        // Mengizinkan penggunaan file eksternal/remote (seperti gambar logo dari URL/Base URL)
        $options->set('isRemoteEnabled', true); 
        $options->set('defaultFont', 'Helvetica');
        
        $dompdf = new Dompdf($options);
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper($paper, $orientation);
        
        // Render HTML sebagai PDF
        $dompdf->render();
        
        if ($stream) {
            // Langsung lempar ke browser
            $dompdf->stream($filename . ".pdf", ["Attachment" => false]); // Attachment => false agar terbuka di tab baru jika browser mendukung
            exit();
        } else {
            // Kembalikan isi PDF (berguna jika ingin disave ke server / dikirim via email)
            return $dompdf->output();
        }
    }
}
