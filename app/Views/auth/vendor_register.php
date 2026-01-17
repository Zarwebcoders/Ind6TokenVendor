<?= view('partials/head', ['title' => 'Vendor Registration']) ?>

<body class="bg-lightprimary dark:bg-darkprimary">

    <div class="min-h-screen flex items-center justify-center p-4 bg-lightprimary dark:bg-gray-800">
        <div class="w-full max-w-2xl bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 sm:p-8 border-none">
            <!-- Logo -->
            <div class="mx-auto mb-6 flex justify-center">
                <a href="<?= base_url() ?>" class="text-2xl font-extrabold text-primary flex items-center gap-2">
                    Ind6Token
                </a>
            </div>

            <h2 class="text-2xl font-bold text-center text-gray-900 dark:text-white mb-6">Vendor Registration</h2>

            <!-- Alerts -->
            <?php if (session()->getFlashdata('error')): ?>
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400"
                    role="alert">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400"
                    role="alert">
                    <ul class="list-disc list-inside">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <form action="<?= site_url('/register') ?>" method="post" enctype="multipart/form-data" class="space-y-4">
                <?= csrf_field() ?>

                <div class="grid gap-4 sm:grid-cols-2">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Your Name
                            <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="<?= old('name') ?>"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary dark:focus:border-primary"
                            placeholder="John Doe" required>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email
                            Address <span class="text-red-500">*</span></label>
                        <input type="email" name="email" id="email" value="<?= old('email') ?>"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary dark:focus:border-primary"
                            placeholder="name@company.com" required>
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Phone
                            Number <span class="text-red-500">*</span></label>
                        <input type="tel" name="phone" id="phone" value="<?= old('phone') ?>"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary dark:focus:border-primary"
                            placeholder="1234567890" required>
                    </div>

                    <!-- Business Name -->
                    <div>
                        <label for="business_name"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Business Name</label>
                        <input type="text" name="business_name" id="business_name" value="<?= old('business_name') ?>"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary dark:focus:border-primary"
                            placeholder="My Enterprise">
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <!-- Wallet Address -->
                    <div class="sm:col-span-2">
                        <label for="wallet_address"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Wallet Address</label>
                        <input type="text" name="wallet_address" id="wallet_address"
                            value="<?= old('wallet_address') ?>"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary dark:focus:border-primary"
                            placeholder="0x...">
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <!-- Referral Code -->
                    <div>
                        <label for="referral_code"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Referral Code</label>
                        <input type="text" name="referral_code" id="referral_code" value="<?= old('referral_code') ?>"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary dark:focus:border-primary"
                            placeholder="REF123">
                    </div>

                    <!-- Profile Image -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                            for="profile_image">Profile Image</label>
                        <input
                            class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                            id="profile_image" name="profile_image" type="file" accept="image/*">
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-1">
                    <!-- Password -->
                    <div>
                        <label for="password"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password <span
                                class="text-red-500">*</span></label>
                        <input type="password" name="password" id="password"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary dark:focus:border-primary"
                            required>
                    </div>
                </div>

                <button type="submit"
                    class="w-full text-white bg-primary hover:bg-yellow-500 font-medium rounded-md text-sm px-5 py-2.5 text-center transition-colors">
                    Register Vendor
                </button>

                <div class="flex gap-2 text-base text-gray-500 font-medium mt-6 items-center justify-center">
                    <p>Already have an Account?</p>
                    <a href="<?= site_url('login') ?>" class="text-primary text-sm font-medium hover:underline">Sign
                        in</a>
                </div>
            </form>

        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.js"></script>
</body>

</html>