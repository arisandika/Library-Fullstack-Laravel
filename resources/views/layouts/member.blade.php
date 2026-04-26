<!DOCTYPE html>
<html class="light" lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Manajemen' }} | Empora Library</title>

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
                        "display-lg": ["Plus Jakarta Sans"],
                        "h2": ["Plus Jakarta Sans"],
                        "button": ["Plus Jakarta Sans"],
                        "body-sm": ["Plus Jakarta Sans"],
                        "body-base": ["Plus Jakarta Sans"],
                        "label-caps": ["Plus Jakarta Sans"],
                        "h1": ["Plus Jakarta Sans"]
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
            min-height: 100dvh;
        }

        [x-cloak] {
            display: none !important;
        }

        .scroll-bar-hidden {
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .scroll-bar-hidden::-webkit-scrollbar {
            display: none;
        }
    </style>
</head>


<body class="overflow-x-hidden bg-background text-on-background" x-data="{ sidebarOpen: false }">

    @include('layouts.topbar')

    @include('layouts.sidebar')

    <main class="px-4 pt-24 pb-12 transition-all duration-300 md:ml-64 md:px-8">
        <div class="mx-auto max-w-7xl">

            @if (isset($header))
                <div class="mb-8">
                    {{ $header }}
                </div>
            @endif

            {{ $slot }}

        </div>
    </main>

    @stack('scripts')
</body>

</html>