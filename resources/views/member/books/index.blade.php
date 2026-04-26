<x-member-layout>
    <x-slot name="title">
        Daftar Buku
    </x-slot>

    <div>
        <div class="mb-8">
            <h1 class="text-2xl font-semibold text-zinc-800">Jelajahi koleksi perpustakaan kami.</h1>
            <form action="{{ route('member.books.index') }}" method="GET" class="mt-4">
                <div class="relative w-full max-w-lg">
                    <svg class="absolute w-5 h-5 -translate-y-1/2 pointer-events-none left-4 top-1/2 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                    <input name="search_query"
                        class="w-full py-3 pr-4 text-sm transition bg-white border rounded-lg pl-11 text-zinc-800 placeholder-zinc-400 border-zinc-200 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary/60"
                        placeholder="Cari berdasarkan judul, penulis, atau kode buku..." type="text" value="{{ request('search_query') }}" />
                </div>
            </form>
        </div>

        @if ($books->count() > 0)
            <div class="grid grid-cols-2 gap-5 md:grid-cols-3 lg:grid-cols-4">
                @foreach ($books as $book)
                    <div class="flex flex-col overflow-hidden bg-white border rounded-xl border-zinc-200/80">
                        <div class="relative aspect-[3/4] bg-zinc-100">
                            <img src="{{ $book->image_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($book->title) . '&background=fff7ed&color=d97757' }}" 
                                alt="Cover: {{ $book->title }}" class="object-cover w-full h-full">
                            
                            @if ($book->stock > 0)
                                <span class="absolute top-2 right-2 px-2 py-1 text-[10px] font-bold text-white bg-green-500 rounded-full shadow">Tersedia</span>
                            @else
                                <span class="absolute top-2 right-2 px-2 py-1 text-[10px] font-bold text-white bg-red-500 rounded-full shadow">Habis</span>
                            @endif
                        </div>

                        <div class="flex flex-col flex-grow p-4">
                            <div class="flex-grow">
                                <p class="text-[11px] font-semibold tracking-wide text-zinc-500 uppercase">{{ $book->publish_year }}</p>
                                <h3 class="mt-1 text-base font-semibold leading-tight text-zinc-800">{{ $book->title }}</h3>
                                <p class="mt-2 text-xs font-medium text-zinc-500">{{ $book->author }}</p>
                            </div>

                            <div class="flex items-center justify-between pt-2 mt-2 border-t border-zinc-100">
                                <p class="text-xs font-medium text-zinc-800">{{ $book->book_code }}</p>
                                @if ($book->stock > 0)
                                    <a href="{{ route('member.loans.create', $book) }}" class="px-3 py-1.5 text-xs font-semibold text-white transition-colors rounded-md bg-primary hover:bg-primary/90 active:scale-95">
                                        Pinjam
                                    </a>
                                @else
                                    <button disabled class="px-3 py-1.5 text-xs font-semibold text-zinc-500 bg-zinc-200 rounded-md cursor-not-allowed">
                                        Antrian ({{ $book->loan_requests_count }})
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="py-20 text-center">
                <h3 class="text-lg font-medium text-zinc-700">Buku Tidak Ditemukan</h3>
                <p class="mt-1 text-sm text-zinc-500">
                    @if (request('search_query'))
                        Kami tidak dapat menemukan buku dengan kata kunci "{{ request('search_query') }}".
                    @else
                        Saat ini belum ada buku di perpustakaan.
                    @endif
                </p>
            </div>
        @endif

        <div class="mt-10">
            {{ $books->withQueryString()->links() }}
        </div>
    </div>
</x-member-layout>