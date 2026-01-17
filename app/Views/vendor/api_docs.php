<?= view('partials/head', ['title' => 'API Documentation']) ?>

<body class="bg-light dark:bg-dark text-textprimary">
    <div class="flex w-full min-h-screen">
        <?= view('partials/sidebar') ?>

        <!-- Main Wrapper -->
        <div class="flex-1 xl:ml-64 w-full bg-white dark:bg-dark min-h-screen transition-all">
            <?= view('partials/header') ?>

            <!-- Body Content -->
            <main class="p-[30px] container mx-auto">
                <div class="header mb-12 flex flex-col items-center text-center">
                    <span
                        class="bg-lightprimary text-primary px-3 py-1 rounded-full text-xs font-bold uppercase mb-4">Documentation</span>
                    <h1 class="text-3xl font-extrabold lg:text-4xl">Developer Integration API</h1>
                    <p class="text-gray-500 mt-2 max-w-2xl text-lg">Detailed guide for integrating our white-label
                        payment processing engine into your own products.</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                    <!-- Sidebar Navigation -->
                    <div class="lg:col-span-1">
                        <div class="sticky top-24 space-y-2">
                            <h6 class="text-xs font-bold text-gray-400 uppercase px-4 mb-4">Endpoints</h6>
                            <a href="#auth"
                                class="flex items-center gap-3 px-4 py-2 rounded-lg text-sm hover:bg-lightprimary hover:text-primary transition-colors font-medium">Authentication</a>
                            <a href="#create"
                                class="flex items-center gap-3 px-4 py-2 rounded-lg text-sm hover:bg-lightprimary hover:text-primary transition-colors font-medium text-gray-600 dark:text-gray-400">Create
                                Payment</a>
                            <a href="#status"
                                class="flex items-center gap-3 px-4 py-2 rounded-lg text-sm hover:bg-lightprimary hover:text-primary transition-colors font-medium text-gray-600 dark:text-gray-400">Check
                                Status</a>
                            <a href="#webhooks"
                                class="flex items-center gap-3 px-4 py-2 rounded-lg text-sm hover:bg-lightprimary hover:text-primary transition-colors font-medium text-gray-600 dark:text-gray-400">Webhooks</a>
                        </div>
                    </div>

                    <!-- Main Documentation Area -->
                    <div class="lg:col-span-3 space-y-16">

                        <!-- Auth Section -->
                        <section id="auth">
                            <h2 class="text-2xl font-bold mb-4 flex items-center gap-3">
                                <span
                                    class="w-8 h-8 rounded bg-lightprimary text-primary flex items-center justify-center text-sm">01</span>
                                Authentication
                            </h2>
                            <p class="text-gray-600 dark:text-gray-400 mb-6 font-medium">All requests must be
                                authenticated using the <strong>Bearer Token</strong> from your settings page. Requests
                                must be sent over HTTPS.</p>
                            <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
                                <div
                                    class="px-4 py-2 bg-gray-800 border-b border-gray-700 flex justify-between items-center text-xs text-gray-400 font-mono">
                                    <span>Request Header</span>
                                    <span>HTTP</span>
                                </div>
                                <pre
                                    class="p-6 text-yellow-400 text-sm font-mono overflow-auto">Authorization: Bearer YOUR_API_TOKEN</pre>
                            </div>
                        </section>

                        <!-- Create Payment Section -->
                        <section id="create">
                            <div class="flex items-center gap-3 mb-4">
                                <h2 class="text-2xl font-bold flex items-center gap-3">
                                    <span
                                        class="w-8 h-8 rounded bg-lightprimary text-primary flex items-center justify-center text-sm">02</span>
                                    Create Payment
                                </h2>
                                <span class="bg-blue-100 text-blue-600 text-xs font-bold px-2 py-1 rounded">POST</span>
                            </div>
                            <p class="text-gray-600 dark:text-gray-400 mb-6 font-medium">Initiates a new payment request
                                and returns a UPI intent URL or gateway link.</p>

                            <div
                                class="bg-gray-50 dark:bg-gray-800 border border-border rounded-xl p-4 font-mono text-sm mb-6 flex items-center gap-3">
                                <span class="text-primary font-bold">API_URL</span>
                                <span class="text-gray-500">/api/v1/payment/create</span>
                            </div>

                            <div class="overflow-x-auto mb-8 border border-border rounded-xl">
                                <table class="w-full text-left text-sm">
                                    <thead
                                        class="bg-gray-50 dark:bg-gray-700 text-xs uppercase text-gray-500 font-bold">
                                        <tr>
                                            <th class="px-6 py-4">Field</th>
                                            <th class="px-6 py-4">Type</th>
                                            <th class="px-6 py-4">Required</th>
                                            <th class="px-6 py-4">Description</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                            <td class="px-6 py-4 font-mono font-bold">amount</td>
                                            <td class="px-6 py-4 text-xs">float</td>
                                            <td class="px-6 py-4 text-error font-bold text-xs uppercase">Yes</td>
                                            <td class="px-6 py-4 text-gray-500">Amount in INR (e.g. 500.00).</td>
                                        </tr>
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                            <td class="px-6 py-4 font-mono font-bold">customer_name</td>
                                            <td class="px-6 py-4 text-xs">string</td>
                                            <td class="px-6 py-4 text-error font-bold text-xs uppercase">Yes</td>
                                            <td class="px-6 py-4 text-gray-500">Name of the customer for display on QR.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
                                <div
                                    class="px-4 py-2 bg-gray-800 border-b border-gray-700 flex justify-between items-center text-xs text-gray-400 font-mono">
                                    <span>Sample Response</span>
                                    <span>JSON</span>
                                </div>
                                <pre class="p-6 text-green-400 text-sm font-mono overflow-auto">{
  "success": true,
  "order_id": "ORD_20261750",
  "payment_url": "upi://pay?pa=zarweb@...",
  "message": "Transaction created successfully"
}</pre>
                            </div>
                        </section>

                        <!-- Status Check Section -->
                        <section id="status">
                            <div class="flex items-center gap-3 mb-4">
                                <h2 class="text-2xl font-bold flex items-center gap-3">
                                    <span
                                        class="w-8 h-8 rounded bg-lightprimary text-primary flex items-center justify-center text-sm">03</span>
                                    Check Status
                                </h2>
                                <span class="bg-blue-100 text-blue-600 text-xs font-bold px-2 py-1 rounded">POST</span>
                            </div>
                            <p class="text-gray-600 dark:text-gray-400 mb-6 font-medium">Query the current status of a
                                transaction assigned to your vendor account.</p>

                            <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
                                <div
                                    class="px-4 py-2 bg-gray-800 border-b border-gray-700 flex justify-between items-center text-xs text-gray-400 font-mono">
                                    <span>Request Body</span>
                                    <span>JSON</span>
                                </div>
                                <pre class="p-6 text-yellow-400 text-sm font-mono overflow-auto">{
  "order_id": "ORD_20261750"
}</pre>
                            </div>
                        </section>

                        <!-- Webhooks Section -->
                        <section id="webhooks" class="bg-lightprimary/20 p-8 rounded-2xl border border-lightprimary">
                            <div class="flex items-center gap-3 mb-6">
                                <h2 class="text-2xl font-bold flex items-center gap-3">
                                    <iconify-icon icon="tabler:webhook" class="text-primary"></iconify-icon>
                                    Automated Webhooks
                                </h2>
                            </div>
                            <p class="text-gray-600 dark:text-gray-400 mb-8 font-medium">To avoid polling for every
                                transaction, the engine will automatically forward a success response to your
                                <strong>Webhook URL</strong> when we detect a completed payment.</p>

                            <div
                                class="bg-white dark:bg-gray-900 border border-lightprimary rounded-xl overflow-hidden">
                                <div
                                    class="px-4 py-2 bg-lightprimary text-primary flex justify-between items-center text-xs font-bold uppercase">
                                    <span>Data Delivered to your server</span>
                                </div>
                                <pre class="p-6 text-sm font-mono overflow-auto">{
  "order_id": "ORD_20261750",
  "amount": 500,
  "status": "success",
  "utr": "REF260117...",
  "timestamp": "2026-01-17 18:50:00"
}</pre>
                            </div>
                        </section>

                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.js"></script>
</body>

</html>