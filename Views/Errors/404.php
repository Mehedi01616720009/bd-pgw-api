<?php include_once __DIR__ . '/../Layouts/Admin/Header.php' ?>

<div class="min-h-screen flex justify-center items-center relative">
    <div class="text-center">
        <h2 class="font-semibold text-4xl lg:text-8xl">404</h2>
        <p class="text-sm lg:text-lg"><?= $message ? $message : 'Page not found!' ?></p>
    </div>
</div>

<?php include_once __DIR__ . '/../Layouts/Admin/Footer.php' ?>