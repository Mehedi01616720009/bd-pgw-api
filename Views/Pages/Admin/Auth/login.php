<?php include_once __DIR__ . '/../../../Layouts/Admin/Header.php' ?>

<div class="min-h-screen flex justify-center items-center relative">
    <div class="max-w-[400px] w-full mx-5">
        <h3 class="text-3xl font-bold mb-2">Welcome back!</h3>
        <p class="text-sm font-semibold mb-10">Please enter your credentials to sign in!</p>

        <?php if ($success = flash('success')): ?>
            <div class="bg-lime-50 text-sm text-lime-600 px-4 py-2 rounded-lg mb-4">
                <?= e($success) ?>
            </div>
        <?php endif; ?>

        <?php if ($error = flash('error')): ?>
            <div class="bg-rose-50 text-sm text-rose-600 px-4 py-2 rounded-lg mb-4">
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= route('login.post') ?>">
            <?= csrf_field() ?>

            <div class="grid gap-7">
                <div class="grid gap-2">
                    <label for="email" class="text-sm text-[#737373]">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Enter email address"
                        value="<?= old('email') ?>"
                        class="h-12 outline-none mb-1 font-semibold text-[#171717] text-sm px-3 py-2 rounded-xl border-2 border-[#f5f5f5] focus:ring-[#171717] focus-within:ring-[#171717] focus-within:border-[#171717] focus:border-[#171717] <?= hasError('email') ? 'bg-rose-50' : 'bg-[#f5f5f5]' ?>">
                    <?php if (hasError('email')): ?>
                        <span class="bg-rose-50 text-sm text-rose-600 px-4 py-2 rounded-lg"><?= errors('email') ?></span>
                    <?php endif; ?>
                </div>

                <div class="grid gap-2">
                    <label for="password" class="text-sm text-[#737373]">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Enter password"
                        class="h-12 outline-none mb-1 font-semibold text-[#171717] text-sm px-3 py-2 rounded-xl border-2 border-[#f5f5f5] focus:ring-[#171717] focus-within:ring-[#171717] focus-within:border-[#171717] focus:border-[#171717] <?= hasError('password') ? 'bg-rose-50' : 'bg-[#f5f5f5]' ?>">
                    <?php if (hasError('password')): ?>
                        <span class="bg-rose-50 text-sm text-rose-600 px-4 py-2 rounded-lg"><?= errors('password') ?></span>
                    <?php endif; ?>
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center items-center gap-1.25 rounded-xl bg-[#181818] hover:bg-[#181818]/95 px-4 py-3.5 font-semibold text-white text-sm outline-none cursor-pointer">Sign In</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include_once __DIR__ . '/../../../Layouts/Admin/Footer.php' ?>