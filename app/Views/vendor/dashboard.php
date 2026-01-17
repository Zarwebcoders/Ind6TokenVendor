<?= view('partials/head', ['title' => 'Vendor Dashboard']) ?>

<body class="bg-light dark:bg-dark text-textprimary">
    <div class="flex w-full min-h-screen">
        <?= view('partials/sidebar') ?>

        <!-- Main Wrapper -->
        <div class="flex-1 xl:ml-64 w-full bg-white dark:bg-dark min-h-screen transition-all">
            <?= view('partials/header') ?>

            <!-- Body Content -->
            <main class="p-[30px] container mx-auto">
                <div class="header mb-8">
                    <h1 class="text-2xl font-bold">Welcome, <?= esc($vendor['name']) ?></h1>
                    <p class="text-gray-500">Overview of your payment activity and integration status.</p>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-border">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h5 class="text-base font-bold text-gray-400 uppercase text-xs mb-1">Total Transactions
                                </h5>
                                <h2 class="text-3xl font-bold text-primary"><?= $total_payments ?></h2>
                            </div>
                            <span
                                class="w-12 h-12 rounded-full bg-lightprimary flex items-center justify-center text-primary">
                                <iconify-icon icon="tabler:layers-intersect" width="24"></iconify-icon>
                            </span>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-border">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h5 class="text-base font-bold text-gray-400 uppercase text-xs mb-1">Success Rate</h5>
                                <h2 class="text-3xl font-bold text-success">
                                    <?php
                                    $rate = $total_payments > 0 ? ($success_payments / $total_payments) * 100 : 0;
                                    echo number_format($rate, 1) . '%';
                                    ?>
                                </h2>
                            </div>
                            <span
                                class="w-12 h-12 rounded-full bg-lightsuccess flex items-center justify-center text-success">
                                <iconify-icon icon="tabler:trend-up" width="24"></iconify-icon>
                            </span>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-border">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h5 class="text-base font-bold text-gray-400 uppercase text-xs mb-1">Live Endpoint</h5>
                                <p class="text-sm font-mono text-gray-600 dark:text-gray-400 break-all">
                                    v1/payment/create</p>
                            </div>
                            <span
                                class="w-12 h-12 rounded-full bg-lightinfo flex items-center justify-center text-info">
                                <iconify-icon icon="tabler:plug-connected" width="24"></iconify-icon>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-border overflow-hidden">
                    <div class="p-6 border-b border-border flex justify-between items-center">
                        <h5 class="text-lg font-bold">Recent API Transactions</h5>
                        <a href="<?= base_url('vendor/api-docs') ?>" class="text-primary text-sm hover:underline">View
                            Docs &rarr;</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 dark:bg-gray-700 text-gray-500 uppercase text-xs">
                                <tr>
                                    <th class="px-6 py-4">Date</th>
                                    <th class="px-6 py-4">Order ID</th>
                                    <th class="px-6 py-4">Amount</th>
                                    <th class="px-6 py-4">UTR</th>
                                    <th class="px-6 py-4">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <?php foreach ($recent_payments as $p): ?>
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                            <?= date('d M, Y H:i', strtotime($p['created_at'])) ?></td>
                                        <td class="px-6 py-4 font-medium"><?= $p['txn_id'] ?></td>
                                        <td class="px-6 py-4 font-bold text-dark dark:text-white">
                                            ₹<?= number_format($p['amount'], 2) ?></td>
                                        <td class="px-6 py-4 text-xs font-mono">
                                            <?= $p['utr'] ?: '<span class="text-gray-400 italic">Pending</span>' ?></td>
                                        <td class="px-6 py-4">
                                            <?php
                                            $badge = $p['status'] == 'success' ? 'bg-lightsuccess text-success' : ($p['status'] == 'pending' ? 'bg-lightwarning text-warning' : 'bg-lighterror text-error');
                                            ?>
                                            <span
                                                class="px-2.5 py-1 rounded text-xs font-bold uppercase <?= $badge ?>"><?= $p['status'] ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($recent_payments)): ?>
                                    <tr>
                                        <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">No transactions
                                            found in this account yet.</td>
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