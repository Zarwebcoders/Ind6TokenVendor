<?= view('partials/head', ['title' => 'Dashboard']) ?>

<body class="bg-light dark:bg-dark text-textprimary">
    <div class="flex w-full min-h-screen">
        <?= view('partials/sidebar') ?>

        <!-- Main Wrapper -->
        <div class="flex-1 xl:ml-64 w-full bg-white dark:bg-dark min-h-screen transition-all">
            <?= view('partials/header') ?>

            <!-- Body Content -->
            <main class="p-[30px] container mx-auto">

                <!-- Top Cards -->
                <div class="grid grid-cols-12 gap-6 mb-8">
                    <!-- Products Sold -->
                    <div class="col-span-12 md:col-span-6 lg:col-span-3">
                        <div
                            class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-border relative overflow-hidden group hover:shadow-md transition-shadow">
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
                            <h5 class="text-2xl font-semibold text-dark dark:text-white" id="token-balance-display">
                                <?= number_format($tokensSold) ?></h5>
                        </div>
                    </div>
                    <!-- Total Sales -->
                    <div class="col-span-12 md:col-span-6 lg:col-span-3">
                        <div
                            class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-border relative overflow-hidden group hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h5 class="text-base font-bold text-dark dark:text-white mb-1">Total Transaction
                                    </h5>
                                    <span class="text-[13px] text-gray-500">Cumulative sales revenue</span>
                                </div>
                                <span
                                    class="w-11 h-11 rounded-full bg-lighterror flex items-center justify-center text-error">
                                    <iconify-icon icon="proicons:box" width="24"></iconify-icon>
                                </span>
                            </div>
                            <h5 class="text-2xl font-semibold text-dark dark:text-white">₹
                                <?= number_format($totalTransaction, 2) ?></h5>
                        </div>
                    </div>
                    <!-- Monthly Sales -->
                    <div class="col-span-12 md:col-span-6 lg:col-span-3">
                        <div
                            class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-border relative overflow-hidden group hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h5 class="text-base font-bold text-dark dark:text-white mb-1">Monthly Transaction
                                    </h5>
                                    <span class="text-[13px] text-gray-500">Sales generated</span>
                                </div>
                                <span
                                    class="w-11 h-11 rounded-full bg-lightwarning flex items-center justify-center text-warning">
                                    <iconify-icon icon="material-symbols:inventory-2-outline" width="24"></iconify-icon>
                                </span>
                            </div>
                            <h5 class="text-2xl font-semibold text-dark dark:text-white">₹
                                <?= number_format($monthlyTransaction, 2) ?></h5>
                        </div>
                    </div>
                    <!-- Total Customers -->
                    <div class="col-span-12 md:col-span-6 lg:col-span-3">
                        <div
                            class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-border relative overflow-hidden group hover:shadow-md transition-shadow">
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
                            <h5 class="text-2xl font-semibold text-dark dark:text-white">
                                <?= number_format($totalPayments) ?></h5>
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
                                    <h4 class="text-2xl font-bold text-dark dark:text-white mb-2">₹
                                        <?= number_format($totalTransaction, 0) ?></h4>
                                    <div class="flex items-center gap-2 mb-2">
                                        <?php if ($yearlyGrowth >= 0): ?>
                                            <span
                                                class="w-9 h-9 rounded-full bg-lightsuccess flex items-center justify-center text-success">
                                                <iconify-icon icon="tabler:arrow-up-left" width="20"></iconify-icon>
                                            </span>
                                        <?php else: ?>
                                            <span
                                                class="w-9 h-9 rounded-full bg-lighterror flex items-center justify-center text-error">
                                                <iconify-icon icon="tabler:arrow-down-right" width="20"></iconify-icon>
                                            </span>
                                        <?php endif; ?>
                                        <p class="text-dark dark:text-white text-sm font-semibold">
                                            <?= ($yearlyGrowth >= 0 ? '+' : '') . number_format($yearlyGrowth, 1) ?>%
                                            <span class="text-gray-400 font-normal">last year</span></p>
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
                                    <h4 class="text-2xl font-bold text-dark dark:text-white mb-2">₹
                                        <?= number_format($monthlyTransaction, 0) ?></h4>
                                    <div class="flex items-center gap-2 mb-2">
                                        <?php if ($monthlyGrowth >= 0): ?>
                                            <span
                                                class="w-9 h-9 rounded-full bg-lightsuccess flex items-center justify-center text-success">
                                                <iconify-icon icon="tabler:arrow-up-left" width="20"></iconify-icon>
                                            </span>
                                        <?php else: ?>
                                            <span
                                                class="w-9 h-9 rounded-full bg-lighterror flex items-center justify-center text-error">
                                                <iconify-icon icon="tabler:arrow-down-right" width="20"></iconify-icon>
                                            </span>
                                        <?php endif; ?>
                                        <p class="text-dark dark:text-white text-sm font-semibold">
                                            <?= ($monthlyGrowth >= 0 ? '+' : '') . number_format($monthlyGrowth, 1) ?>%
                                            <span class="text-gray-400 font-normal">last year</span></p>
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

                <!-- Recent Payments Table -->
                <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-border overflow-hidden">
                    <div class="p-6">
                        <h5 class="text-lg font-bold text-dark dark:text-white">Recent Payments</h5>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left align-middle text-gray-500 font-medium">
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
                                            <td class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                <?= $payment['id'] ?></td>
                                            <td class="py-4 px-6 font-semibold text-dark dark:text-white">₹
                                                <?= number_format($payment['amount'], 2) ?></td>
                                            <td class="py-4 px-6 text-gray-500 uppercase text-xs"><?= $payment['method'] ?></td>
                                            <td class="py-4 px-6">
                                                <?php
                                                $statusClass = match ($payment['status']) {
                                                    'success' => 'bg-green-100 text-green-800',
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'failed' => 'bg-red-100 text-red-800',
                                                    default => 'bg-gray-100 text-gray-800'
                                                };
                                                ?>
                                                <span
                                                    class="<?= $statusClass ?> text-[10px] font-bold px-2 py-1 rounded uppercase"><?= ucfirst($payment['status']) ?></span>
                                            </td>
                                            <td class="py-4 px-6 text-gray-400 text-xs">
                                                <?= date('M d, Y H:i', strtotime($payment['created_at'])) ?></td>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ethers/5.7.2/ethers.umd.min.js"
        referrerpolicy="no-referrer"></script>
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
    </script>
</body>

</html>