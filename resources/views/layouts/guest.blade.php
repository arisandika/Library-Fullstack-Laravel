<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet" />

    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    fontFamily: {
                        sans: ["Plus Jakarta Sans", 'sans-serif'],
                    },
                    colors: {
                        "primary-container": "#d97757",
                        "on-primary": "#ffffff",
                        "primary": "#d97757",
                        "background": "#f8f9fa",
                        "on-background": "#191c1d",
                        "on-surface": "#191c1d",
                        "on-surface-variant": "#55433d",
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
    </style>
</head>

<body class="text-on-background bg-background">
    <div class="flex flex-col items-center justify-center min-h-screen px-4 py-12 sm:px-6">
        <div class="mb-6">
            <h1 class="text-xl font-bold tracking-tight text-primary dark:text-white">Empora <span
                    class="text-zinc-800">Library</span></h1>
        </div>
        <div class="w-full max-w-lg px-8 py-10 overflow-hidden bg-white border sm:rounded-xl border-zinc-200">
            {{ $slot }}
        </div>
    </div>
</body>

</html>