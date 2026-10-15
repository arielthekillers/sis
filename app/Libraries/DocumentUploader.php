<?php

namespace App\Libraries;

use CodeIgniter\HTTP\Files\UploadedFile;

class DocumentUploader
{
    protected string $uploadPath;
    protected array $allowedMimeTypes;

    public function __construct(string $uploadPath = 'uploads/documents/')
    {
        $this->uploadPath = FCPATH . ltrim($uploadPath, '/');
        
        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0755, true);
        }

        // Default Allowed MIME Types (PDF, Word, Excel)
        $this->allowedMimeTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ];
    }

    /**
     * Set MIME Types yang diizinkan untuk diupload
     */
    public function setAllowedMimeTypes(array $mimes)
    {
        $this->allowedMimeTypes = $mimes;
        return $this;
    }

    /**
     * Proses Upload Dokumen
     *
     * @param UploadedFile $file File yang diupload
     * @return array Result array berisi status dan daftar nama file
     */
    public function process(UploadedFile $file)
    {
        if (!$file->isValid() || $file->hasMoved()) {
            return [
                'status'  => 'error',
                'message' => $file->getErrorString()
            ];
        }

        // Cek MIME type demi keamanan
        if (!in_array($file->getMimeType(), $this->allowedMimeTypes)) {
            return [
                'status'  => 'error',
                'message' => 'Format file tidak diizinkan. Hanya PDF, Word, dan Excel yang diperbolehkan.'
            ];
        }

        try {
            // Gunakan getRandomName untuk menghindari file overwriting & eksploitasi
            $newName = $file->getRandomName();
            $file->move($this->uploadPath, $newName);

            return [
                'status'   => 'success',
                'filename' => $newName,
                'original' => $file->getClientName(),
                'size'     => $file->getSizeByUnit('kb')
            ];

        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => 'Gagal mengupload dokumen: ' . $e->getMessage()
            ];
        }
    }
}
