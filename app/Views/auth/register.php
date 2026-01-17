<?= view('partials/head', ['title' => 'Register']) ?>

<body class="bg-lightprimary dark:bg-darkprimary">

    <div class="relative overflow-hidden h-screen bg-lightprimary dark:bg-gray-800">
        <div class="flex h-full justify-center items-center px-4">
            <div class="md:w-[450px] w-full bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border-none">
                <!-- Logo -->
                <div class="mx-auto mb-6 flex justify-center">
                    <a href="../../" class="text-2xl font-extrabold text-primary flex items-center gap-2">
                        Ind6Token
                    </a>
                </div>

                <!-- Social Buttons -->
                <div class="flex justify-between gap-8 my-4 mt-2">
                    <div
                        class="px-4 py-2.5 border-border border dark:border-gray-700 flex gap-2 items-center w-full rounded-md text-center justify-center text-dark dark:text-white cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <img src="../../images/svgs/google-icon.svg" alt="google" height="18" width="18"
                            onerror="this.style.display='none'" /> Google
                    </div>
                    <div
                        class="px-4 py-2.5 border-border border dark:border-gray-700 flex gap-2 items-center w-full rounded-md text-center justify-center text-dark dark:text-white cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <img src="../../images/svgs/logos--github-icon.svg" alt="github" height="18" width="18"
                            onerror="this.style.display='none'" />
                        Github
                    </div>
                </div>

                <!-- Divider -->
                <div class="relative flex py-5 items-center">
                    <div class="flex-grow border-t border-border dark:border-gray-700"></div>
                    <span class="flex-shrink mx-4 text-gray-500 text-sm">or sign up with</span>
                    <div class="flex-grow border-t border-border dark:border-gray-700"></div>
                </div>

                <!-- Form -->
                <form class="mt-6">
                    <div class="mb-4">
                        <div class="mb-2 block">
                            <label for="name" class="font-semibold text-sm text-dark dark:text-white">Name</label>
                        </div>
                        <input id="name" type="text"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    </div>
                    <div class="mb-4">
                        <div class="mb-2 block">
                            <label for="emadd" class="font-semibold text-sm text-dark dark:text-white">Email
                                Address</label>
                        </div>
                        <input id="emadd" type="text"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    </div>
                    <div class="mb-6">
                        <div class="mb-2 block">
                            <label for="userpwd"
                                class="font-semibold text-sm text-dark dark:text-white">Password</label>
                        </div>
                        <input id="userpwd" type="password"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    </div>
                    <button type="submit"
                        class="w-full text-white bg-primary hover:bg-blue-600 font-medium rounded-md text-sm px-5 py-2.5 mb-2 transition-colors">Sign
                        Up</button>

                    <div class="flex gap-2 text-base text-gray-500 font-medium mt-6 items-center justify-start">
                        <p>Already have an Account?</p>
                        <a href="<?= base_url('auth/login') ?>"
                            class="text-primary text-sm font-medium hover:underline">Sign
                            in</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.js"></script>
</body>

</html>