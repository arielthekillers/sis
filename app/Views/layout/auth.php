<!DOCTYPE html>
<html lang="id" x-data="{ darkMode: localStorage.getItem('theme') === 'dark' }" x-init="$watch('darkMode', val => localStorage.setItem('theme', val ? 'dark' : 'light'))" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'RSK APPS - Rumah Semut Kecil' ?></title>
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('uploads/logo.png') ?>">
    <link rel="icon" type="image/svg+xml" href="<?= base_url('uploads/logo.svg') ?>">
    <link rel="apple-touch-icon" href="<?= base_url('uploads/logo.png') ?>">
    <!-- Tailwind CSS -->
    <link href="<?= base_url('css/app.css') ?>" rel="stylesheet">
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Phosphor Icons -->
    <script src="https://cdn.jsdelivr.net/npm/@phosphor-icons/web"></script>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-800 dark:bg-slate-900 dark:text-slate-200 antialiased overflow-hidden flex items-center justify-center h-screen" style="font-family: 'Inter', sans-serif;">
    
    <!-- Absolute Dark Mode Toggle -->
    <button @click="darkMode = !darkMode" class="absolute top-6 right-6 w-10 h-10 flex items-center justify-center rounded-full bg-white dark:bg-slate-800 text-gray-500 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 shadow-sm border border-gray-200 dark:border-slate-700 transition-colors" title="Toggle Dark/Light Mode">
        <i class="text-xl" :class="darkMode ? 'ph ph-sun' : 'ph ph-moon'"></i>
    </button>

    <?= $this->renderSection('main') ?>

</body>
</html>
