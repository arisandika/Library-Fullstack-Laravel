<header
    class="fixed top-0 z-40 flex items-center justify-between w-full h-16 px-4 bg-white border-b dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 md:px-6">
    <div class="flex items-center gap-3">
        <button @click="sidebarOpen = !sidebarOpen"
            class="p-2 -ml-2 rounded-lg md:hidden text-zinc-500 hover:bg-zinc-50">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>

        <h1 class="hidden text-lg font-bold tracking-tight text-primary dark:text-white sm:block">Empora <span class="text-zinc-800">Library</span></h1>
    </div>

    <div class="flex items-center gap-4">
        <div class="flex items-center gap-3 pl-4 ml-2 border-l border-zinc-200">
            <div class="hidden text-right sm:block">
                <p class="text-xs font-bold text-zinc-900">
                    {{ Auth::guard('member')->check() ? Auth::guard('member')->user()->name : Auth::user()->name }}
                </p>
                <p class="text-[10px] text-zinc-500 tracking-wider">
                    {{ Auth::guard('member')->check() ? Auth::guard('member')->user()->email : Auth::user()->email }}
                </p>
            </div>
        </div>
    </div>
</header>