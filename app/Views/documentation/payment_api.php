<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment API Documentation - Ind6Token</title>
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
                        dark: '#111c2d',
                        light: '#f6f9fc',
                    }
                }
            }
        }
    </script>
    <script>
        // Updated SDK for Auto-Detection Support
        class PaymentService {
            constructor(baseUrl, vendorId) {
                this.baseUrl = baseUrl;
                this.vendorId = vendorId;
            }

            async initiate(amount) {
                try {
                    const res = await fetch(`${this.baseUrl}/initiate`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ vendor_id: this.vendorId, amount: amount })
                    });
                    const result = await res.json();
                    if (!res.ok) {
                        const errorMsg = result.messages?.error || result.message || 'Initiation failed';
                        throw new Error(errorMsg);
                    }
                    return result;
                } catch (err) {
                    console.error('API Error:', err);
                    throw err;
                }
            }

            async updateStatus(txnId, status, utr = null) {
                try {
                    const res = await fetch(`${this.baseUrl}/update`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ transaction_id: txnId, status: status, utr: utr })
                    });
                    const result = await res.json();
                    if (!res.ok) {
                         const errorMsg = result.messages?.error || result.message || 'Update failed';
                         throw new Error(errorMsg);
                    }
                    return result;
                } catch (err) {
                    console.error('API Error:', err);
                    throw err;
                }
            }
            
            // Helper for Apps to Launch Intent
            launchUpiIntent(intentUrl) {
                window.location.href = intentUrl; // Works on Mobile to open GPay/PhonePe
            }
        }
    </script>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Iconify -->
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        pre { white-space: pre-wrap; word-wrap: break-word; }
    </style>
</head>

<body class="bg-light dark:bg-dark text-gray-800 dark:text-gray-200">

    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <div class="flex items-center gap-2">
                        <iconify-icon icon="tabler:api" class="text-primary text-2xl"></iconify-icon>
                        <span class="font-bold text-lg text-dark dark:text-white">API Docs</span>
                    </div>
                    <?php if(session()->get('isLoggedIn')): ?>
                    <a href="<?= base_url('/') ?>" class="text-sm font-medium hover:text-primary transition-colors flex items-center gap-1">
                        <iconify-icon icon="tabler:arrow-left"></iconify-icon> Back to Dashboard
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </header>

        <!-- Content -->
        <main class="flex-grow py-10 px-4 sm:px-6">
            <div class="max-w-4xl mx-auto">
                
                <div class="mb-10 text-center">
                    <h1 class="text-3xl font-bold text-dark dark:text-white mb-4">Payment Gateway Integration</h1>
                    <p class="text-gray-500 dark:text-gray-400 max-w-2xl mx-auto">
                        This documentation guides you through integrating the Ind6Token Payment Gateway into your JavaScript-based mobile or web application.
                    </p>
                </div>

                <div class="grid gap-8">
                    
                    <!-- 1. Prerequisites -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h2 class="text-xl font-bold mb-4 flex items-center gap-2 text-dark dark:text-white">
                            <span class="bg-primary/10 text-primary w-8 h-8 rounded-full flex items-center justify-center text-sm">1</span>
                            Prerequisites
                        </h2>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Before starting, ensure you have the following from your generic Vendor Dashboard:</p>
                        <ul class="space-y-3 mb-6">
                            <li class="flex items-start gap-3">
                                <iconify-icon icon="tabler:check" class="text-green-500 mt-1"></iconify-icon>
                                <div>
                                    <strong class="block text-dark dark:text-white">Vendor ID</strong>
                                    <span class="text-sm text-gray-500">Your unique identifier (e.g., <?= session()->get('id') ?? '123' ?>).</span>
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <iconify-icon icon="tabler:check" class="text-green-500 mt-1"></iconify-icon>
                                <div>
                                    <strong class="block text-dark dark:text-white">Bank Details</strong>
                                    <span class="text-sm text-gray-500">Ensure your UPI/Bank details are configured in the <a href="<?= base_url('utilities/form') ?>" class="text-primary hover:underline">Utilities Section</a>.</span>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- 2. Integration Workflow -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h2 class="text-xl font-bold mb-4 flex items-center gap-2 text-dark dark:text-white">
                            <span class="bg-primary/10 text-primary w-8 h-8 rounded-full flex items-center justify-center text-sm">2</span>
                            Integration Workflow
                        </h2>
                        
                        <div class="relative border-l-2 border-gray-200 dark:border-gray-700 ml-3 space-y-10 pb-2">
                            <!-- Step A -->
                            <div class="ml-6">
                                <span class="absolute -left-[9px] mt-1 w-4 h-4 rounded-full bg-gray-200 dark:bg-gray-700 border-2 border-primary"></span>
                                <h3 class="text-lg font-semibold text-dark dark:text-white mb-1">1. Initiate & Get Intent</h3>
                                <p class="text-gray-600 dark:text-gray-400 text-sm mb-3">Call the API. It returns a `upi_intent` link (e.g. `upi://pay?...`).</p>
                            </div>

                            <!-- Step B -->
                            <div class="ml-6">
                                <span class="absolute -left-[9px] mt-1 w-4 h-4 rounded-full bg-gray-200 dark:bg-gray-700 border-2 border-primary"></span>
                                <h3 class="text-lg font-semibold text-dark dark:text-white mb-1">2. Auto-Detect Payment</h3>
                                <p class="text-gray-600 dark:text-gray-400 text-sm mb-3">
                                    Use the **UPI Intent** link to open the user's payment app directly (GPay/PhonePe). 
                                    <br>When the user returns to your app, your app knows if the payment was `Success`.
                                    <br><em>(No screenshot needed)</em>
                                </p>
                            </div>

                            <!-- Step C -->
                            <div class="ml-6">
                                <span class="absolute -left-[9px] mt-1 w-4 h-4 rounded-full bg-gray-200 dark:bg-gray-700 border-2 border-primary"></span>
                                <h3 class="text-lg font-semibold text-dark dark:text-white mb-1">3. Auto-Save to Server</h3>
                                <p class="text-gray-600 dark:text-gray-400 text-sm mb-3">
                                    Your app automatically calls the `update` API with `status: success`. 
                                    <br>The transaction is instantly saved in the database.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Client Side SDK -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-xl font-bold flex items-center gap-2 text-dark dark:text-white">
                                <span class="bg-primary/10 text-primary w-8 h-8 rounded-full flex items-center justify-center text-sm">3</span>
                                JavaScript SDK
                            </h2>
                            <button onclick="copyCode()" class="bg-primary/10 hover:bg-primary/20 text-primary px-3 py-1 rounded text-sm font-medium transition-colors">
                                Copy Code
                            </button>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Copy and paste this class into your project to handle the entire flow.</p>

                        <div class="bg-gray-900 rounded-xl p-4 overflow-hidden relative group">
                            <pre class="font-mono text-sm text-blue-300 overflow-x-auto custom-scrollbar" id="sdk-code">
