<?= $this->extend('layout/auth') ?>

<?= $this->section('main') ?>

<div class="w-full max-w-md p-8 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-gray-100 dark:border-slate-800">
    <div class="flex flex-col items-center gap-3 justify-center mb-8">
        <img src="<?= base_url('uploads/logo.png') ?>" alt="RSK APPS Logo" class="h-20 w-auto object-contain drop-shadow-md">
        <div class="text-center">
            <span class="text-2xl font-bold text-gray-900 dark:text-white tracking-wide">RSK <span class="text-rose-500 dark:text-rose-400">APPS</span></span>
            <p class="text-xs text-gray-400 dark:text-slate-500 mt-0.5">Rumah Semut Kecil · Preschool & Kindergarten</p>
        </div>
    </div>

    <h2 class="text-2xl font-bold text-gray-900 dark:text-white text-center mb-2">Selamat Datang! 👋</h2>
    <p class="text-sm text-gray-500 dark:text-slate-400 text-center mb-8">Masuk ke Sistem Manajemen RSK APPS</p>

    <?php if (session('error') !== null) : ?>
        <div class="bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400 p-3 rounded-lg text-sm mb-6 flex items-start gap-2">
            <i class="ph ph-warning-circle text-lg mt-0.5"></i>
            <span><?= session('error') ?></span>
        </div>
    <?php elseif (session('errors') !== null) : ?>
        <div class="bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400 p-3 rounded-lg text-sm mb-6 flex items-start gap-2">
            <i class="ph ph-warning-circle text-lg mt-0.5"></i>
            <div class="flex flex-col">
                <?php if (is_array(session('errors'))) : ?>
                    <?php foreach (session('errors') as $error) : ?>
                        <span><?= $error ?></span>
                    <?php endforeach ?>
                <?php else : ?>
                    <span><?= session('errors') ?></span>
                <?php endif ?>
            </div>
        </div>
    <?php endif ?>

    <?php if (session('message') !== null) : ?>
    <div class="bg-green-50 text-green-600 dark:bg-green-500/10 dark:text-green-400 p-3 rounded-lg text-sm mb-6 flex items-start gap-2">
        <i class="ph ph-check-circle text-lg mt-0.5"></i>
        <span><?= session('message') ?></span>
    </div>
    <?php endif ?>

    <form action="<?= url_to('login') ?>" method="post">
        <?= csrf_field() ?>

        <div class="space-y-5">
            <!-- Email -->
            <div class="space-y-1">
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-slate-300">Alamat Email</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 dark:text-slate-500">
                        <i class="ph ph-envelope-simple text-lg"></i>
                    </div>
                    <input type="email" name="email" id="email" class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 dark:border-slate-700 rounded-lg bg-gray-50 dark:bg-slate-950 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-shadow" placeholder="nama@perusahaan.com" value="<?= old('email') ?>" required autofocus>
                </div>
            </div>

            <!-- Password -->
            <div class="space-y-1">
                <div class="flex items-center justify-between">
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-slate-300">Kata Sandi</label>
                </div>
                <div class="relative" x-data="{ show: false }">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 dark:text-slate-500">
                        <i class="ph ph-lock text-lg"></i>
                    </div>
                    <input :type="show ? 'text' : 'password'" name="password" id="password" class="block w-full pl-10 pr-10 py-2.5 border border-gray-300 dark:border-slate-700 rounded-lg bg-gray-50 dark:bg-slate-950 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-shadow" placeholder="••••••••" required>
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-slate-300 focus:outline-none">
                        <i class="text-lg" :class="show ? 'ph ph-eye-slash' : 'ph ph-eye'"></i>
                    </button>
                </div>
            </div>

            <!-- Remember Me -->
            <?php if (setting('Auth.sessionConfig')['allowRemembering']): ?>
            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 cursor-pointer group">
                    <div class="relative flex items-center justify-center">
                        <input type="checkbox" name="remember" class="peer appearance-none w-4 h-4 border border-gray-300 dark:border-slate-600 rounded bg-white dark:bg-slate-900 checked:bg-blue-600 checked:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 dark:focus:ring-offset-slate-900 transition-colors" <?php if (old('remember')): ?> checked <?php endif ?>>
                        <i class="ph ph-check text-white absolute text-xs opacity-0 peer-checked:opacity-100 pointer-events-none"></i>
                    </div>
                    <span class="text-sm text-gray-600 dark:text-slate-400 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">Ingat Saya</span>
                </label>
            </div>
            <?php endif; ?>

            <!-- Submit -->
            <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                Masuk
            </button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
