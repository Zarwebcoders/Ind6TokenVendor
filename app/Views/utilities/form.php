<?= view('partials/head', ['title' => 'Bank Details']) ?>

<body class="bg-light dark:bg-dark text-textprimary">
    <div class="flex w-full min-h-screen">
        <?= view('partials/sidebar') ?>

        <!-- Main Wrapper -->
        <div class="flex-1 xl:ml-64 w-full bg-white dark:bg-dark min-h-screen transition-all">
            <?= view('partials/header') ?>

            <!-- Body Content -->
            <main class="p-[30px] container mx-auto">
                <div class="header mb-8">
                    <h1 class="text-2xl font-bold">Banking Configuration</h1>
                    <p class="text-gray-500">Configure your bank account and UPI details for receiving payments.</p>
                </div>

                <div class="rounded-xl shadow-sm bg-white dark:bg-gray-800 p-8 border border-border">
                    <h5 class="text-lg font-bold mb-6 text-dark dark:text-white border-b border-border pb-4">Bank
                        Details Form</h5>

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

                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="p-4 mb-6 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 border border-red-200"
                            role="alert">
                            <ul class="list-disc list-inside">
                                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('bank/save') ?>" method="post" enctype="multipart/form-data">
                        <!-- Hidden ID for Update -->
                        <?php if (isset($bank['id'])): ?>
                            <input type="hidden" name="id" value="<?= $bank['id'] ?>">
                        <?php endif; ?>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Col 1 -->
                            <div class="space-y-5">
                                <div>
                                    <label for="account_holder"
                                        class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">Account
                                        Holder Name</label>
                                    <input id="account_holder" name="account_holder" type="text"
                                        placeholder="e.g. John Doe" required
                                        value="<?= old('account_holder', $bank['account_holder'] ?? '') ?>"
                                        class="bg-gray-50 border border-border text-gray-900 text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-3 dark:bg-gray-900 dark:border-gray-700 dark:placeholder-gray-400 dark:text-white outline-none transition-all focus:bg-white dark:focus:bg-gray-950">
                                </div>
                                <div>
                                    <label for="account_number"
                                        class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">Account
                                        Number</label>
                                    <input id="account_number" name="account_number" type="text"
                                        placeholder="e.g. 1234567890" required
                                        value="<?= old('account_number', $bank['account_number'] ?? '') ?>"
                                        class="bg-gray-50 border border-border text-gray-900 text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-3 dark:bg-gray-900 dark:border-gray-700 dark:placeholder-gray-400 dark:text-white outline-none transition-all focus:bg-white dark:focus:bg-gray-950">
                                </div>
                                <div>
                                    <label for="ifsc"
                                        class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">IFSC
                                        Code</label>
                                    <input id="ifsc" name="ifsc" type="text" placeholder="e.g. SBIN0001234" required
                                        value="<?= old('ifsc', $bank['ifsc'] ?? '') ?>"
                                        class="bg-gray-50 border border-border text-gray-900 text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-3 dark:bg-gray-900 dark:border-gray-700 dark:placeholder-gray-400 dark:text-white outline-none transition-all focus:bg-white dark:focus:bg-gray-950">
                                </div>
                            </div>
                            <!-- Col 2 -->
                            <div class="space-y-5">
                                <div>
                                    <label for="bank_name"
                                        class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">Bank
                                        Name</label>
                                    <input id="bank_name" name="bank_name" type="text"
                                        placeholder="e.g. State Bank of India" required
                                        value="<?= old('bank_name', $bank['bank_name'] ?? '') ?>"
                                        class="bg-gray-50 border border-border text-gray-900 text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-3 dark:bg-gray-900 dark:border-gray-700 dark:placeholder-gray-400 dark:text-white outline-none transition-all focus:bg-white dark:focus:bg-gray-950">
                                </div>
                                <div>
                                    <label for="upi_id"
                                        class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">UPI ID
                                        <span class="text-gray-400 font-normal">(Optional)</span></label>
                                    <input id="upi_id" name="upi_id" type="text" placeholder="e.g. username@upi"
                                        value="<?= old('upi_id', $bank['upi_id'] ?? '') ?>"
                                        class="bg-gray-50 border border-border text-gray-900 text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-3 dark:bg-gray-900 dark:border-gray-700 dark:placeholder-gray-400 dark:text-white outline-none transition-all focus:bg-white dark:focus:bg-gray-950">
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300"
                                        for="upi_qr">UPI QR Image <span
                                            class="text-gray-400 font-normal">(Optional)</span></label>

                                    <div class="flex items-start gap-4">
                                        <div class="flex-1">
                                            <input
                                                class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 p-2"
                                                id="upi_qr" name="upi_qr" type="file">
                                            <p class="mt-1 text-xs text-gray-500">SVG, PNG, JPG or GIF (MAX. 800x400px).
                                            </p>
                                        </div>
                                        <?php if (isset($bank['upi_qr']) && $bank['upi_qr']): ?>
                                            <div class="shrink-0 text-center">
                                                <p class="text-[10px] text-gray-500 mb-1 uppercase font-bold">Current QR</p>
                                                <img src="<?= base_url($bank['upi_qr']) ?>" alt="UPI QR"
                                                    class="h-16 w-16 object-cover border border-border rounded-lg shadow-sm">
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="flex gap-3 mt-8 pt-6 border-t border-border">
                            <button type="submit"
                                class="flex items-center gap-2 bg-primary hover:bg-yellow-500 text-dark font-bold rounded-lg text-sm px-6 py-3 transition-all shadow-lg active:scale-[0.98]">
                                <iconify-icon icon="tabler:device-floppy" width="20"></iconify-icon>
                                <?= isset($bank['id']) ? 'Update Bank Details' : 'Save Bank Details' ?>
                            </button>
                            <button type="reset"
                                class="flex items-center gap-2 bg-white dark:bg-gray-700 border border-border hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-medium rounded-lg text-sm px-6 py-3 transition-all">
                                <iconify-icon icon="tabler:rotate-clockwise" width="20"></iconify-icon>
                                Reset
                            </button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.js"></script>
</body>

</html>