<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ind6Token Admin - Form</title>
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
                                class="flex items-center px-4 py-2.5 rounded-md text-gray-600 dark:text-gray-300 hover:bg-lightprimary hover:text-primary transition-colors">
                                <iconify-icon icon="tabler:table" class="text-xl mr-3"></iconify-icon>
                                <span>Vendors</span>
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
                                class="flex items-center px-4 py-2.5 rounded-md bg-lightprimary text-primary font-medium hover:bg-lightprimary hover:text-primary transition-colors">
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
                <div
                    class="rounded-xl shadow-md bg-white dark:bg-gray-800 p-6 relative w-full break-words border border-border">
                    <h5 class="text-lg font-bold mb-4 text-dark dark:text-white">Bank Details</h5>
                    
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                            <span class="font-medium">Success!</span> <?= session()->getFlashdata('success') ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                            <span class="font-medium">Error!</span> <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                            <ul>
                            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div class="mt-6">
                        <form action="<?= base_url('bank/save') ?>" method="post" enctype="multipart/form-data">
                        <!-- Hidden ID for Update -->
                        <?php if(isset($bank['id'])): ?>
                            <input type="hidden" name="id" value="<?= $bank['id'] ?>">
                        <?php endif; ?>

                        <div class="grid grid-cols-12 md:gap-6 gap-0">
                            <!-- Col 1 -->
                            <div class="lg:col-span-6 col-span-12">
                                <div class="flex flex-col gap-4">
                                    <div>
                                        <div class="mb-2 block">
                                            <label for="account_holder" class="font-medium text-sm text-dark dark:text-white">Account Holder Name</label>
                                        </div>
                                        <input id="account_holder" name="account_holder" type="text" placeholder="e.g. John Doe" required 
                                            value="<?= old('account_holder', $bank['account_holder'] ?? '') ?>"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    </div>
                                    <div>
                                        <div class="mb-2 block">
                                            <label for="account_number"
                                                class="font-medium text-sm text-dark dark:text-white">Account Number</label>
                                        </div>
                                        <input id="account_number" name="account_number" type="text" placeholder="e.g. 1234567890" required 
                                            value="<?= old('account_number', $bank['account_number'] ?? '') ?>"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    </div>
                                    <div>
                                        <div class="mb-2 block">
                                            <label for="ifsc"
                                                class="font-medium text-sm text-dark dark:text-white">IFSC Code</label>
                                        </div>
                                        <input id="ifsc" name="ifsc" type="text" placeholder="e.g. SBIN0001234" required 
                                            value="<?= old('ifsc', $bank['ifsc'] ?? '') ?>"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    </div>
                                </div>
                            </div>
                            <!-- Col 2 -->
                            <div class="lg:col-span-6 col-span-12 md:my-0 my-6">
                                <div class="flex flex-col gap-4">
                                     <div>
                                        <div class="mb-2 block">
                                            <label for="bank_name"
                                                class="font-medium text-sm text-dark dark:text-white">Bank Name</label>
                                        </div>
                                        <input id="bank_name" name="bank_name" type="text" placeholder="e.g. State Bank of India" required 
                                            value="<?= old('bank_name', $bank['bank_name'] ?? '') ?>"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    </div>
                                    <div>
                                        <div class="mb-2 block">
                                            <label for="upi_id"
                                                class="font-medium text-sm text-dark dark:text-white">UPI ID (Optional)</label>
                                        </div>
                                        <input id="upi_id" name="upi_id" type="text" placeholder="e.g. username@upi" 
                                            value="<?= old('upi_id', $bank['upi_id'] ?? '') ?>"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    </div>
                                    <div>
                                        <div class="mb-2 block">
                                            <label for="upi_qr"
                                                class="font-medium text-sm text-dark dark:text-white">UPI QR Image (Optional)</label>
                                        </div>
                                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="upi_qr">Upload file</label>
                                        <input class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" 
                                            id="upi_qr" name="upi_qr" type="file">
                                        <?php if(isset($bank['upi_qr']) && $bank['upi_qr']): ?>
                                            <div class="mt-2">
                                                <p class="text-sm text-gray-500 mb-1">Current QR:</p>
                                                <img src="<?= base_url($bank['upi_qr']) ?>" alt="UPI QR" class="h-20 w-20 object-cover border rounded">
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <!-- Buttons -->
                            <div class="col-span-12 flex gap-3 mt-6">
                                <button type="submit"
                                    class="bg-primary hover:bg-blue-600 text-white font-medium rounded-lg text-sm px-5 py-2.5 ease-in-out transition-all">
                                    <?= isset($bank['id']) ? 'Update Details' : 'Save Details' ?>
                                </button>
                                <button type="reset"
                                    class="bg-error hover:bg-red-600 text-white font-medium rounded-lg text-sm px-5 py-2.5 ease-in-out transition-all">
                                    Reset
                                </button>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.js"></script>
</body>

</html>
