<?= view('partials/head', ['title' => 'Vendors']) ?>

<body class="bg-light dark:bg-dark text-textprimary">
    <div class="flex w-full min-h-screen">
        <?= view('partials/sidebar') ?>

        <!-- Main Wrapper -->
        <div class="flex-1 xl:ml-64 w-full bg-white dark:bg-dark min-h-screen transition-all">
            <?= view('partials/header') ?>

            <!-- Body Content -->
            <main class="p-[30px] container mx-auto">
                <div class="header mb-8">
                    <h1 class="text-2xl font-bold">Vendor Directory</h1>
                    <p class="text-gray-500">Manage and monitor all onboarded vendors.</p>
                </div>

                <!-- Vendor Table -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-border overflow-hidden">
                    <div class="p-6 border-b border-border flex justify-between items-center">
                        <h5 class="text-lg font-bold text-dark dark:text-white">All Vendors</h5>
                        <button
                            class="bg-primary hover:bg-yellow-500 text-dark font-bold py-2 px-4 rounded-lg transition-colors text-xs flex items-center gap-2">
                            <iconify-icon icon="tabler:plus" width="16"></iconify-icon> Add New Vendor
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left align-middle text-gray-500 font-medium">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th class="py-3 px-6 font-semibold">Id</th>
                                    <th class="py-3 px-6 font-semibold">Vendor Info</th>
                                    <th class="py-3 px-6 font-semibold">Contact</th>
                                    <th class="py-3 px-6 font-semibold">Status</th>
                                    <th class="py-3 px-6 font-semibold">KYC</th>
                                    <th class="py-3 px-6 font-semibold">Joined Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <?php if (!empty($vendors)): ?>
                                    <?php foreach ($vendors as $vendor): ?>
                                        <tr
                                            class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                            <td class="py-4 px-6 font-mono text-xs text-gray-400">
                                                #<?= $vendor['id'] ?>
                                            </td>
                                            <td class="py-4 px-6">
                                                <div class="flex items-center gap-3">
                                                    <img src="<?= !empty($vendor['profile_image']) ? base_url($vendor['profile_image']) : 'https://api.dicebear.com/7.x/initials/svg?seed=' . urlencode($vendor['name']) ?>"
                                                        alt="<?= esc($vendor['name']) ?>"
                                                        class="w-10 h-10 rounded-full object-cover border border-border"
                                                        onerror="this.src='https://via.placeholder.com/40'">
                                                    <div class="font-bold text-dark dark:text-white">
                                                        <?= esc($vendor['name']) ?>
                                                        <span
                                                            class="block font-normal text-xs text-gray-400 mt-0.5"><?= esc($vendor['business_name']) ?></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 px-6">
                                                <div class="text-[11px] space-y-1">
                                                    <div class="flex items-center gap-1"><iconify-icon icon="tabler:mail"
                                                            class="text-gray-300"></iconify-icon> <a
                                                            href="mailto:<?= esc($vendor['email']) ?>"
                                                            class="hover:text-primary transition-colors"><?= esc($vendor['email']) ?></a>
                                                    </div>
                                                    <div class="flex items-center gap-1"><iconify-icon icon="tabler:phone"
                                                            class="text-gray-300"></iconify-icon> <a
                                                            href="tel:<?= esc($vendor['phone']) ?>"
                                                            class="hover:text-primary transition-colors"><?= esc($vendor['phone']) ?></a>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 px-6">
                                                <span
                                                    class="px-2 py-1 <?= $vendor['status'] == 'active' ? 'bg-lightsuccess text-success' : 'bg-lighterror text-error' ?> rounded text-[10px] font-bold uppercase">
                                                    <?= $vendor['status'] ?>
                                                </span>
                                            </td>
                                            <td class="py-4 px-6">
                                                <span
                                                    class="px-2 py-1 <?= $vendor['kyc_status'] == 'approved' ? 'bg-lightsuccess text-success' : ($vendor['kyc_status'] == 'pending' ? 'bg-lightwarning text-warning' : 'bg-lighterror text-error') ?> rounded text-[10px] font-bold uppercase">
                                                    <?= $vendor['kyc_status'] ?>
                                                </span>
                                            </td>
                                            <td class="py-4 px-6 text-gray-400 text-xs font-mono">
                                                <?= date('M d, Y', strtotime($vendor['created_at'])) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr class="bg-white dark:bg-gray-800">
                                        <td colspan="6" class="py-4 px-6 text-center text-gray-500 italic py-12">No vendors
                                            found in the database.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.js"></script>
</body>

</html>