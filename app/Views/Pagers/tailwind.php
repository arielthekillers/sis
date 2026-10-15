<?php

use CodeIgniter\Pager\PagerRenderer;

/**
 * @var PagerRenderer $pager
 */
$pager->setSurroundCount(2);
?>

<nav aria-label="Page navigation" class="flex items-center justify-between mt-4">
    <div class="hidden sm:block">
        <p class="text-sm text-gray-700 dark:text-slate-300">
            Menampilkan data navigasi
        </p>
    </div>
    <div class="flex-1 flex justify-between sm:justify-end">
        <ul class="inline-flex -space-x-px rounded-md shadow-sm">
            <?php if ($pager->hasPrevious()) : ?>
                <li>
                    <a href="<?= $pager->getPrevious() ?>" class="relative inline-flex items-center justify-center min-w-[40px] px-2 py-2 h-full rounded-l-md border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm font-medium text-gray-500 dark:text-slate-400 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                        <span class="sr-only">Sebelumnya</span>
                        <i class="ph ph-caret-left text-base"></i>
                    </a>
                </li>
            <?php else: ?>
                <li>
                    <span class="relative inline-flex items-center justify-center min-w-[40px] px-2 py-2 h-full rounded-l-md border border-gray-300 dark:border-slate-700 bg-gray-100 dark:bg-slate-800/50 text-sm font-medium text-gray-400 dark:text-slate-600 cursor-not-allowed">
                        <i class="ph ph-caret-left text-base"></i>
                    </span>
                </li>
            <?php endif ?>

            <?php foreach ($pager->links() as $link) : ?>
                <li>
                    <a href="<?= $link['uri'] ?>" class="relative inline-flex items-center justify-center min-w-[40px] px-4 py-2 border border-gray-300 dark:border-slate-700 text-sm font-medium <?= $link['active'] ? 'z-10 bg-blue-50 dark:bg-blue-900/30 border-blue-500 dark:border-blue-500 text-blue-600 dark:text-blue-400' : 'bg-white dark:bg-slate-800 text-gray-500 dark:text-slate-400 hover:bg-gray-50 dark:hover:bg-slate-700' ?> transition-colors">
                        <?= $link['title'] ?>
                    </a>
                </li>
            <?php endforeach ?>

            <?php if ($pager->hasNext()) : ?>
                <li>
                    <a href="<?= $pager->getNext() ?>" class="relative inline-flex items-center justify-center min-w-[40px] px-2 py-2 h-full rounded-r-md border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm font-medium text-gray-500 dark:text-slate-400 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                        <span class="sr-only">Selanjutnya</span>
                        <i class="ph ph-caret-right text-base"></i>
                    </a>
                </li>
            <?php else: ?>
                <li>
                    <span class="relative inline-flex items-center justify-center min-w-[40px] px-2 py-2 h-full rounded-r-md border border-gray-300 dark:border-slate-700 bg-gray-100 dark:bg-slate-800/50 text-sm font-medium text-gray-400 dark:text-slate-600 cursor-not-allowed">
                        <i class="ph ph-caret-right text-base"></i>
                    </span>
                </li>
            <?php endif ?>
        </ul>
    </div>
</nav>
