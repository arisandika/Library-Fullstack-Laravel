<x-guest-layout>

    <div class="mb-5 text-center">
        <h2 class="text-2xl font-bold text-zinc-800">Dashboard Member</h2>
        <p class="mt-1 text-sm text-zinc-500">Selamat datang, silakan masuk ke akun Anda.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('member.login') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block mb-1.5 text-xs font-semibold uppercase tracking-wide text-zinc-500">
                Email
            </label>
            <x-text-input id="email" class="block w-full" type="email" name="email" :value="old('email')" required
                autofocus autocomplete="username" placeholder="email@member.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <label for="password" class="block mb-1.5 text-xs font-semibold uppercase tracking-wide text-zinc-500">
                Password
            </label>
            <x-text-input id="password" class="block w-full" type="password" name="password" required
                autocomplete="current-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end">
            <a class="text-sm underline rounded-md text-primary hover:text-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary/50"
                href="{{ route('login') }}">
                Login Akun Admin
            </a>
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full">
                Login
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>