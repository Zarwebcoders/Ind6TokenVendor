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
        <button id="connect-wallet-btn"
            onclick="typeof connectWallet === 'function' ? connectWallet() : alert('Wallet integration pending')"
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
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
            </span>
        </button>

        <!-- Profile -->
        <div class="relative cursor-pointer group">
            <img src="<?= base_url('images/profile/user-1.jpg') ?>"
                class="w-9 h-9 rounded-full object-cover border border-gray-200" alt="Profile"
                onerror="this.src='https://via.placeholder.com/40'" />
            <div
                class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-border hidden group-hover:block">
                <div class="p-4 border-bottom border-border">
                    <p class="text-sm font-bold">
                        <?= session()->get('name') ?>
                    </p>
                    <p class="text-xs text-gray-500">
                        <?= session()->get('email') ?>
                    </p>
                </div>
                <a href="<?= base_url('user-profile') ?>"
                    class="block px-4 py-2 text-sm hover:bg-lightprimary hover:text-primary transition-colors">My
                    Profile</a>
                <a href="<?= base_url('auth/logout') ?>"
                    class="block px-4 py-2 text-sm text-error hover:bg-lighterror transition-colors">Logout</a>
            </div>
        </div>
    </div>
</header>