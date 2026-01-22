<?= view('partials/head', ['title' => 'KYC Verification']) ?>

<!-- FilePond Styles -->
<link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
<link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">

<body class="bg-light dark:bg-dark text-textprimary">
    <div class="flex w-full min-h-screen">
        <?= view('partials/sidebar') ?>

        <!-- Main Wrapper -->
        <div class="flex-1 xl:ml-64 w-full bg-white dark:bg-dark min-h-screen transition-all">
            <?= view('partials/header') ?>

            <!-- Body Content -->
            <main class="p-[30px] container mx-auto">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h4 class="text-2xl font-bold text-dark dark:text-white">KYC Verification</h4>
                        <p class="text-sm text-gray-500">Submit your business documents to verify your account</p>
                    </div>
                    <?php
                    $statusColor = match ($vendor['kyc_status']) {
                        'verified' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                        'rejected' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                        default => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                    };
                    ?>
                    <span class="px-4 py-2 rounded-full text-xs font-bold uppercase <?= $statusColor ?>">
                        <?= ucfirst($vendor['kyc_status']) ?>
                    </span>
                </div>

                <?php if (session()->getFlashdata('success')): ?>
                    <div
                        class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 dark:bg-green-900/20 dark:text-green-400">
                        <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('errors')): ?>
                    <div
                        class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 dark:bg-red-900/20 dark:text-red-400">
                        <ul class="list-disc ml-4">
                            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                <li>
                                    <?= $error ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('vendor/kyc/update') ?>" method="POST" enctype="multipart/form-data">
                    <div class="grid grid-cols-12 gap-6">
                        <!-- KYC Profile Section -->
                        <div class="col-span-12 lg:col-span-8">
                            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-border">
                                <h5 class="text-lg font-bold text-dark dark:text-white mb-6 flex items-center gap-2">
                                    <iconify-icon icon="solar:user-id-bold-duotone"
                                        class="text-primary text-xl"></iconify-icon>
                                    KYC Profile
                                </h5>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="form-group">
                                        <label
                                            class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">PAN</label>
                                        <input type="text" name="pan_number"
                                            value="<?= old('pan_number', $vendor['pan_number'] ?? '') ?>"
                                            class="w-full px-4 py-3 rounded-lg border border-border bg-transparent focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all dark:text-white"
                                            placeholder="DUMMY0000R">
                                    </div>

                                    <div class="form-group">
                                        <label
                                            class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">GSTIN
                                            (GST Number)</label>
                                        <input type="text" name="gst_number"
                                            value="<?= old('gst_number', $vendor['gst_number'] ?? '') ?>"
                                            class="w-full px-4 py-3 rounded-lg border border-border bg-transparent focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all dark:text-white"
                                            placeholder="29DUMMY0000R1ZB">
                                    </div>

                                    <div class="form-group md:col-span-2">
                                        <label
                                            class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">HSN
                                            Code</label>
                                        <input type="text" name="hsn_code"
                                            value="<?= old('hsn_code', $vendor['hsn_code'] ?? '') ?>"
                                            class="w-full px-4 py-3 rounded-lg border border-border bg-transparent focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all dark:text-white"
                                            placeholder="111111">
                                    </div>

                                    <div class="form-group md:col-span-2">
                                        <label
                                            class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">Address</label>
                                        <textarea name="address" rows="3"
                                            class="w-full px-4 py-3 rounded-lg border border-border bg-transparent focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all dark:text-white"
                                            placeholder="Mumbai"><?= old('address', $vendor['address'] ?? '') ?></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label
                                            class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">City</label>
                                        <input type="text" name="city" value="<?= old('city', $vendor['city'] ?? '') ?>"
                                            class="w-full px-4 py-3 rounded-lg border border-border bg-transparent focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all dark:text-white"
                                            placeholder="Mumbai">
                                    </div>

                                    <div class="form-group">
                                        <label
                                            class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">Pincode</label>
                                        <input type="text" name="pincode"
                                            value="<?= old('pincode', $vendor['pincode'] ?? '') ?>"
                                            class="w-full px-4 py-3 rounded-lg border border-border bg-transparent focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all dark:text-white"
                                            placeholder="111111">
                                    </div>
                                </div>

                                <div class="mt-8">
                                    <button type="submit"
                                        class="bg-primary hover:bg-primary/90 text-white font-bold py-3 px-8 rounded-lg transition-all shadow-lg hover:shadow-primary/30">
                                        Save KYC Details
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- KYC Documents Section -->
                        <div class="col-span-12 lg:col-span-4">
                            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-border">
                                <h5 class="text-lg font-bold text-dark dark:text-white mb-6 flex items-center gap-2">
                                    <iconify-icon icon="solar:documents-minimalistic-bold-duotone"
                                        class="text-primary text-xl"></iconify-icon>
                                    KYC Documents
                                </h5>

                                <div class="space-y-6">
                                    <!-- PAN Upload -->
                                    <div>
                                        <label
                                            class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-3 uppercase tracking-wider">PAN
                                            Card</label>
                                        <input type="file" name="pan_document" id="pan_document" class="filepond">
                                        <p class="text-[10px] text-gray-400 mt-2 flex items-center gap-1">
                                            <iconify-icon icon="solar:info-circle-bold" class="text-xs"></iconify-icon>
                                            <?= !empty($vendor['pan_document']) ? 'Document uploaded!' : 'Document pending!' ?>
                                        </p>
                                    </div>

                                    <!-- GST Upload -->
                                    <div class="pt-4 border-t border-border/50">
                                        <label
                                            class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-3 uppercase tracking-wider">GST
                                            Registration Certificate</label>
                                        <input type="file" name="gst_document" id="gst_document" class="filepond">
                                        <p class="text-[10px] text-gray-400 mt-2 flex items-center gap-1">
                                            <iconify-icon icon="solar:info-circle-bold" class="text-xs"></iconify-icon>
                                            <?= !empty($vendor['gst_document']) ? 'Document uploaded!' : 'Document pending!' ?>
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-6 p-4 rounded-lg bg-primary/5 border border-primary/20">
                                    <p class="text-[11px] text-primary leading-relaxed">
                                        <strong>Note:</strong> Files should be in JPG, PNG or PDF format. Maximum file
                                        size allowed is 5MB.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </main>
        </div>
    </div>

    <!-- FilePond Scripts -->
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>

    <script>
        // Register FilePond plugins
        FilePond.registerPlugin(FilePondPluginImagePreview);

        // Turn all file input elements into ponds
        const panInput = document.querySelector('#pan_document');
        const gstInput = document.querySelector('#gst_document');

        FilePond.create(panInput, {
            storeAsFile: true,
            labelIdle: 'No file chosen <span class="filepond--label-action">Browse</span>',
            credits: { label: 'Powered by PQINA', url: 'https://pqina.nl' }
        });

        FilePond.create(gstInput, {
            storeAsFile: true,
            labelIdle: 'No file chosen <span class="filepond--label-action">Browse</span>',
            credits: { label: 'Powered by PQINA', url: 'https://pqina.nl' }
        });
    </script>
</body>

</html>