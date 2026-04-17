<?php include_once __DIR__ . '/Header.php' ?>

<div class="max-w-[1520px] 2xl:w-full flex justify-between items-baseline gap-4 mx-5 2xl:mx-auto mb-6">
    <a href="<?= route('dashboard') ?>">
        <img src="<?= asset('assets/images/buttons/home.png') ?>" alt="">
    </a>

    <div>
        <form method="POST" action="<?= route('logout.post') ?>">
            <?= csrf_field() ?>
            <button type="submit" class="bg-transperant p-0 border-0 outline-none cursor-pointer" onclick="return confirm('Are you sure?')">
                <img src="<?= asset('assets/images/buttons/logout.png') ?>" alt="">
            </button>
        </form>
    </div>
</div>