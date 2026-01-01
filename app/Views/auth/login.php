<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ind6Token Admin - Login</title>
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
                        darkprimary: '#1c2536' // Approximate darkprimary
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
    </style>
</head>

<body class="bg-lightprimary dark:bg-darkprimary">

    <div class="relative overflow-hidden h-screen bg-lightprimary dark:bg-gray-800">
        <div class="flex h-full justify-center items-center px-4">
            <div class="md:w-[400px] w-full bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border-none">
                <!-- Logo -->
                <div class="mx-auto mb-6 flex justify-center">
                    <a href="../../"
                        class="text-2xl font-extrabold text-primary flex items-center gap-2">
                    Ind6Token
                    </a>
                </div>



                <!-- Divider -->
                <div class="relative flex py-5 items-center">
                    <div class="flex-grow border-t border-border dark:border-gray-700"></div>
                    <span class="flex-shrink mx-4 text-gray-500 text-sm">Vendor Login</span>
                    <div class="flex-grow border-t border-border dark:border-gray-700"></div>
                </div>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                        <?= session()->getFlashdata('error') ?>
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

                <!-- Form -->
                <form class="mt-2" action="<?= base_url('auth/login') ?>" method="post">
                    <div class="mb-4">
                        <div class="mb-2 block">
                            <label for="Email" class="font-medium text-sm text-dark dark:text-white">Email</label>
                        </div>
                        <input id="Email" name="email" type="email"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            value="admin@ind6token.com" required>
                    </div>
                    <div class="mb-4">
                        <div class="mb-2 block">
                            <label for="userpwd" class="font-medium text-sm text-dark dark:text-white">Password</label>
                        </div>
                        <input id="userpwd" name="password" type="password"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            value="password123" required>
                    </div>
                    <div class="flex justify-between my-5">
                        <div class="flex items-center gap-2">
                            <input id="accept" type="checkbox"
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <label for="accept"
                                class="font-normal cursor-pointer text-sm text-dark dark:text-white">Remember this
                                Device</label>
                        </div>
                    </div>
                    <button type="submit"
                        class="w-full text-white bg-primary hover:bg-blue-600 font-medium rounded-md text-sm px-5 py-2.5 mb-2 transition-colors">
                        Sign in
                    </button>

                    
                </form>

            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.js"></script>
</body>

</html>
