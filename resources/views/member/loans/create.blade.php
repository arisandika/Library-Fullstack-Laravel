<x-member-layout>
    <x-slot name="title">
        Ajukan Peminjaman
    </x-slot>

    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <div class="max-w-2xl mx-auto" x-data="loanRequestForm({
            selectedBook: {{ Js::from($selectedBook) }},
            allBooks: {{ Js::from($allBooks) }}
         })">

        <div class="mb-8 text-center">
            <h1 class="text-2xl font-semibold text-zinc-800">Ajukan Peminjaman Baru</h1>
            <p class="mt-1 text-sm text-zinc-500">Lengkapi form di bawah ini untuk meminjam buku dari perpustakaan.</p>
        </div>

        <div class="p-6 bg-white border shadow-sm rounded-xl border-zinc-200/80">
            <form x-ref="loanForm" action="{{ route('member.loans.store') }}" method="POST"
                @submit.prevent="openConfirmModal">
                @csrf
                <div class="space-y-5">

                    <div>
                        <label class="block mb-1.5 text-xs font-semibold uppercase tracking-wide text-zinc-500">Pilih
                            Buku</label>
                        <select id="book-select" name="book_id" x-model.number="selectedBookId"
                            placeholder="Cari judul atau penulis..."></select>
                        <input type="hidden" name="book_id" :value="selectedBookId">

                        @error('book_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror

                        <div x-show="bookStock === 0"
                            class="flex items-start gap-3 p-3 mt-3 text-sm text-red-700 border border-red-200 rounded-lg bg-red-50">
                            <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z" />
                            </svg>
                            <p>Stok buku habis. Silakan pilih buku lain atau tunggu hingga stok tersedia.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="borrow_date"
                                class="block mb-1.5 text-xs font-semibold uppercase tracking-wide text-zinc-500">Tanggal
                                Peminjaman</label>
                            <input type="date" name="borrow_date" id="borrow_date" x-model="borrowDate"
                                class="w-full px-3 py-2.5 text-sm text-zinc-800 bg-white border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary/60 transition">
                            @error('borrow_date') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="return_date"
                                class="block mb-1.5 text-xs font-semibold uppercase tracking-wide text-zinc-500">Tanggal
                                Pengembalian</label>
                            <input type="date" name="return_date" id="return_date" x-model="returnDate"
                                class="w-full px-3 py-2.5 text-sm text-zinc-800 bg-white border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary/60 transition">
                            @error('return_date') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <div
                            class="flex items-center justify-between px-4 py-3 border rounded-lg bg-zinc-50 border-zinc-100">
                            <span class="text-sm text-zinc-500">Durasi Pinjam (Otomatis)</span>
                            <span class="text-sm font-semibold text-zinc-800" x-text="loanDuration"></span>
                        </div>
                    </div>

                    <div>
                        <label for="notes"
                            class="block mb-1.5 text-xs font-semibold uppercase tracking-wide text-zinc-500">Catatan
                            (Opsional)</label>
                        <textarea name="notes" id="notes" rows="3" placeholder="Tambahkan catatan untuk pustakawan..."
                            class="w-full px-3 py-2.5 text-sm text-zinc-800 placeholder-zinc-400 bg-white border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary/60 transition"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-zinc-100">
                        <a href="{{ route('member.books.index') }}"
                            class="px-4 py-2.5 text-sm font-medium text-zinc-600 bg-zinc-100 border border-zinc-200 rounded-lg hover:bg-zinc-200 transition-colors">
                            Batal
                        </a>
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors">
                            Ajukan Peminjaman
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div x-show="isModalOpen" style="display: none;" @keydown.escape.window="isModalOpen = false"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-900/50 backdrop-blur-sm"
            x-transition.opacity>
            <div class="w-full max-w-md overflow-hidden bg-white rounded-xl" @click.away="isModalOpen = false">
                <div class="p-6 text-center">
                    <h3 class="text-lg font-medium text-zinc-800">Konfirmasi Pengajuan</h3>
                    <p class="mt-2 text-sm text-zinc-500">
                        Anda akan mengajukan peminjaman buku <strong x-text="selectedBookTitle"></strong>. Pastikan
                        tanggal peminjaman sudah benar.
                    </p>
                </div>
                <div class="flex items-center justify-end gap-2 px-6 py-4 border-t border-zinc-100 bg-zinc-50/50">
                    <button type="button" @click="isModalOpen = false"
                        class="px-4 py-2.5 text-sm font-medium text-zinc-600 bg-white border border-zinc-200 rounded-lg hover:bg-zinc-50 transition-colors">
                        Periksa Kembali
                    </button>
                    <button type="button" @click="$refs.loanForm.submit()"
                        class="px-4 py-2.5 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors">
                        Ya, Lanjutkan
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <style>
            .ts-control {
                padding: 12px 12px;
                border-radius: 8px;
            }
        </style>

        <script>
            function loanRequestForm(data) {
                return {
                    selectedBookId: data.selectedBook.id,
                    bookStock: data.selectedBook.stock,
                    selectedBookTitle: data.selectedBook.title,
                    borrowDate: '',
                    returnDate: '',
                    loanDuration: '-',
                    isModalOpen: false,

                    init() {
                        const tomSelect = new TomSelect('#book-select', {
                            options: data.allBooks.map(book => ({ value: book.id, text: book.title, author: book.author })),
                            items: [this.selectedBookId],
                            render: {
                                option: function (item, escape) {
                                    return `<div>
                                                                        <span class="font-medium text-zinc-800">${escape(item.text)}</span>
                                                                        <span class="block text-xs text-zinc-500 mt-0.5">${escape(item.author)}</span>
                                                                    </div>`;
                                },
                                item: function (item, escape) {
                                    return `<div>${escape(item.text)}</div>`;
                                }
                            }
                        });

                        const today = new Date().toISOString().split('T')[0];
                        this.borrowDate = today;

                        this.$watch('selectedBookId', (newVal) => this.updateBookDetails(newVal, data.allBooks));
                        this.$watch('borrowDate', () => this.calculateDuration());
                        this.$watch('returnDate', () => this.calculateDuration());

                        this.calculateDuration();
                    },

                    updateBookDetails(bookId, allBooks) {
                        const book = allBooks.find(b => b.id == bookId);
                        if (book) {
                            this.bookStock = book.stock;
                            this.selectedBookTitle = book.title;
                        }
                    },

                    calculateDuration() {
                        if (this.borrowDate && this.returnDate) {
                            const start = new Date(this.borrowDate);
                            const end = new Date(this.returnDate);
                            if (end > start) {
                                const diffTime = Math.abs(end - start);
                                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                                this.loanDuration = `${diffDays} Hari`;
                            } else {
                                this.loanDuration = '-';
                            }
                        } else {
                            this.loanDuration = '-';
                        }
                    },

                    openConfirmModal() {
                        if (this.selectedBookId && this.borrowDate && this.returnDate) {
                            this.isModalOpen = true;
                        } else {
                            this.$refs.loanForm.requestSubmit();
                        }
                    }
                }
            }
        </script>
    @endpush
</x-member-layout>