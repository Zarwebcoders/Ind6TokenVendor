<?php
$current_url = current_url();
?>
<!-- Sidebar -->
<aside
    class="fixed top-0 left-0 w-64 h-full bg-white dark:bg-dark border-r border-border z-30 transition-transform -translate-x-full xl:translate-x-0"
    id="sidebar">
    <!-- Logo -->
    <div class="px-6 py-5 flex items-center justify-between">
        <a href="<?= base_url() ?>" class="text-2xl font-extrabold text-primary flex items-center gap-2">
            Ind6Token
        </a>
        <button type="button"
            class="xl:hidden text-gray-400 hover:text-gray-900 rounded-lg text-sm p-1.5 absolute top-2.5 right-2.5 inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
            onclick="document.getElementById('sidebar').classList.add('-translate-x-full')">
            <iconify-icon icon="tabler:x" width="20"></iconify-icon>
        </button>
    </div>

    <!-- Nav -->
    <div class="px-4 py-4 overflow-y-auto h-[calc(100vh-100px)] custom-scrollbar">

        <!-- Section: Home -->
        <div class="mb-2">
            <h5 class="px-4 text-xs font-bold text-gray-400 uppercase mb-3 mt-4">Home</h5>
            <ul>
                <li>
                    <a href="<?= base_url() ?>"
                        class="flex items-center px-4 py-2.5 rounded-md <?= (base_url() == $current_url || base_url('vendor/dashboard') == $current_url) ? 'bg-lightprimary text-primary font-medium' : 'text-gray-600 dark:text-gray-300 hover:bg-lightprimary hover:text-primary transition-colors' ?>">
                        <iconify-icon icon="tabler:aperture" class="text-xl mr-3"></iconify-icon>
                        <span>Dashboard</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Section: Vendor API -->
        <div class="mb-2">
            <h5 class="px-4 text-xs font-bold text-gray-400 uppercase mb-3 mt-4">Vendor API</h5>
            <ul>
                <li>
                    <a href="<?= base_url('vendor/api-settings') ?>"
                        class="flex items-center px-4 py-2.5 rounded-md <?= base_url('vendor/api-settings') == $current_url ? 'bg-lightprimary text-primary font-medium' : 'text-gray-600 dark:text-gray-300 hover:bg-lightprimary hover:text-primary transition-colors' ?>">
                        <iconify-icon icon="tabler:key" class="text-xl mr-3"></iconify-icon>
                        <span>API Keys</span>
                    </a>
                </li>
                <li>
                    <a href="<?= base_url('vendor/api-docs') ?>"
                        class="flex items-center px-4 py-2.5 rounded-md <?= base_url('vendor/api-docs') == $current_url ? 'bg-lightprimary text-primary font-medium' : 'text-gray-600 dark:text-gray-300 hover:bg-lightprimary hover:text-primary transition-colors' ?>">
                        <iconify-icon icon="tabler:book" class="text-xl mr-3"></iconify-icon>
                        <span>Developer Guide</span>
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
                        class="flex items-center px-4 py-2.5 rounded-md <?= base_url('utilities/vendors') == $current_url ? 'bg-lightprimary text-primary font-medium' : 'text-gray-600 dark:text-gray-300 hover:bg-lightprimary hover:text-primary transition-colors' ?>">
                        <iconify-icon icon="tabler:users" class="text-xl mr-3"></iconify-icon>
                        <span>Vendors</span>
                    </a>
                </li>
                <li>
                    <a href="<?= base_url('utilities/transactions') ?>"
                        class="flex items-center px-4 py-2.5 rounded-md <?= base_url('utilities/transactions') == $current_url ? 'bg-lightprimary text-primary font-medium' : 'text-gray-600 dark:text-gray-300 hover:bg-lightprimary hover:text-primary transition-colors' ?>">
                        <iconify-icon icon="tabler:table" class="text-xl mr-3"></iconify-icon>
                        <span>Transactions</span>
                    </a>
                </li>
                <li>
                    <a href="<?= base_url('utilities/form') ?>"
                        class="flex items-center px-4 py-2.5 rounded-md <?= base_url('utilities/form') == $current_url ? 'bg-lightprimary text-primary font-medium' : 'text-gray-600 dark:text-gray-300 hover:bg-lightprimary hover:text-primary transition-colors' ?>">
                        <iconify-icon icon="tabler:building-bank" class="text-xl mr-3"></iconify-icon>
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
                        class="flex items-center px-4 py-2.5 rounded-md <?= base_url('user-profile') == $current_url ? 'bg-lightprimary text-primary font-medium' : 'text-gray-600 dark:text-gray-300 hover:bg-lightprimary hover:text-primary transition-colors' ?>">
                        <iconify-icon icon="tabler:user-circle" class="text-xl mr-3"></iconify-icon>
                        <span>Profile</span>
                    </a>
                </li>

                <li>
                    <a href="<?= base_url('auth/logout') ?>"
                        class="flex items-center px-4 py-2.5 rounded-md text-gray-600 dark:text-gray-300 hover:bg-lightprimary hover:text-primary transition-colors shadow-sm">
                        <iconify-icon icon="tabler:logout" class="text-xl mr-3"></iconify-icon>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </div>

    </div>
</aside>

<!-- Backdrop -->
<div class="bg-gray-900 bg-opacity-50 dark:bg-opacity-80 fixed inset-0 z-20 xl:hidden hidden" id="sidebarBackdrop"
    onclick="document.getElementById('sidebar').classList.add('-translate-x-full'); this.classList.add('hidden')">
</div>