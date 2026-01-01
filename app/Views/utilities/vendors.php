<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ind6Token Admin - Order Table</title>
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
                <a href="../"
                    class="text-2xl font-extrabold text-primary flex items-center gap-2">
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
                            <a href="../"
                                class="flex items-center px-4 py-2.5 rounded-md text-gray-600 dark:text-gray-300 hover:bg-lightprimary hover:text-primary transition-colors">
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
                            <a href="./vendors"
                                class="flex items-center px-4 py-2.5 rounded-md bg-lightprimary text-primary font-medium hover:bg-lightprimary hover:text-primary transition-colors">
                                <iconify-icon icon="tabler:table" class="text-xl mr-3"></iconify-icon>
                                <span>Vendord</span>
                            </a>
                        </li>
                        <li>
                            <a href="./transactions"
                                class="flex items-center px-4 py-2.5 rounded-md text-gray-600 dark:text-gray-300 hover:bg-lightprimary hover:text-primary transition-colors">
                                <iconify-icon icon="tabler:table" class="text-xl mr-3"></iconify-icon>
                                <span>Transactions</span>
                            </a>
                        </li>
                        <li>
                            <a href="./form"
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
                            <a href="../user-profile"
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
                        <img src="../../images/profile/user-1.jpg"
                            class="w-9 h-9 rounded-full object-cover border border-gray-200" alt="Profile"
                            onerror="this.src='https://via.placeholder.com/40'" />
                    </div>
                </div>
            </header>

            <!-- Body Content -->
            <main class="p-[30px] container mx-auto">

                <!-- Product Performance -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-border overflow-hidden">
                    <div class="p-6">
                        <h5 class="text-lg font-bold text-dark dark:text-white">All Vendors</h5>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left align-middle text-gray-500">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th class="py-3 px-6 font-semibold">Id</th>
                                    <th class="py-3 px-6 font-semibold">Vendor</th>
                                    <th class="py-3 px-6 font-semibold">Contact</th>
                                    <th class="py-3 px-6 font-semibold">Status</th>
                                    <th class="py-3 px-6 font-semibold">KYC</th>
                                    <th class="py-3 px-6 font-semibold">Joined Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <?php if(!empty($vendors)): ?>
                                    <?php foreach($vendors as $vendor): ?>
                                    <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            #<?= $vendor['id'] ?>
                                        </td>
                                        <td class="py-4 px-6">
                                            <div class="flex items-center gap-3">
                                                 <img src="<?= !empty($vendor['profile_image']) ? base_url($vendor['profile_image']) : 'https://via.placeholder.com/40' ?>" 
                                                      alt="<?= esc($vendor['name']) ?>" class="w-10 h-10 rounded-full object-cover"
                                                      onerror="this.src='https://via.placeholder.com/40'">
                                                <div class="font-semibold text-dark dark:text-white">
                                                    <?= esc($vendor['name']) ?> 
                                                    <span class="block font-normal text-xs text-gray-500"><?= esc($vendor['business_name']) ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-6 text-gray-500">
                                            <div class="text-xs">
                                                <div><a href="mailto:<?= esc($vendor['email']) ?>" class="hover:text-primary"><?= esc($vendor['email']) ?></a></div>
                                                <div><a href="tel:<?= esc($vendor['phone']) ?>" class="hover:text-primary"><?= esc($vendor['phone']) ?></a></div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-6">
                                            <span class="px-3 py-1 <?= $vendor['status'] == 'active' ? 'bg-lightsuccess text-success' : 'bg-lighterror text-error' ?> rounded-md text-xs font-semibold uppercase">
                                                <?= $vendor['status'] ?>
                                            </span>
                                        </td>
                                        <td class="py-4 px-6">
                                             <span class="px-3 py-1 <?= $vendor['kyc_status'] == 'approved' ? 'bg-lightsuccess text-success' : ($vendor['kyc_status'] == 'pending' ? 'bg-lightwarning text-warning' : 'bg-lighterror text-error') ?> rounded-md text-xs font-semibold uppercase">
                                                <?= $vendor['kyc_status'] ?>
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 text-gray-500">
                                            <?= date('M d, Y', strtotime($vendor['created_at'])) ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr class="bg-white dark:bg-gray-800">
                                        <td colspan="6" class="py-4 px-6 text-center text-gray-500">No vendors found.</td>
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
