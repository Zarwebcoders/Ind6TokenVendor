<?= view('partials/head', ['title' => 'API Settings']) ?>

<body class="bg-light dark:bg-dark text-textprimary">
    <div class="flex w-full min-h-screen">
        <?= view('partials/sidebar') ?>

        <!-- Main Wrapper -->
        <div class="flex-1 xl:ml-64 w-full bg-white dark:bg-dark min-h-screen transition-all">
            <?= view('partials/header') ?>

            <!-- Body Content -->
            <main class="p-[30px] container mx-auto">
                <div class="header mb-8">
                    <h1 class="text-2xl font-bold">API Credentials</h1>
                    <p class="text-gray-500">Manage your bearer tokens, IP whitelist, and webhook notifications.</p>
                </div>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="p-4 mb-6 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400 border border-green-200"
                        role="alert">
                        <span class="font-bold">Success!</span> <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Token Information -->
                    <div class="space-y-6">
                        <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-sm border border-border">
                            <div class="flex justify-between items-center mb-6">
                                <h5 class="text-lg font-bold">Production Bearer Token</h5>
                                <span
                                    class="bg-lightsuccess text-success text-[10px] font-bold px-2 py-1 rounded-full uppercase">Live</span>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Public
                                        Token</label>
                                    <div
                                        class="flex items-center gap-2 p-3 bg-gray-50 dark:bg-gray-900 border border-border rounded-lg group">
                                        <code
                                            class="flex-1 text-sm font-mono text-primary font-bold overflow-hidden text-ellipsis"
                                            id="tokenText"><?= $vendor['api_token'] ?: 'Not generated' ?></code>
                                        <button onclick="copyTo('tokenText')"
                                            class="p-2 text-gray-400 hover:text-primary transition-colors">
                                            <iconify-icon icon="tabler:copy" width="20"></iconify-icon>
                                        </button>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">API
                                        Secret</label>
                                    <div
                                        class="flex items-center gap-2 p-3 bg-gray-50 dark:bg-gray-900 border border-border rounded-lg">
                                        <code
                                            class="flex-1 text-xs font-mono text-gray-500 overflow-hidden text-ellipsis"><?= $vendor['api_secret'] ? '••••••••••••••••••••••••••••••••' : 'Not generated' ?></code>
                                        <iconify-icon icon="tabler:lock" width="18"
                                            class="text-gray-300"></iconify-icon>
                                    </div>
                                    <p class="text-[11px] text-gray-400 mt-2">The secret key is used for signature
                                        verification in future updates.</p>
                                </div>
                            </div>

                            <hr class="my-8 border-border">

                            <form action="<?= base_url('vendor/api-settings/update') ?>" method="POST">
                                <input type="hidden" name="action" value="generate_token">
                                <button type="submit"
                                    class="w-full flex items-center justify-center gap-2 py-3 px-4 bg-white dark:bg-gray-700 border border-primary text-primary font-bold rounded-lg hover:bg-lightprimary transition-colors text-sm"
                                    onclick="return confirm('WARNING: Generating a new token will immediately invalidate your current one. Are you sure?')">
                                    <iconify-icon icon="tabler:refresh" width="18"></iconify-icon>
                                    Regenerate API Keys
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Webhook and Security Settings -->
                    <div class="space-y-6">
                        <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-sm border border-border">
                            <h5 class="text-lg font-bold mb-6">Security & Automation</h5>
                            <form action="<?= base_url('vendor/api-settings/update') ?>" method="POST"
                                class="space-y-6">
                                <input type="hidden" name="action" value="update_ips">

                                <div class="space-y-2">
                                    <label class="flex items-center gap-2 text-sm font-bold text-dark dark:text-white">
                                        <iconify-icon icon="tabler:shield-check"
                                            class="text-success text-lg"></iconify-icon>
                                        Whitelisted IP Addresses
                                    </label>
                                    <textarea name="whitelisted_ips" rows="3"
                                        class="w-full bg-gray-50 dark:bg-gray-900 border border-border rounded-lg p-3 text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none"
                                        placeholder="Enter server IPs separated by commas (e.g. 192.168.1.1, 8.8.4.4)"><?= $vendor['whitelisted_ips'] ?></textarea>
                                    <p class="text-[11px] text-gray-400">If left empty, any IP will be allowed (not
                                        recommended for production). Only your server IP should be here.</p>
                                </div>

                                <div class="space-y-2">
                                    <label class="flex items-center gap-2 text-sm font-bold text-dark dark:text-white">
                                        <iconify-icon icon="tabler:webhook" class="text-info text-lg"></iconify-icon>
                                        Webhook URL
                                    </label>
                                    <input type="url" name="webhook_url" value="<?= $vendor['webhook_url'] ?>"
                                        class="w-full bg-gray-50 dark:bg-gray-900 border border-border rounded-lg p-3 text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none"
                                        placeholder="https://your-domain.com/callbacks/payment-success">
                                    <p class="text-[11px] text-gray-400">We will send a POST request with the payment
                                        payload to this URL when a transaction is completed.</p>
                                </div>

                                <button type="submit"
                                    class="w-full flex items-center justify-center gap-2 py-3 px-4 bg-primary text-dark font-bold rounded-lg hover:bg-yellow-500 transition-colors shadow-lg active:scale-[0.98]">
                                    <iconify-icon icon="tabler:device-floppy" width="20"></iconify-icon>
                                    Update Configuration
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        function copyTo(id) {
            const el = document.getElementById(id);
            if (el.innerText.includes('Not generated')) return;
            navigator.clipboard.writeText(el.innerText).then(() => {
                alert('Copied to clipboard!');
            });
        }
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.js"></script>
</body>

</html>