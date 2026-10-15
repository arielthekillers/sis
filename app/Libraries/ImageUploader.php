<?php

namespace App\Libraries;

use CodeIgniter\HTTP\Files\UploadedFile;

class ImageUploader
{
    protected string $uploadPath;

    public function __construct(string $uploadPath = 'uploads/images/')
    {
        // Set root path for uploads (usually in public/uploads/images)
        $this->uploadPath = FCPATH . ltrim($uploadPath, '/');
        
        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0755, true);
        }
    }

    /**
     * Proses Upload & Manipulasi Gambar
     *
     * @param UploadedFile $file File yang diupload
     * @param bool $squareCrop Apakah gambar harus dipotong menjadi persegi (square)?
     * @param array $sizes Daftar ukuran output (misal: ['thumb' => 150, 'md' => 500])
     * @return array Result array berisi status dan daftar nama file
     */
    public function process(UploadedFile $file, bool $squareCrop = false, array $sizes = ['thumb' => 150, 'md' => 500])
    {
        if (!$file->isValid() || $file->hasMoved()) {
            return [
                'status'  => 'error',
                'message' => $file->getErrorString()
            ];
        }

        // Dapatkan nama acak yang unik, ganti ekstensinya jadi .webp
        $tempName = $file->getRandomName();
        $baseName = pathinfo($tempName, PATHINFO_FILENAME);
        $finalName = $baseName . '.webp';

        // Pindahkan file asli sementara ke direktori upload
        $file->move($this->uploadPath, $tempName);
        $originalFilePath = $this->uploadPath . $tempName;
        
        $finalPaths = [];

        try {
            // 1. Simpan original ke WebP (Jika square, crop original juga)
            $originalWebpPath = $this->uploadPath . $finalName;
            $image = \Config\Services::image()->withFile($originalFilePath);
            
            if ($squareCrop) {
                // Ambil dimensi terkecil dari gambar asli
                $info = getimagesize($originalFilePath);
                $minDim = min($info[0], $info[1]);
                $image->fit($minDim, $minDim, 'center');
            }
            
            // Konversi ke webp dengan kualitas 80%
            $image->convert(IMAGETYPE_WEBP)->save($originalWebpPath, 80);
            $finalPaths['original'] = $finalName;

            // 2. Loop dan buat versi kompresi untuk setiap ukuran yang direquest
            foreach ($sizes as $prefix => $size) {
                $targetFile = $this->uploadPath . $prefix . '_' . $finalName;
                
                // Gunakan base original webp yang baru dibuat untuk mempercepat proses (atau file aslinya)
                $img = \Config\Services::image()->withFile($originalWebpPath);

                if ($squareCrop) {
                    $img->fit($size, $size, 'center');
                } else {
                    // Resize sambil mempertahankan aspect ratio
                    $img->resize($size, $size, true); 
                }

                $img->save($targetFile, 80);
                $finalPaths[$prefix] = $prefix . '_' . $finalName;
            }

            // 3. Hapus file asli (JPG/PNG/dll) karena sudah di-convert sepenuhnya ke WebP
            if (file_exists($originalFilePath)) {
                unlink($originalFilePath);
            }

            return [
                'status' => 'success',
                'files'  => $finalPaths
            ];

        } catch (\Exception $e) {
            // Rollback jika terjadi error
            if (file_exists($originalFilePath)) {
                unlink($originalFilePath);
            }
            
            return [
                'status'  => 'error',
                'message' => 'Gagal memproses gambar: ' . $e->getMessage()
            ];
        }
    }
}
