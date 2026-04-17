<?php include_once __DIR__ . '/../../../Layouts/Admin/AfterLogin.php' ?>

<div class="max-w-[1520px] 2xl:w-full py-4 mx-5 2xl:mx-auto mb-6">
    <div class="flex flex-wrap gap-3">
        <a href="<?= route('categories.index') ?>" class="flex flex-col flex-[1_1_103px] justify-center items-center gap-2 bg-rose-100 rounded-xl p-3">
            <div>
                <img src="<?= asset('assets/images/category.png') ?>" alt="">
            </div>
            <div>
                <p class="text-sm lg:text-base text-center font-semibold">Category</p>
            </div>
        </a>
        <div class="flex flex-col flex-[1_1_103px] justify-center items-center gap-2 bg-yellow-100 rounded-xl p-3">
            <div>
                <img src="<?= asset('assets/images/product.png') ?>" alt="">
            </div>
            <div>
                <p class="text-sm lg:text-base text-center font-semibold">Product</p>
            </div>
        </div>
        <div class="flex flex-col flex-[1_1_103px] justify-center items-center gap-2 bg-sky-100 rounded-xl p-3">
            <div>
                <img src="<?= asset('assets/images/color.png') ?>" alt="">
            </div>
            <div>
                <p class="text-sm lg:text-base text-center font-semibold">Variant</p>
            </div>
        </div>
        <div class="flex flex-col flex-[1_1_103px] justify-center items-center gap-2 bg-cyan-100 rounded-xl p-3">
            <div>
                <img src="<?= asset('assets/images/order.png') ?>" alt="">
            </div>
            <div>
                <p class="text-sm lg:text-base text-center font-semibold">Order</p>
            </div>
        </div>
        <div class="flex flex-col flex-[1_1_103px] justify-center items-center gap-2 bg-indigo-100 rounded-xl p-3">
            <div>
                <img src="<?= asset('assets/images/shop.png') ?>" alt="">
            </div>
            <div>
                <p class="text-sm lg:text-base text-center font-semibold">Shop</p>
            </div>
        </div>
        <div class="flex flex-col flex-[1_1_103px] justify-center items-center gap-2 bg-emerald-100 rounded-xl p-3">
            <div>
                <img src="<?= asset('assets/images/inventory.png') ?>" alt="">
            </div>
            <div>
                <p class="text-sm lg:text-base text-center font-semibold">Inventory</p>
            </div>
        </div>
        <div class="flex flex-col flex-[1_1_103px] justify-center items-center gap-2 bg-orange-100 rounded-xl p-3">
            <div>
                <img src="<?= asset('assets/images/delivery.png') ?>" alt="">
            </div>
            <div>
                <p class="text-sm lg:text-base text-center font-semibold">Delivery</p>
            </div>
        </div>
        <div class="flex flex-col flex-[1_1_103px] justify-center items-center gap-2 bg-lime-100 rounded-xl p-3">
            <div>
                <img src="<?= asset('assets/images/profit.png') ?>" alt="">
            </div>
            <div>
                <p class="text-sm lg:text-base text-center font-semibold">Profit</p>
            </div>
        </div>
        <div class="flex flex-col flex-[1_1_103px] justify-center items-center gap-2 bg-purple-100 rounded-xl p-3">
            <div>
                <img src="<?= asset('assets/images/user.png') ?>" alt="">
            </div>
            <div>
                <p class="text-sm lg:text-base text-center font-semibold">User</p>
            </div>
        </div>
        <div class="flex flex-col flex-[1_1_103px] justify-center items-center gap-2 bg-stone-100 rounded-xl p-3">
            <div>
                <img src="<?= asset('assets/images/setting.png') ?>" alt="">
            </div>
            <div>
                <p class="text-sm lg:text-base text-center font-semibold">Setting</p>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../../../Layouts/Admin/Footer.php' ?>