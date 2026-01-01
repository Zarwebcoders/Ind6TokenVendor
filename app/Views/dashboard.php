<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ind6Token Admin - Dashboard</title>
    <link rel="icon" href="<?= base_url('favicon.svg') ?>" type="image/svg+xml" />
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#EBBE31',
                        lightprimary: '#FCF5D9',
                        secondary: '#050401',
                        lightsecondary: '#E6E6E5',
                        success: '#13DEB9',
                        lightsuccess: '#E6FFFA',
                        info: '#539BFF',
                        lightinfo: '#EBF3FE',
                        warning: '#FFAE1F',
                        lightwarning: '#FEF5E5',
                        error: '#FA896B',
                        lighterror: '#FDEDE8',
                        dark: '#050401',
                        light: '#F6F9FC',
                        border: '#EAEFF4',
                        inputBorder: '#DFE5EF',
                        gray: '#5A6A85',
                        link: '#5A6A85',
                        darklink: '#fff',
                        textprimary: '#050401',
                        textsecondary: '#050401',
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <!-- Iconify -->
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <!-- Flowbite -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.css" rel="stylesheet" />
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* SimpleBar Scrollbar adjustment might be needed similar to simplebar-react */
        ::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }
    </style>
</head>

<body class="bg-light dark:bg-dark text-textprimary">

    <div class="flex w-full min-h-screen">

        <!-- Sidebar -->
        <aside
            class="fixed top-0 left-0 w-64 h-full bg-white dark:bg-dark border-r border-border z-30 transition-transform -translate-x-full xl:translate-x-0"
            id="sidebar">
            <!-- Logo -->
            <div class="px-6 py-5 flex items-center justify-between">
                <a href="#" class="text-2xl font-extrabold text-primary flex items-center gap-2">
                    Ind6Token
                </a>
                <button type="button"
                    class="xl:hidden text-gray-400 hover:text-gray-900 rounded-lg text-sm p-1.5 absolute top-2.5 right-2.5 inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
                    onclick="document.getElementById('sidebar').classList.add('-translate-x-full')">
                    <iconify-icon icon="tabler:x" width="20"></iconify-icon>
                </button>
            </div>

            <!-- Nav -->
            <div class="px-4 py-4 overflow-y-auto h-[calc(100vh-100px)]">

                <!-- Section: Home -->
                <div class="mb-2">
                    <h5 class="px-4 text-xs font-bold text-gray-400 uppercase mb-3 mt-4">Home</h5>
                    <ul>
                        <li>
                            <a href="<?= base_url() ?>"
                                class="flex items-center px-4 py-2.5 rounded-md bg-lightprimary text-primary font-medium hover:bg-lightprimary hover:text-primary transition-colors">
                                <iconify-icon icon="tabler:aperture" class="text-xl mr-3"></iconify-icon>
                                <span>Dashboard</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Section: Utilities -->
                <div class="mb-2">
                    <h5 class="px-4 text-xs font-bold text-gray-400 uppercase mb-3 mt-4">Utilities</h5>
                    <ul>
                        <li>
                            <a href="<?= base_url('utilities/vendors') ?>"
                                class="flex items-center px-4 py-2.5 rounded-md text-gray-600 dark:text-gray-300 hover:bg-lightprimary hover:text-primary transition-colors">
                                <iconify-icon icon="tabler:table" class="text-xl mr-3"></iconify-icon>
                                <span>Vendors</span>
                            </a>
                        </li>
                         <li>
                            <a href="<?= base_url('utilities/transactions') ?>"
                                class="flex items-center px-4 py-2.5 rounded-md text-gray-600 dark:text-gray-300 hover:bg-lightprimary hover:text-primary transition-colors">
                                <iconify-icon icon="tabler:table" class="text-xl mr-3"></iconify-icon>
                                <span>Transactions</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('utilities/form') ?>"
                                class="flex items-center px-4 py-2.5 rounded-md text-gray-600 dark:text-gray-300 hover:bg-lightprimary hover:text-primary transition-colors">
                                <iconify-icon icon="tabler:brand-terraform" class="text-xl mr-3"></iconify-icon>
                                <span>Bank Details</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Section: Extra -->
                <div class="mb-2">
                    <h5 class="px-4 text-xs font-bold text-gray-400 uppercase mb-3 mt-4">Extra</h5>
                    <ul>
                        <li>
                            <a href="<?= base_url('user-profile') ?>"
                                class="flex items-center px-4 py-2.5 rounded-md text-gray-600 dark:text-gray-300 hover:bg-lightprimary hover:text-primary transition-colors">
                                <iconify-icon icon="tabler:user-circle" class="text-xl mr-3"></iconify-icon>
                                <span>Profile</span>
                            </a>
                        </li>
                       
                        <li>
                            <a href="<?= base_url('auth/logout') ?>"
                                class="flex items-center px-4 py-2.5 rounded-md text-gray-600 dark:text-gray-300 hover:bg-lightprimary hover:text-primary transition-colors">
                                <iconify-icon icon="tabler:logout" class="text-xl mr-3"></iconify-icon>
                                <span>Logout</span>
                            </a>
                        </li>
                    </ul>
                </div>

               </div>
        </aside>

        <!-- Backdrop -->
        <div class="bg-gray-900 bg-opacity-50 dark:bg-opacity-80 fixed inset-0 z-20 xl:hidden hidden"
            id="sidebarBackdrop"
            onclick="document.getElementById('sidebar').classList.add('-translate-x-full'); this.classList.add('hidden')">
        </div>

        <!-- Main Wrapper -->
        <div class="flex-1 xl:ml-64 w-full bg-white dark:bg-dark min-h-screen transition-all">

            <!-- Header -->
            <header
                class="sticky top-0 z-20 bg-white/90 dark:bg-dark/90 backdrop-blur-sm shadow-sm px-6 py-3 flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <button
                        class="xl:hidden p-2 text-gray-600 hover:text-primary rounded-full hover:bg-lightprimary transition-colors"
                        onclick="document.getElementById('sidebar').classList.remove('-translate-x-full'); document.getElementById('sidebarBackdrop').classList.remove('hidden')">
                        <iconify-icon icon="tabler:menu-2" width="20"></iconify-icon>
                    </button>
                    <!-- Connect Wallet Button -->
                    <button id="connect-wallet-btn" onclick="connectWallet()" 
                        class="hidden md:flex items-center gap-2 bg-primary hover:bg-yellow-500 text-white font-medium py-2 px-4 rounded-lg transition-colors text-sm">
                        <iconify-icon icon="tabler:wallet" width="18"></iconify-icon>
                        Connect Wallet
                    </button>
                </div>

                <div class="flex items-center gap-4">
                    <!-- Notifications -->
                    <button
                        class="text-gray-600 hover:text-primary relative p-2 rounded-full hover:bg-lightprimary transition-colors">
                        <iconify-icon icon="tabler:bell" width="22"></iconify-icon>
                        <span class="absolute top-2 right-2 flex h-2 w-2">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                        </span>
                    </button>
                    

                    <!-- Profile -->
                    <div class="relative cursor-pointer">
                        <img src="<?= base_url('images') ?>/profile/user-1.jpg"
                            class="w-9 h-9 rounded-full object-cover border border-gray-200" alt="Profile"
                            onerror="this.src='https://via.placeholder.com/40'" />
                    </div>
                </div>
            </header>

            <!-- Body Content -->
            <main class="p-[30px] container mx-auto">

                <!-- Top Cards -->
                <div class="grid grid-cols-12 gap-6 mb-8">
                    <!-- Products Sold -->
                    <div class="col-span-12 md:col-span-6 lg:col-span-3">
                        <div
                            class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-border/50 relative overflow-hidden group hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h5 class="text-base font-bold text-dark dark:text-white mb-1">Ind6Token</h5>
                                    <span class="text-[13px] text-gray-500">Number of tokens get</span>
                                </div>
                                <span
                                    class="w-11 h-11 rounded-full bg-purple/10 flex items-center justify-center text-purple-600">
                                    <iconify-icon icon="icon-park-outline:sales-report" width="24"></iconify-icon>
                                </span>
                            </div>
                            <h5 class="text-2xl font-semibold text-dark dark:text-white" id="token-balance-display"><?= number_format($tokensSold) ?></h5>
                        </div>
                    </div>
                    <!-- Total Sales -->
                    <div class="col-span-12 md:col-span-6 lg:col-span-3">
                        <div
                            class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-border/50 relative overflow-hidden group hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h5 class="text-base font-bold text-dark dark:text-white mb-1">Total Transaction</h5>
                                    <span class="text-[13px] text-gray-500">Cumulative sales revenue</span>
                                </div>
                                <span
                                    class="w-11 h-11 rounded-full bg-lighterror flex items-center justify-center text-error">
                                    <iconify-icon icon="proicons:box" width="24"></iconify-icon>
                                </span>
                            </div>
                            <h5 class="text-2xl font-semibold text-dark dark:text-white">₹ <?= number_format($totalTransaction, 2) ?></h5>
                        </div>
                    </div>
                    <!-- Monthly Sales -->
                    <div class="col-span-12 md:col-span-6 lg:col-span-3">
                        <div
                            class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-border/50 relative overflow-hidden group hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h5 class="text-base font-bold text-dark dark:text-white mb-1">Monthly Transaction</h5>
                                    <span class="text-[13px] text-gray-500">Sales generated</span>
                                </div>
                                <span
                                    class="w-11 h-11 rounded-full bg-lightwarning flex items-center justify-center text-warning">
                                    <iconify-icon icon="material-symbols:inventory-2-outline" width="24"></iconify-icon>
                                </span>
                            </div>
                            <h5 class="text-2xl font-semibold text-dark dark:text-white">₹ <?= number_format($monthlyTransaction, 2) ?></h5>
                        </div>
                    </div>
                    <!-- Total Customers -->
                    <div class="col-span-12 md:col-span-6 lg:col-span-3">
                        <div
                            class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-border/50 relative overflow-hidden group hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h5 class="text-base font-bold text-dark dark:text-white mb-1">Total Payments</h5>
                                    <span class="text-[13px] text-gray-500">Payments received</span>
                                </div>
                                <span
                                    class="w-11 h-11 rounded-full bg-lightsuccess flex items-center justify-center text-success">
                                    <iconify-icon icon="ph:users-three-light" width="24"></iconify-icon>
                                </span>
                            </div>
                            <h5 class="text-2xl font-semibold text-dark dark:text-white"><?= number_format($totalPayments) ?></h5>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-12 gap-6">
                    <!-- Column 1 -->
                    <div class="col-span-12 lg:col-span-4 flex flex-col gap-6">
                        <!-- Yearly Breakup -->
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-border">
                            <h5 class="text-lg font-bold mb-4 text-dark dark:text-white">Yearly Breakup</h5>
                            <div class="flex items-center gap-4">
                                <div class="flex-1">
                                    <h4 class="text-2xl font-bold text-dark dark:text-white mb-2">₹ <?= number_format($totalTransaction, 0) ?></h4>
                                    <div class="flex items-center gap-2 mb-2">
                                        <?php if ($yearlyGrowth >= 0): ?>
                                        <span class="w-9 h-9 rounded-full bg-lightsuccess flex items-center justify-center text-success">
                                            <iconify-icon icon="tabler:arrow-up-left" width="20"></iconify-icon>
                                        </span>
                                        <?php else: ?>
                                        <span class="w-9 h-9 rounded-full bg-lighterror flex items-center justify-center text-error">
                                            <iconify-icon icon="tabler:arrow-down-right" width="20"></iconify-icon>
                                        </span>
                                        <?php endif; ?>
                                        <p class="text-dark dark:text-white text-sm font-semibold"><?= ($yearlyGrowth >= 0 ? '+' : '') . number_format($yearlyGrowth, 1) ?>% <span
                                                class="text-gray-400 font-normal">last year</span></p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="flex items-center gap-1">
                                            <span class="w-2 h-2 rounded-full bg-primary"></span>
                                            <span class="text-xs text-gray-500"><?= $currentYear ?></span>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <span class="w-2 h-2 rounded-full bg-lightprimary"></span>
                                            <span class="text-xs text-gray-500"><?= $previousYear ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div id="yearly-breakup-chart"></div>
                            </div>
                        </div>

                        <!-- Monthly Earnings -->
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-border">
                            <h5 class="text-lg font-bold mb-1 text-dark dark:text-white">Monthly Payment</h5>
                            <div class="flex items-end justify-between">
                                <div>
                                    <h4 class="text-2xl font-bold text-dark dark:text-white mb-2">₹ <?= number_format($monthlyTransaction, 0) ?></h4>
                                    <div class="flex items-center gap-2 mb-2">
                                        <?php if ($monthlyGrowth >= 0): ?>
                                        <span class="w-9 h-9 rounded-full bg-lightsuccess flex items-center justify-center text-success">
                                            <iconify-icon icon="tabler:arrow-up-left" width="20"></iconify-icon>
                                        </span>
                                        <?php else: ?>
                                        <span class="w-9 h-9 rounded-full bg-lighterror flex items-center justify-center text-error">
                                            <iconify-icon icon="tabler:arrow-down-right" width="20"></iconify-icon>
                                        </span>
                                        <?php endif; ?>
                                        <p class="text-dark dark:text-white text-sm font-semibold"><?= ($monthlyGrowth >= 0 ? '+' : '') . number_format($monthlyGrowth, 1) ?>% <span
                                                class="text-gray-400 font-normal">last year</span></p>
                                    </div>
                                </div>
                            </div>
                            <div id="monthly-earnings-chart" class="mt-4"></div>
                        </div>
                    </div>

                    <!-- Sales Overview -->
                    <div class="col-span-12 lg:col-span-8">
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-border h-full">
                            <div class="flex justify-between items-center mb-4">
                                <h5 class="text-lg font-bold text-dark dark:text-white">Sales Overview</h5>
                                <div class="flex items-center">
                                    <select
                                        class="form-select bg-transparent dark:bg-transparent border-none text-gray-500 dark:text-gray-400 text-sm focus:ring-0 cursor-pointer">
                                        <option><?= date('F Y') ?></option>
                                        <option><?= date('F Y', strtotime("-1 month")) ?></option>
                                        <option><?= date('F Y', strtotime("-2 month")) ?></option>
                                    </select>
                                </div>
                            </div>
                            <div id="sales-overview-chart"></div>
                        </div>
                    </div>
                </div>

                <!-- Payment Gateway Integration -->
                <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-border p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h5 class="text-lg font-bold text-dark dark:text-white">Payment Gateway Integration</h5>
                            <p class="text-gray-500 text-sm mt-1">
                                Integrate payment functionality into your personal mobile app. 
                                The system uses your <a href="<?= base_url('utilities/form') ?>" class="text-primary hover:underline">Bank Details</a> (QR Code) for receiving payments.
                            </p>
                        </div>
                        <div class="hidden sm:block">
                            <a href="<?= base_url('docs/payment-api') ?>" class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded border border-blue-400 hover:bg-blue-200 transition-colors">Developer API Docs &rarr;</a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- API Credentials -->
                        <div class="bg-gray-50 dark:bg-gray-700/30 p-5 rounded-lg border border-gray-100 dark:border-gray-700">
                            <h6 class="font-semibold text-dark dark:text-white mb-4 flex items-center gap-2">
                                <iconify-icon icon="tabler:settings-code" class="text-primary"></iconify-icon> API Configuration
                            </h6>
                            
                            <div class="mb-4">
                                <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Base URL Endpoint</label>
                                <div class="flex items-center gap-2">
                                    <code class="block w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded px-3 py-2 text-sm text-gray-600 dark:text-gray-300 font-mono truncate select-all" id="api-base-url"><?= base_url('api/payment') ?></code>
                                    <button onclick="copyToClipboard(document.getElementById('api-base-url').innerText)" class="p-2 text-gray-400 hover:text-primary transition-colors" title="Copy URL">
                                        <iconify-icon icon="tabler:copy" width="18"></iconify-icon>
                                    </button>
                                </div>
                            </div>

                            <div class="mb-2">
                                <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Vendor ID</label>
                                <div class="flex items-center gap-2">
                                    <code class="bg-lightprimary text-primary font-bold px-3 py-1 rounded text-lg select-all"><?= session()->get('id') ?></code>
                                </div>
                            </div>
                            
                            <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 text-blue-800 dark:text-blue-200 text-xs rounded border border-blue-100 dark:border-blue-800/30">
                                <p class="flex gap-2">
                                    <iconify-icon icon="tabler:info-circle" width="16" class="shrink-0 mt-0.5"></iconify-icon> 
                                    <span>Pass this <strong>Vendor ID</strong> in the <code>initiate</code> request to link payments to your account.</span>
                                </p>
                            </div>
                        </div>

                        <!-- Documentation / Usage -->
                        <div class="bg-gray-900 text-gray-300 p-5 rounded-lg font-mono text-xs overflow-hidden flex flex-col h-full">
                            <div class="flex justify-between items-center mb-3">
                                <h6 class="font-semibold text-gray-100 flex items-center gap-2">
                                    <iconify-icon icon="tabler:brand-javascript" class="text-yellow-400"></iconify-icon> JavaScript SDK
                                </h6>
                                <button onclick="copyToClipboard(document.getElementById('js-sdk-code').innerText)" class="text-gray-400 hover:text-white text-[10px] uppercase border border-gray-600 px-2 py-1 rounded">
                                    Copy Code
                                </button>
                            </div>
                            
                            <div class="overflow-y-auto custom-scrollbar flex-1">
                                <pre class="text-blue-300 bg-gray-950 p-3 rounded border border-gray-800 whitespace-pre-wrap break-all" id="js-sdk-code">
