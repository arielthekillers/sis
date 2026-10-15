/**
 * Sistem Informasi Sekolah - AJAX Uploader Helper
 * Menangani upload file dengan Progress Bar (menggunakan Axios atau Fetch)
 */

async function uploadFileAjax(file, uploadUrl, csrfToken, fieldName = 'file', onProgress, onSuccess, onError) {
    if (!file) return;

    // Persiapkan FormData
    const formData = new FormData();
    formData.append(fieldName, file);
    formData.append('csrf_test_name', csrfToken); // Sesuaikan dengan nama token CI4 Anda, default csrf_test_name

    try {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', uploadUrl, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        // Event listener untuk progress
        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
                const percentComplete = Math.round((e.loaded / e.total) * 100);
                if (typeof onProgress === 'function') {
                    onProgress(percentComplete);
                }
            }
        });

        // Event listener ketika selesai
        xhr.addEventListener('load', function() {
            if (xhr.status >= 200 && xhr.status < 300) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.status === 'success') {
                        if (typeof onSuccess === 'function') onSuccess(response);
                    } else {
                        if (typeof onError === 'function') onError(response.message || 'Upload gagal.');
                    }
                } catch (e) {
                    if (typeof onError === 'function') onError('Invalid server response.');
                }
            } else {
                if (typeof onError === 'function') onError('HTTP Error ' + xhr.status);
            }
        });

        // Event listener untuk error network
        xhr.addEventListener('error', function() {
            if (typeof onError === 'function') onError('Terjadi kesalahan jaringan.');
        });

        // Mulai upload
        xhr.send(formData);

    } catch (error) {
        if (typeof onError === 'function') onError(error.toString());
    }
}
