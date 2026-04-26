<x-member-layout>
    <x-slot name="title">
        Riwayat Peminjaman
    </x-slot>

    <div x-data="loanHistoryPage()">

        <div class="mb-8">
            <h1 class="text-2xl font-semibold text-zinc-800">Riwayat Peminjaman Anda</h1>
            <p class="mt-1 text-sm text-zinc-500">Pantau status pengajuan dan histori peminjaman buku Anda di sini.</p>
        </div>

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
                        placeholder="Cari judul buku..." type="text" />
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
                <table id="history-table" class="text-sm text-left">
                    <thead class="w-full">
                        <tr class="border-b border-zinc-200 bg-zinc-50">
                            <th class="w-12 px-5 py-4 text-xs font-semibold tracking-wide uppercase text-zinc-500">No
                            </th>
                            <th class="px-5 py-4 text-xs font-semibold tracking-wide uppercase text-zinc-500">Judul Buku
                            </th>
                            <th
                                class="px-5 py-4 text-xs font-semibold tracking-wide text-center uppercase text-zinc-500">
                                Tgl Pinjam</th>
                            <th
                                class="px-5 py-4 text-xs font-semibold tracking-wide text-center uppercase text-zinc-500">
                                Tgl Kembali</th>
                            <th
                                class="px-5 py-4 text-xs font-semibold tracking-wide text-center uppercase text-zinc-500">
                                Tgl Dikembalikan</th>
                            <th
                                class="px-5 py-4 text-xs font-semibold tracking-wide text-center uppercase text-zinc-500">
                                Status</th>
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
    </div>

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.datatables.net/2.0.5/js/dataTables.min.js"></script>

        <style>
            #history-table_wrapper>.dt-layout-row:not(:has(table)) {
                display: none !important;
            }

            #history-table {
                width: 100% !important;
            }

            #history-table tbody tr {
                transition: background 0.1s;
            }

            #history-table tbody tr:hover:not(.bg-red-50\/50) {
                background-color: #fafafa;
            }

            #history-table thead td,
            #history-table tbody td {
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
            function loanHistoryPage() {
                return {
                    searchQuery: '',
                    isSearching: false,
                    debounceTimer: null,

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

                window.dtTable = $('#history-table').DataTable({
                    processing: true,
                    serverSide: true,
                    pageLength: 8,
                    ajax: {
                        url: "{{ route('member.loan-history.data') }}",
                        data: function (d) {
                            d.search_query = $('#search-input').val();
                        }
                    },
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center font-semibold' },
                        { data: 'book_title', name: 'book.title', className: 'font-semibold text-zinc-800' },
                        { data: 'borrow_date', name: 'borrow_date', className: 'text-center text-zinc-600' },
                        { data: 'return_date', name: 'return_date', className: 'text-center text-zinc-600' },
                        { data: 'returned_at', name: 'returned_at', className: 'text-center text-zinc-600' },
                        {
                            data: 'combined_status',
                            name: 'combined_status',
                            className: 'text-center',
                            render: function (data) {
                                let statusText = '';
                                let cls = '';

                                if (data === 'pending') {
                                    statusText = 'Menunggu';
                                    cls = 'bg-orange-50 text-orange-600';
                                } else if (data === 'queue') {
                                    statusText = 'Antrian';
                                    cls = 'bg-blue-50 text-blue-600';
                                } else if (data === 'borrowed') {
                                    statusText = 'Disetujui';
                                    cls = 'bg-green-50 text-green-600';
                                } else if (data === 'rejected') {
                                    statusText = 'Ditolak';
                                    cls = 'bg-red-50 text-red-600';
                                } else if (data === 'returned') {
                                    statusText = 'Selesai';
                                    cls = 'text-zinc-600 bg-zinc-50';
                                } else {
                                    statusText = data;
                                    cls = 'bg-zinc-100 text-zinc-600';
                                }

                               return '<span class="inline-block px-2 py-1 text-xs font-semibold rounded-md ' + cls + '">' + statusText + '</span>';
                            }
                        },
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
            });
        </script>
    @endpush
</x-member-layout>