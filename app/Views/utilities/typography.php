<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ind6Token Admin - Typography</title>
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
                <a href="../index"
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
                            <a href="../index"
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
                            <a href="./typography"
                                class="flex items-center px-4 py-2.5 rounded-md bg-lightprimary text-primary font-medium hover:bg-lightprimary hover:text-primary transition-colors">
                                <iconify-icon icon="tabler:a-b-2" class="text-xl mr-3"></iconify-icon>
                                <span>Typography</span>
                            </a>
                        </li>
                        <li>
                            <a href="./table"
                                class="flex items-center px-4 py-2.5 rounded-md text-gray-600 dark:text-gray-300 hover:bg-lightprimary hover:text-primary transition-colors">
                                <iconify-icon icon="tabler:table" class="text-xl mr-3"></iconify-icon>
                                <span>Order Table</span>
                            </a>
                        </li>
                        <li>
                            <a href="./form"
                                class="flex items-center px-4 py-2.5 rounded-md text-gray-600 dark:text-gray-300 hover:bg-lightprimary hover:text-primary transition-colors">
                                <iconify-icon icon="tabler:brand-terraform" class="text-xl mr-3"></iconify-icon>
                                <span>Form</span>
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
                            <a href="#"
                                class="flex items-center px-4 py-2.5 rounded-md text-gray-600 dark:text-gray-300 hover:bg-lightprimary hover:text-primary transition-colors">
                                <iconify-icon icon="tabler:mood-smile" class="text-xl mr-3"></iconify-icon>
                                <span>Icons</span>
                            </a>
                        </li>
                        <li>
                            <a href="#"
                                class="flex items-center px-4 py-2.5 rounded-md text-gray-600 dark:text-gray-300 hover:bg-lightprimary hover:text-primary transition-colors">
                                <iconify-icon icon="tabler:aperture" class="text-xl mr-3"></iconify-icon>
                                <span>Sample Page</span>
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
                    <h5 class="text-lg font-bold mb-4 text-dark dark:text-white">Typography</h5>
                    <div class="mt-6">
                        <div class="border border-border dark:border-gray-700 rounded-xl px-6 py-4 mb-6">
                            <h1 class="font-semibold text-4xl text-dark dark:text-white">h1.Heading</h1>
                            <p class="mt-2 text-gray-500">font size: 36 | line-height: 40 | font weight: 600</p>
                        </div>
                        <div class="border border-border dark:border-gray-700 rounded-xl px-6 py-4 mb-6">
                            <h2 class="font-semibold text-3xl text-dark dark:text-white">h2.Heading</h2>
                            <p class="mt-2 text-gray-500">font size: 30 | line-height: 36 | font weight: 600</p>
                        </div>
                        <div class="border border-border dark:border-gray-700 rounded-xl px-6 py-4 mb-6">
                            <h3 class="font-semibold text-2xl text-dark dark:text-white">h3.Heading</h3>
                            <p class="mt-2 text-gray-500">font size: 24 | line-height: 32 | font weight: 600</p>
                        </div>
                        <div class="border border-border dark:border-gray-700 rounded-xl px-6 py-4 mb-6">
                            <h4 class="font-semibold text-xl text-dark dark:text-white">h4.Heading</h4>
                            <p class="mt-2 text-gray-500">font size: 20 | line-height: 28 | font weight: 600</p>
                        </div>
                        <div class="border border-border dark:border-gray-700 rounded-xl px-6 py-4 mb-6">
                            <h5 class="font-semibold text-lg text-dark dark:text-white">h5.Heading</h5>
                            <p class="mt-2 text-gray-500">font size: 20 | line-height: 28 | font weight: 600</p>
                        </div>
                        <div class="border border-border dark:border-gray-700 rounded-xl px-6 py-4 mb-6">
                            <h6 class="font-semibold text-base text-dark dark:text-white">h6.Heading</h6>
                            <p class="mt-2 text-gray-500">font size: 16 | line-height: 24 | font weight: 600</p>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.js"></script>
</body>

</html>
