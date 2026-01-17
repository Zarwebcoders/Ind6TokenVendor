<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ind6Token Admin -
        <?= $title ?? 'Dashboard' ?>
    </title>
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
                        darkprimary: '#1c2536',
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