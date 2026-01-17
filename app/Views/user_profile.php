<?= view('partials/head', ['title' => 'Profile']) ?>

<body class="bg-light dark:bg-dark text-textprimary">
    <div class="flex w-full min-h-screen">
        <?= view('partials/sidebar') ?>

        <!-- Main Wrapper -->
        <div class="flex-1 xl:ml-64 w-full bg-white dark:bg-dark min-h-screen transition-all">
            <?= view('partials/header') ?>

            <!-- Body Content -->
            <main class="p-[30px] container mx-auto">
                <div class="header mb-8">
                    <h1 class="text-2xl font-bold text-dark dark:text-white">Account Settings</h1>
                    <p class="text-gray-500">Update your personal information and login credentials.</p>
                </div>

                <div class="flex justify-center">
                    <div class="w-full max-w-2xl">
                        <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-sm border border-border">
                            <div class="flex flex-col items-center mb-8">
                                <div class="relative">
                                    <div
                                        class="w-24 h-24 rounded-full border border-border flex items-center justify-center bg-gray-50 dark:bg-gray-700 text-gray-400">
                                        <iconify-icon icon="solar:user-linear" width="48" height="48"></iconify-icon>
                                    </div>
                                    <button
                                        class="absolute bottom-0 right-0 p-2 bg-primary text-dark rounded-full shadow-lg hover:scale-110 transition-transform">
                                        <iconify-icon icon="tabler:camera" width="16"></iconify-icon>
                                    </button>
                                </div>
                                <h2 class="mt-4 text-xl font-bold"><?= esc($user['name'] ?? 'User') ?></h2>
                                <p class="text-sm text-gray-500"><?= esc($user['email'] ?? '') ?></p>
                            </div>

                            <!-- Messages -->
                            <?php if (session()->getFlashdata('success')): ?>
                                <div class="p-4 mb-6 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400 border border-green-200"
                                    role="alert">
                                    <span class="font-bold">Success!</span> <?= session()->getFlashdata('success') ?>
                                </div>
                            <?php endif; ?>
                            <?php if (session()->getFlashdata('error')): ?>
                                <div class="p-4 mb-6 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 border border-red-200"
                                    role="alert">
                                    <span class="font-bold">Error!</span> <?= session()->getFlashdata('error') ?>
                                </div>
                            <?php endif; ?>

                            <form action="<?= base_url('user-profile/update') ?>" method="post" class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <label for="name"
                                            class="text-sm font-bold text-gray-600 dark:text-gray-400">Full Name</label>
                                        <input id="name" name="name" type="text"
                                            class="w-full bg-gray-50 dark:bg-gray-900 border border-border text-gray-900 text-sm rounded-lg focus:ring-primary focus:border-primary block p-3 outline-none"
                                            placeholder="Enter your fullname" required
                                            value="<?= old('name', $user['name'] ?? '') ?>">
                                    </div>
                                    <div class="space-y-2">
                                        <label for="email"
                                            class="text-sm font-bold text-gray-600 dark:text-gray-400">Email
                                            Address</label>
                                        <input id="email" name="email" type="email"
                                            class="w-full bg-gray-50 dark:bg-gray-900 border border-border text-gray-900 text-sm rounded-lg focus:ring-primary focus:border-primary block p-3 outline-none"
                                            placeholder="Enter your email" required
                                            value="<?= old('email', $user['email'] ?? '') ?>">
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label for="password"
                                        class="text-sm font-bold text-gray-600 dark:text-gray-400">Change
                                        Password</label>
                                    <div class="relative">
                                        <input id="password" name="password" type="password"
                                            class="w-full bg-gray-50 dark:bg-gray-900 border border-border text-gray-900 text-sm rounded-lg focus:ring-primary focus:border-primary block p-3 outline-none"
                                            placeholder="Leave blank to keep current password">
                                        <iconify-icon icon="tabler:lock" class="absolute right-3 top-3 text-gray-400"
                                            width="18"></iconify-icon>
                                    </div>
                                    <p class="text-[11px] text-gray-400">Min 8 characters with numbers.</p>
                                </div>

                                <button type="submit"
                                    class="w-full text-dark bg-primary hover:bg-yellow-500 font-bold rounded-lg text-sm px-5 py-3 transition-all active:scale-[0.98] shadow-lg flex items-center justify-center gap-2">
                                    <iconify-icon icon="tabler:device-floppy" width="20"></iconify-icon>
                                    Save Profile Changes
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.js"></script>
</body>

</html>