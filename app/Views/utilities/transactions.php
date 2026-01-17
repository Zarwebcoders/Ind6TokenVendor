<?= view('partials/head', ['title' => 'Transactions']) ?>

<body class="bg-light dark:bg-dark text-textprimary">
    <div class="flex w-full min-h-screen">
        <?= view('partials/sidebar') ?>

        <!-- Main Wrapper -->
        <div class="flex-1 xl:ml-64 w-full bg-white dark:bg-dark min-h-screen transition-all">
            <?= view('partials/header') ?>

            <!-- Body Content -->
            <main class="p-[30px] container mx-auto">
                <div class="header mb-8">
                    <h1 class="text-2xl font-bold">Transaction History</h1>
                    <p class="text-gray-500">View and audit all payments processed through the platform.</p>
                </div>

                <!-- Transactions Table -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-border overflow-hidden">
                    <div class="p-6 border-b border-border flex justify-between items-center">
                        <h5 class="text-lg font-bold text-dark dark:text-white">All Transactions</h5>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left align-middle text-gray-500 font-medium">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th class="py-3 px-6 font-semibold">Id</th>
                                    <th class="py-3 px-6 font-semibold">Vendor</th>
                                    <th class="py-3 px-6 font-semibold">Amount</th>
                                    <th class="py-3 px-6 font-semibold">Status</th>
                                    <th class="py-3 px-6 font-semibold">Reference</th>
                                    <th class="py-3 px-6 font-semibold">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <?php if (!empty($payments)): ?>
                                    <?php foreach ($payments as $payment): ?>
                                        <tr
                                            class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                            <td class="py-4 px-6 font-mono text-xs text-gray-400">
                                                #<?= $payment['id'] ?>
                                            </td>
                                            <td class="py-4 px-6">
                                                <div class="font-bold text-dark dark:text-white">
                                                    <?= esc($payment['vendor_name']) ?>
                                                    <span
                                                        class="block font-normal text-xs text-gray-400 mt-0.5"><?= esc($payment['vendor_email']) ?></span>
                                                </div>
                                            </td>
                                            <td class="py-4 px-6 font-bold text-dark dark:text-white">
                                                ₹ <?= number_format($payment['amount'], 2) ?>
                                            </td>
                                            <td class="py-4 px-6">
                                                <?php
                                                $statusClass = match ($payment['status']) {
                                                    'success' => 'bg-lightsuccess text-success',
                                                    'pending' => 'bg-lightwarning text-warning',
                                                    'failed' => 'bg-lighterror text-error',
                                                    default => 'bg-lightsecondary text-secondary',
                                                };
                                                ?>
                                                <span
                                                    class="px-2 py-1 <?= $statusClass ?> rounded text-[10px] font-bold uppercase">
                                                    <?= $payment['status'] ?>
                                                </span>
                                            </td>
                                            <td class="py-4 px-6">
                                                <div class="text-[11px] space-y-1">
                                                    <div class="font-bold text-gray-600 dark:text-gray-300">TXN:
                                                        <?= esc($payment['txn_id']) ?></div>
                                                    <div class="text-gray-400">Ref: <?= esc($payment['reference_no']) ?></div>
                                                </div>
                                            </td>
                                            <td class="py-4 px-6 text-gray-400 text-xs font-mono">
                                                <?= date('M d, Y h:i A', strtotime($payment['created_at'])) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr class="bg-white dark:bg-gray-800">
                                        <td colspan="6" class="py-4 px-6 text-center text-gray-500 py-12 italic">No
                                            transactions were found.</td>
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