/**
 * PaymentService - Handles integration with Ind6Token Vendor Gateway
 */
class PaymentService {
    constructor() {
        // Base API URL
        this.baseUrl = '<?= base_url('api/payment') ?>';
        // Vendor ID (Keep this secure in your app config)
        this.vendorId = '<?= session()->get('id') ?? 'YOUR_VENDOR_ID' ?>';
    }

    /**
     * 1. Initiate a Payment
     * @param {number} amount - The amount to charge
     * @returns {Promise<Object>} - Contains transaction_id, bank_details, etc.
     */
    async initiate(amount) {
        try {
            console.log("Initiating payment of " + amount + "...");
            const response = await fetch(`${this.baseUrl}/initiate`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    vendor_id: this.vendorId,
                    amount: amount
                })
            });

            const result = await response.json();
            if (!response.ok) throw new Error(result.message || 'Initiation failed');
            
            return result; 
        } catch (error) {
            console.error('Payment Initiation Error:', error);
            throw error;
        }
    }

    /**
     * 2. Update Transaction Status
     * Call this after a successful payment on the client side.
     * @param {string} txnId - The Transaction ID returned from initiate()
     * @param {string} utr - The UTR / Reference Number from the bank/UPI response
     */
    async updateStatus(txnId, utr) {
        try {
            console.log("Updating status for TXN: " + txnId);
            const response = await fetch(`${this.baseUrl}/update`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    transaction_id: txnId,
                    status: 'success',
                    utr: utr
                })
            });

            const result = await response.json();
            if (!response.ok) throw new Error(result.message || 'Update failed');

            return result;
        } catch (error) {
            console.error('Payment Update Error:', error);
            throw error;
        }
    }
}

// --- Example Usage Flow ---
/*
(async () => {
    const api = new PaymentService('http://your-site/api/payment', 'VENDOR_123');
    
    try {
        // 1. Initiate 
        const initData = await api.initiate(500); 
        console.log("Transaction Created:", initData.transaction_id);

        // 2. Launch UPI Intent (Mobile Only)
        // This opens GPay/PhonePe directly.
        if (initData.upi_intent) {
            api.launchUpiIntent(initData.upi_intent);
        }

        // 3. Handle Return (Conceptual)
        // In a real mobile app, you would listen for the 'app resume' event
        // or the result of the intent to verify 'Success'.
        
        // Simulating usage: if app detects success
        const uir = '1234567890'; // Captured UTR or 'NA' if unavailable
        
        await api.updateStatus(initData.transaction_id, 'success', uir);
        console.log("Payment Saved Successfully!");

    } catch (e) {
        alert("Flow failed: " + e.message);
    }
})();
*/
                            </pre>
                        </div>
                    </div>

                </div>
            </div>
        </main>
        
        <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 py-6 text-center">
            <p class="text-gray-500 text-sm">© <?= date('Y') ?> Ind6Token Vendor System. All rights reserved.</p>
        </footer>

    </div>

    <script>
        function copyCode() {
            const codeText = document.getElementById('sdk-code').innerText;
            navigator.clipboard.writeText(codeText).then(() => {
                alert('SDK Code copied to clipboard!');
            }).catch(err => {
                console.error('Failed to copy', err);
                alert('Failed to copy code.');
            });
        }
    </script>
</body>
</html>
