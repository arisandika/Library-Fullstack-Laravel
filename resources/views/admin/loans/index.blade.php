<x-app-layout>
    <x-slot name="title">
        Peminjaman Aktif
    </x-slot>

    <x-slot name="header">
        <div class="flex flex-col justify-between gap-3 md:flex-row md:items-end">
            <div>
                <nav class="flex items-center gap-1.5 mb-2 text-xs" aria-label="Breadcrumb">
                    @foreach ($breadcrumbs as $crumb)
                        @if (!$loop->last)
                            <a href="{{ $crumb['url'] }}" class="transition-colors text-zinc-500 hover:text-zinc-600">
                                {{ $crumb['label'] }}
                            </a>
                            <svg class="flex-shrink-0 w-3 h-3 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                            </svg>
                        @else
                            <span class="font-medium text-zinc-700">{{ $crumb['label'] }}</span>
                        @endif
                    @endforeach
                </nav>
                <h2 class="text-2xl font-semibold text-zinc-800">Peminjaman Aktif</h2>
                <p class="mt-1 text-sm text-zinc-500">Pantau buku yang sedang dipinjam dan kelola pengembalian.</p>
            </div>
        </div>
    </x-slot>

    <div x-data="loanPage()">

        <div class="p-5 bg-white border-t border-x border-zinc-200 rounded-t-xl">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div class="relative w-full md:max-w-xs">
                    <svg class="absolute w-4 h-4 -translate-y-1/2 pointer-events-none left-3 top-1/2 text-zinc-400"
                        fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                    <input id="search-input" x-model="searchQuery" @input="onSearchInput"
                        class="w-full pl-9 pr-9 py-2.5 text-sm text-zinc-800 placeholder-zinc-400 bg-white border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary/60 transition"
                        placeholder="Cari nama anggota atau judul buku..." type="text" />
                    <span x-show="isSearching" class="absolute -translate-y-1/2 right-3 top-1/2" style="display: none;">
                        <svg class="w-4 h-4 animate-spin text-primary" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                        </svg>
                    </span>
                    <button x-show="searchQuery && !isSearching" @click="clearSearch" style="display: none;"
                        class="absolute transition-colors -translate-y-1/2 right-3 top-1/2 text-zinc-400 hover:text-zinc-600">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-hidden bg-white border border-zinc-200 rounded-b-xl">
            <div class="overflow-x-auto">
                <table id="loans-table" class="text-sm text-left">
                    <thead class="w-full">
                        <tr class="border-b border-zinc-200 bg-zinc-50">
                            <th class="w-12 px-5 py-4 text-xs font-semibold tracking-wide uppercase text-zinc-500">No
                            </th>
                            <th class="px-5 py-4 text-xs font-semibold tracking-wide uppercase truncate text-zinc-500">
                                Nama
                                Anggota</th>
                            <th class="px-5 py-4 text-xs font-semibold tracking-wide uppercase truncate text-zinc-500">
                                Judul Buku
                            </th>
                            <th
                                class="px-5 py-4 text-xs font-semibold tracking-wide text-center uppercase truncate text-zinc-500">
                                Tgl Pinjam</th>
                            <th
                                class="px-5 py-4 text-xs font-semibold tracking-wide text-center uppercase truncate text-zinc-500">
                                Tgl Kembali</th>
                            <th
                                class="px-5 py-4 text-xs font-semibold tracking-wide text-center uppercase truncate text-zinc-500">
                                Keterlambatan</th>
                            <th
                                class="px-5 py-4 text-xs font-semibold tracking-wide text-center uppercase truncate text-zinc-500">
                                Status</th>
                            <th
                                class="px-5 py-4 text-xs font-semibold tracking-wide text-center uppercase truncate text-zinc-500">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100"></tbody>
                </table>
            </div>

            <div id="dt-footer"
                class="flex items-center justify-between px-5 py-3 border-t border-zinc-200 bg-zinc-50/60">
                <div id="dt-info" class="text-xs text-zinc-500"></div>
                <div id="dt-pagination" class="flex items-center gap-1"></div>
            </div>
        </div>

        <div x-show="isReturnModalOpen" style="display: none;"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-900/50 backdrop-blur-sm"
            x-transition.opacity>
            <div class="w-full max-w-lg overflow-hidden bg-white rounded-xl" @click.away="closeReturnModal()">
                <div class="flex items-center justify-between px-6 py-4 border-b border-zinc-100">
                    <h3 class="text-base font-semibold text-zinc-800">Konfirmasi Pengembalian Buku</h3>
                    <button @click="closeReturnModal()"
                        class="p-1 transition-colors rounded-md text-zinc-400 hover:text-zinc-600 hover:bg-zinc-100">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4 p-4 mb-4 text-sm border rounded-lg bg-zinc-50 border-zinc-100">
                        <div class="col-span-2">
                            <span class="block text-xs font-semibold text-zinc-500 uppercase tracking-wide mb-0.5">Judul
                                Buku</span>
                            <span class="font-medium text-zinc-800" x-text="loanDetails.book_title"></span>
                        </div>
                        <div class="col-span-2">
                            <span class="block text-xs font-semibold text-zinc-500 uppercase tracking-wide mb-0.5">Nama
                                Anggota</span>
                            <span class="font-medium text-zinc-800" x-text="loanDetails.member_name"></span>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-zinc-500 uppercase tracking-wide mb-0.5">Tgl
                                Pinjam</span>
                            <span class="text-zinc-700" x-text="loanDetails.borrow_date"></span>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-zinc-500 uppercase tracking-wide mb-0.5">Batas
                                Kembali</span>
                            <span class="text-zinc-700" x-text="loanDetails.return_date"></span>
                        </div>
                    </div>

                    <template x-if="loanDetails.days_late > 0">
                        <div class="flex items-start gap-3 p-4 border border-red-200 rounded-lg bg-red-50">
                            <div class="p-2 text-red-600 bg-white rounded-full shadow-sm">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-red-800">Terlambat <span
                                        x-text="loanDetails.days_late"></span> Hari</h4>
                                <p class="text-xs text-red-600 mt-0.5">Terdapat denda keterlambatan sebesar <strong
                                        class="text-sm font-bold" x-text="loanDetails.formatted_fine"></strong> yang
                                    harus dilunasi.</p>
                            </div>
                        </div>
                    </template>
                    <template x-if="loanDetails.days_late <= 0">
                        <div
                            class="flex items-center gap-2 p-3 text-sm text-green-700 border border-green-200 rounded-lg bg-green-50">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="font-medium">Dikembalikan Tepat Waktu.</span>
                        </div>
                    </template>
                </div>

                <div class="flex items-center justify-end gap-2 px-6 py-4 border-t border-zinc-100 bg-zinc-50/50">
                    <button type="button" @click="closeReturnModal()"
                        class="px-4 py-2.5 text-sm font-medium text-zinc-600 bg-white border border-zinc-200 rounded-lg hover:bg-zinc-50 transition-colors">
                        Batal
                    </button>
                    <button type="button" @click="submitReturn()" :disabled="isSubmitting"
                        class="px-4 py-2.5 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors flex items-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed">
                        <span x-show="isSubmitting" class="animate-spin" style="display: none;">
                            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                            </svg>
                        </span>
                        <span>Konfirmasi Dikembalikan</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.datatables.net/2.0.5/js/dataTables.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <style>
            #loans-table_wrapper>.dt-layout-row:not(:has(table)) {
                display: none !important;
            }

            #loans-table {
                width: 100% !important;
            }

            #loans-table tbody tr {
                transition: background 0.15s ease-in-out;
            }

            #loans-table tbody tr:hover:not(.bg-red-50\/50) {
                background-color: #fafafa;
            }

            #loans-table thead td,
            #loans-table tbody td {
                padding: 0.875rem 1.25rem;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                vertical-align: middle;
            }

            .dt-page-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-width: 2rem;
                height: 2rem;
                padding: 0 0.5rem;
                font-size: 0.75rem;
                font-weight: 500;
                border: 1px solid #e4e4e7;
                border-radius: 0.5rem;
                color: #52525b;
                background: #fff;
                cursor: pointer;
                transition: all 0.15s;
            }

            .dt-page-btn:hover:not(:disabled) {
                background: var(--color-primary, #d97757);
                border-color: var(--color-primary, #d97757);
                color: #fff;
            }

            .dt-page-btn.active {
                background: #fff7ed;
                border-color: var(--color-primary, #d97757);
                color: var(--color-primary, #d97757);
                font-weight: 600;
            }

            .dt-page-btn:disabled {
                opacity: 0.35;
                cursor: not-allowed;
            }
        </style>

        <script>
            function loanPage() {
                return {
                    searchQuery: '',
                    isSearching: false,
                    debounceTimer: null,

                    isReturnModalOpen: false,
                    isSubmitting: false,
                    loanId: null,
                    loanDetails: {
                        member_name: '', book_title: '', borrow_date: '', return_date: '', days_late: 0,
                    },

                    init() {
                        window.addEventListener('open-return-modal', (e) => {
                            this.loanId = e.detail.id;
                            this.loanDetails = e.detail.data;
                            this.isReturnModalOpen = true;
                        });
                    },

                    closeReturnModal() {
                        this.isReturnModalOpen = false;
                        setTimeout(() => { this.loanId = null; }, 300);
                    },

                    submitReturn() {
                        this.isSubmitting = true;
                        $.ajax({
                            url: "{{ url('admin/loans') }}/" + this.loanId + "/return",
                            type: 'POST',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: (res) => {
                                this.isSubmitting = false;
                                this.closeReturnModal();
                                window.dtTable.draw(false);
                                Swal.fire({ icon: 'success', title: 'Dikembalikan!', text: res.message, timer: 2000, showConfirmButton: false });
                            },
                            error: (xhr) => {
                                this.isSubmitting = false;
                                Swal.fire({ icon: 'error', title: 'Gagal', text: xhr.responseJSON?.message || 'Terjadi kesalahan sistem.' });
                            }
                        });
                    },

                    onSearchInput() {
                        clearTimeout(this.debounceTimer);
                        if (this.searchQuery.length === 0) {
                            this.isSearching = false;
                            window.dtTable && window.dtTable.draw();
                            return;
                        }
                        this.isSearching = true;
                        this.debounceTimer = setTimeout(() => {
                            this.isSearching = false;
                            window.dtTable && window.dtTable.draw();
                        }, 500);
                    },
                    clearSearch() {
                        this.searchQuery = '';
                        this.isSearching = false;
                        window.dtTable && window.dtTable.draw();
                    },
                }
            }
        </script>

        <script>
            $(document).ready(function () {
                $.ajaxSetup({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
                });

                window.dtTable = $('#loans-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('admin.loans.data') }}",
                        data: function (d) {
                            d.search_query = $('#search-input').val();
                        }
                    },
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center font-semibold' },
                        { data: 'member_name', name: 'member.name', className: 'font-semibold' },
                        { data: 'book_title', name: 'book.title', className: 'font-semibold' },
                        { data: 'borrow_date', name: 'borrow_date', className: 'text-center' },
                        { data: 'return_date', name: 'return_date', className: 'text-center' },
                        { data: 'lateness', name: 'lateness', className: 'text-center', orderable: false, searchable: false },
                        {
                            data: 'status',
                            name: 'status',
                            className: 'text-center',
                            render: function (data) {
                                let statusText = '';
                                let cls = '';
                                
                                if (data == 'returned') {
                                    statusText = 'Dikembalikan';
                                    cls = 'bg-zinc-100 text-zinc-600';
                                } else if (data == 'borrowed') {
                                    statusText = 'Dipinjam';
                                    cls = 'bg-red-50 text-red-600';
                                }

                                return '<span class="inline-block px-2 py-1 text-xs font-semibold rounded-md ' + cls + '">' + statusText + '</span>';
                            }
                        },
                        { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
                    ],
                    searching: false,
                    lengthChange: false,
                    dom: 't',
                    drawCallback: function (settings) {
                        renderPagination(this.api());
                    }
                });

                function renderPagination(api) {
                    var info = api.page.info();
                    var start = info.recordsTotal === 0 ? 0 : info.start + 1;
                    var end = Math.min(info.end, info.recordsTotal);

                    $('#dt-info').html(
                        'Menampilkan <span class="font-semibold text-zinc-700">' + start + '–' + end + '</span> dari <span class="font-semibold text-zinc-700">' + info.recordsTotal + '</span> data'
                    );

                    var $pg = $('#dt-pagination').empty();
                    var currentPage = info.page;
                    var totalPages = info.pages;

                    var $prev = $('<button class="dt-page-btn">').attr('title', 'Sebelumnya').html(
                        '<svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>'
                    );
                    if (currentPage === 0) $prev.prop('disabled', true);
                    else $prev.on('click', function () { api.page('previous').draw('page'); });
                    $pg.append($prev);

                    var startPage = Math.max(0, currentPage - 2);
                    var endPage = Math.min(totalPages - 1, startPage + 4);
                    if (endPage - startPage < 4) startPage = Math.max(0, endPage - 4);

                    if (startPage > 0) {
                        $pg.append($('<button class="dt-page-btn">').text(1).on('click', function () { api.page(0).draw('page'); }));
                        if (startPage > 1) $pg.append($('<span class="dt-page-btn" style="border:none;cursor:default;background:transparent">…</span>'));
                    }

                    for (var i = startPage; i <= endPage; i++) {
                        var $btn = $('<button class="dt-page-btn">').text(i + 1);
                        if (i === currentPage) $btn.addClass('active');
                        else (function (page) { $btn.on('click', function () { api.page(page).draw('page'); }); })(i);
                        $pg.append($btn);
                    }

                    if (endPage < totalPages - 1) {
                        if (endPage < totalPages - 2) $pg.append($('<span class="dt-page-btn" style="border:none;cursor:default;background:transparent">…</span>'));
                        $pg.append($('<button class="dt-page-btn">').text(totalPages).on('click', function () { api.page(totalPages - 1).draw('page'); }));
                    }

                    var $next = $('<button class="dt-page-btn">').attr('title', 'Berikutnya').html(
                        '<svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>'
                    );
                    if (currentPage >= totalPages - 1) $next.prop('disabled', true);
                    else $next.on('click', function () { api.page('next').draw('page'); });
                    $pg.append($next);
                }

                $(document).on('click', '.btn-return', function () {
                    var id = $(this).data('id');
                    var $btn = $(this);
                    var originalHtml = $btn.html();

                    $btn.html('<svg class="w-4 h-4 text-white animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg> Memuat...').prop('disabled', true);

                    $.get("{{ url('admin/loans') }}/" + id, function (data) {
                        $btn.html(originalHtml).prop('disabled', false);
                        window.dispatchEvent(new CustomEvent('open-return-modal', { detail: { id: id, data: data } }));
                    }).fail(function () {
                        $btn.html(originalHtml).prop('disabled', false);
                        Swal.fire({ icon: 'error', title: 'Gagal', text: 'Tidak dapat mengambil detail peminjaman.' });
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>