class PaymentService {
    constructor() {
        this.baseUrl = '<?= base_url('api/payment') ?>';
        this.vendorId = '<?= session()->get('id') ?>'; // Your Vendor ID
    }

    // 1. Initiate Payment
    async initiate(amount) {
        try {
            const res = await fetch(`${this.baseUrl}/initiate`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    vendor_id: this.vendorId, 
                    amount: amount 
                })
            });
            const result = await res.json();
            if (!res.ok) throw new Error(result.messages?.error || 'Initiation failed');
            return result;
        } catch (err) {
            console.error(err);
            throw err;
        }
    }

    // 2. Update Status (after success)
    async updateStatus(txnId, utr) {
        try {
            const res = await fetch(`${this.baseUrl}/update`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    transaction_id: txnId, 
                    status: 'success', 
                    utr: utr 
                })
            });
            const result = await res.json();
             if (!res.ok) throw new Error(result.messages?.error || 'Update failed');
            return result;
        } catch (err) {
            console.error(err);
            throw err;
        }
    }
}

// Usage:
// const api = new PaymentService();
// api.initiate(500).then(data => console.log(data));
</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Payments -->
                <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-border overflow-hidden">
                    <div class="p-6">
                        <h5 class="text-lg font-bold text-dark dark:text-white">Recent Payments</h5>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left align-middle text-gray-500">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th class="py-3 px-6 font-semibold">Id</th>
                                    <th class="py-3 px-6 font-semibold">Amount</th>
                                    <th class="py-3 px-6 font-semibold">Method</th>
                                    <th class="py-3 px-6 font-semibold">Status</th>
                                    <th class="py-3 px-6 font-semibold">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <?php if (!empty($recentPayments)): ?>
                                    <?php foreach ($recentPayments as $payment): ?>
                                    <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap dark:text-white"><?= $payment['id'] ?></td>
                                        <td class="py-4 px-6 font-semibold text-dark dark:text-white">₹ <?= number_format($payment['amount'], 2) ?></td>
                                        <td class="py-4 px-6 text-gray-500"><?= $payment['method'] ?></td>
                                        <td class="py-4 px-6">
                                            <?php 
                                            $statusClass = match ($payment['status']) {
                                                'success' => 'bg-green-100 text-green-800',
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'failed'  => 'bg-red-100 text-red-800',
                                                default   => 'bg-gray-100 text-gray-800'
                                            };
                                            ?>
                                            <span class="<?= $statusClass ?> text-xs font-medium px-2.5 py-0.5 rounded"><?= ucfirst($payment['status']) ?></span>
                                        </td>
                                        <td class="py-4 px-6 text-gray-500"><?= date('M d, Y H:i', strtotime($payment['created_at'])) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr class="bg-white dark:bg-gray-800">
                                        <td colspan="5" class="py-4 px-6 text-center text-gray-500">No payments found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>


            </main>
        </div>
    </div>

    <!-- Scripts (ApexCharts for Charts) -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.js"></script>
    <!-- Ethers.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ethers/5.7.2/ethers.umd.min.js" referrerpolicy="no-referrer"></script>
    <script>
        // --- Sales Overview Chart ---
        var monthlyEarnings = <?= json_encode($monthlyEarnings) ?>;
        var optionsSales = {
            series: [
                { name: 'Earnings ', data: monthlyEarnings },
            ],
            chart: {
                type: 'bar',
                height: 350,
                fontFamily: "'Plus Jakarta Sans', sans-serif",
                toolbar: { show: false },
                foreColor: '#adb0bb'
            },
            colors: ['#EBBE31'], // Primary color
            plotOptions: {
                bar: { horizontal: false, columnWidth: '40%', borderRadius: 3 }
            },
            dataLabels: { enabled: false },
            legend: { show: false },
            grid: {
                borderColor: 'rgba(0,0,0,0.1)',
                strokeDashArray: 3,
                xaxis: { lines: { show: false } }
            },
            stroke: { show: true, width: 2, colors: ['transparent'] },
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                axisBorder: { show: false },
                axisTicks: { show: false },
            },
            yaxis: { title: { text: 'Amount' } },
            fill: { opacity: 1 },
            tooltip: { theme: 'dark' }
        };
        var chartSales = new ApexCharts(document.querySelector("#sales-overview-chart"), optionsSales);
        chartSales.render();

        // --- Yearly Breakup Chart (Current Year vs Previous Year Sales) ---
        var yearlySales = <?= json_encode($yearlySales) ?>; // [Current Year Sales, Previous Year Sales]
        var currentYear = <?= $currentYear ?>;
        var previousYear = <?= $previousYear ?>;
        
        var optionsBreakup = {
            series: yearlySales,
            labels: [currentYear.toString(), previousYear.toString()],
            chart: { width: 180, type: 'donut', fontFamily: "'Plus Jakarta Sans', sans-serif", },
            plotOptions: {
                pie: {
                    startAngle: 0,
                    endAngle: 360,
                    donut: { size: '75%' }
                }
            },
            stroke: { show: false },
            dataLabels: { enabled: false },
            legend: { show: false },
            colors: ['#EBBE31', '#FCF5D9'], // Primary, LightPrimary
            responsive: [{ breakpoint: 991, options: { chart: { width: 150 } } }],
            tooltip: { theme: 'dark', y: { formatter: function (val) { return "₹ " + val } } }
        };
        var chartBreakup = new ApexCharts(document.querySelector("#yearly-breakup-chart"), optionsBreakup);
        chartBreakup.render();

        // --- Monthly Earnings Chart ---
        var optionsEarning = {
            series: [{ name: "Earnings", data: [25, 66, 20, 40, 12, 58, 20] }],
            chart: {
                type: 'area',
                height: 60,
                sparkline: { enabled: true },
                fontFamily: "'Plus Jakarta Sans', sans-serif",
            },
            stroke: { curve: 'smooth', width: 2 },
            fill: {
                colors: ['#FCF5D9'],
                type: 'solid',
                opacity: 0.05
            },
            markers: { size: 0 },
            tooltip: {
                theme: 'dark',
                fixed: { enabled: false, position: 'right' },
                x: { show: false }
            },
            colors: ['#EBBE31'],
        };
        var chartEarning = new ApexCharts(document.querySelector("#monthly-earnings-chart"), optionsEarning);
        chartEarning.render();
        
        // --- Wallet Connection Logic ---
        const tokenContractAddress = 'YOUR_TOKEN_CONTRACT_ADDRESS'; // REPLACE THIS WITH ACTUAL CONTRACT ADDRESS
        const tokenABI = [
            "function balanceOf(address owner) view returns (uint256)",
            "function decimals() view returns (uint8)",
            "function symbol() view returns (string)"
        ];

        async function connectWallet() {
            if (typeof window.ethereum !== 'undefined') {
                try {
                    const provider = new ethers.providers.Web3Provider(window.ethereum);
                    await provider.send("eth_requestAccounts", []);
                    const signer = provider.getSigner();
                    const address = await signer.getAddress();
                    
                    document.getElementById('connect-wallet-btn').innerHTML = '<iconify-icon icon="tabler:wallet" width="18"></iconify-icon> ' + address.substring(0, 6) + '...' + address.substring(address.length - 4);
                    document.getElementById('connect-wallet-btn').onclick = null; // Disable click after connect

                    // Fetch Token Balance
                    // Only try if contract address is set (not the placeholder)
                    if (tokenContractAddress !== 'YOUR_TOKEN_CONTRACT_ADDRESS') {
                         try {
                            const contract = new ethers.Contract(tokenContractAddress, tokenABI, provider);
                            const balance = await contract.balanceOf(address);
                            const decimals = await contract.decimals();
                            const formattedBalance = ethers.utils.formatUnits(balance, decimals);
                            
                            document.getElementById('token-balance-display').innerText = parseFloat(formattedBalance).toLocaleString();
                        } catch (err) {
                            console.error("Error fetching token balance:", err);
                             document.getElementById('token-balance-display').innerText = "Err";
                        }
                    } else {
                         console.warn("Please set the Token Contract Address in the script.");
                         // For demo purpose, show a fake balance if placeholder is there so user sees 'something' changed
                         // Remove this else block in production
                         document.getElementById('token-balance-display').innerText = "0.00 (Set Contract)";
                    }

                } catch (error) {
                    console.error("User rejected request", error);
                }
            } else {
                alert("Please install MetaMask or another Web3 wallet!");
            }
        }

        // Copy to clipboard helper
        function copyToClipboard(text) {
            if (!navigator.clipboard) {
                // Fallback for older browsers
                var textArea = document.createElement("textarea");
                textArea.value = text;
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                try {
                    document.execCommand('copy');
                    alert("Copied to clipboard!");
                } catch (err) {
                    console.error('Fallback: Oops, unable to copy', err);
                }
                document.body.removeChild(textArea);
                return;
            }
            navigator.clipboard.writeText(text).then(() => {
                alert("Copied to clipboard!");
            }).catch(err => {
                console.error('Failed to copy: ', err);
            });
        }

        // --- Vendor Payment Gateway API ---
        /**
         * API for Vendor Payment Integration.
         * Usage: 
         *  - To initiate a payment: await window.vendorPaymentApi.initiatePayment(amount);
         *  - To update status: await window.vendorPaymentApi.updateStatus(txnId, 'success');
         */
        class VendorPaymentGateway {
            constructor() {
                this.apiBase = '<?= base_url('api/payment') ?>';
            }

            // Initiate a payment transaction
            // Returns: { status: 'initiated', transaction_id: '...', bank_details: {...} }
            async initiatePayment(amount, vendorId = null) {
                try {
                    const data = { amount: amount };
                    if (vendorId) data.vendor_id = vendorId;

                    const response = await fetch(`${this.apiBase}/initiate`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify(data)
                    });
                    
                    const result = await response.json();
                    if (!response.ok) throw new Error(result.error || result.message || 'Unknown error');
                    
                    console.log('Payment Initiated:', result);
                    return result; 
                } catch (error) {
                    console.error('Payment Initiation Error:', error);
                    // alert('Failed to initiate payment: ' + error.message);
                    throw error;
                }
            }

            // Update payment status (e.g. 'success', 'failed')
            async updateStatus(txnId, status) {
                try {
                    const response = await fetch(`${this.apiBase}/update`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ transaction_id: txnId, status: status })
                    });

                    const result = await response.json();
                    if (!response.ok) throw new Error(result.error || result.message || 'Unknown error');

                    console.log('Status Updated:', result);
                    return result;
                } catch (error) {
                    console.error('Update Status Error:', error);
                    throw error;
                }
            }
        }

        // Global instance
        window.vendorPaymentApi = new VendorPaymentGateway();
    </script>
</body>

</html>
