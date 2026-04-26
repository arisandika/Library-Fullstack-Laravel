<x-app-layout>
    <x-slot name="title">
        Pengajuan Peminjaman
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
                <h2 class="text-2xl font-semibold text-zinc-800">Pengajuan Peminjaman</h2>
                <p class="mt-1 text-sm text-zinc-500">Kelola persetujuan peminjaman buku dari anggota perpustakaan.</p>
            </div>
        </div>
    </x-slot>

    <div x-data="loanRequestPage()">

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
                <table id="loan-requests-table" class="text-sm text-left">
                    <thead class="w-full">
                        <tr class="border-b border-zinc-200 bg-zinc-50">
                            <th class="w-12 px-5 py-4 text-xs font-semibold tracking-wide uppercase text-zinc-500">No
                            </th>
                            <th class="px-5 py-4 text-xs font-semibold tracking-wide uppercase text-zinc-500">Nama
                                Anggota</th>
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
                                Status</th>
                            <th
                                class="px-5 py-4 text-xs font-semibold tracking-wide text-center uppercase text-zinc-500">
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

        <div x-show="isRejectModalOpen" style="display: none;"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-900/50 backdrop-blur-sm"
            x-transition.opacity>
            <div class="w-full max-w-lg overflow-hidden bg-white rounded-xl" @click.away="closeRejectModal()">
                <div class="flex items-center justify-between px-6 py-4 border-b border-zinc-100">
                    <h3 class="text-base font-semibold text-zinc-800">Tolak Pengajuan Peminjaman</h3>
                    <button @click="closeRejectModal()"
                        class="p-1 transition-colors rounded-md text-zinc-400 hover:text-zinc-600 hover:bg-zinc-100">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="submitReject" class="p-6 space-y-4">
                    <div>
                        <label for="rejection_reason"
                            class="block mb-1.5 text-xs font-semibold uppercase tracking-wide text-zinc-500">Alasan
                            Penolakan</label>
                        <textarea id="rejection_reason" x-model="rejectReason" rows="4"
                            class="w-full px-3 py-2.5 text-sm text-zinc-800 placeholder-zinc-400 bg-white border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary/60 transition"
                            placeholder="Tuliskan alasan mengapa pengajuan ini ditolak..."></textarea>
                        <p x-show="rejectError" x-text="rejectError" class="mt-1 text-xs text-red-500"
                            style="display: none;"></p>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-4 border-t border-zinc-100">
                        <button type="button" @click="closeRejectModal()"
                            class="px-4 py-2.5 text-sm font-medium text-zinc-600 bg-zinc-100 border border-zinc-200 rounded-lg hover:bg-zinc-200 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2.5 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors flex items-center gap-2">
                            <span x-show="isSubmitting" class="animate-spin">
                                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                </svg>
                            </span>
                            Konfirmasi Penolakan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.datatables.net/2.0.5/js/dataTables.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <style>
            #loan-requests-table_wrapper>.dt-layout-row:not(:has(table)) {
                display: none !important;
            }

            #loan-requests-table {
                width: 100% !important;
            }

            #loan-requests-table tbody tr {
                transition: background 0.1s;
            }

            #loan-requests-table tbody tr:hover {
                background-color: #fafafa;
            }

            #loan-requests-table thead td,
            #loan-requests-table tbody td {
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
            function loanRequestPage() {
                return {
                    searchQuery: '',
                    isSearching: false,
                    debounceTimer: null,

                    isRejectModalOpen: false,
                    rejectId: null,
                    rejectReason: '',
                    rejectError: '',
                    isSubmitting: false,

                    init() {
                        window.addEventListener('open-reject-modal', (e) => {
                            this.rejectId = e.detail.id;
                            this.rejectReason = '';
                            this.rejectError = '';
                            this.isRejectModalOpen = true;
                        });
                    },

                    closeRejectModal() {
                        this.isRejectModalOpen = false;
                        setTimeout(() => { this.rejectId = null; }, 300);
                    },

                    submitReject() {
                        this.rejectError = '';
                        if (!this.rejectReason.trim()) {
                            this.rejectError = 'Alasan penolakan wajib diisi.';
                            return;
                        }

                        this.isSubmitting = true;

                        $.ajax({
                            url: "{{ url('admin/loan-requests') }}/" + this.rejectId + "/reject",
                            type: 'POST',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                rejection_reason: this.rejectReason
                            },
                            success: (res) => {
                                this.isSubmitting = false;
                                this.closeRejectModal();
                                window.dtTable.draw();
                                Swal.fire({ icon: 'success', title: 'Berhasil Ditolak!', text: res.message, timer: 2000, showConfirmButton: false });
                            },
                            error: (xhr) => {
                                this.isSubmitting = false;
                                if (xhr.status === 422) {
                                    this.rejectError = xhr.responseJSON.errors.rejection_reason[0];
                                } else {
                                    this.rejectError = 'Terjadi kesalahan pada sistem.';
                                }
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

                window.dtTable = $('#loan-requests-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('admin.loan-requests.data') }}",
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
                        {
                            data: 'status',
                            name: 'status',
                            className: 'text-center capitalize',
                            render: function (data) {
                                let statusText = '';
                                let cls = '';

                                if (data == 'pending') {
                                    statusText = 'Menunggu';
                                    cls = 'bg-orange-50 text-orange-600';
                                } else if (data === 'returned') {
                                    statusText = 'Selesai';
                                    cls = 'text-zinc-600 bg-zinc-50';
                                } else if (data == 'approved') {
                                    statusText = 'Disetujui';
                                    cls = 'bg-green-50 text-green-600';
                                } else if (data == 'rejected') {
                                    statusText = 'Ditolak';
                                    cls = 'bg-red-50 text-red-600';
                                } else {
                                    statusText = data;
                                    cls = 'bg-zinc-100 text-zinc-600';
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

                $(document).on('click', '.btn-approve', function () {
                    var id = $(this).data('id');
                    Swal.fire({
                        title: 'Setujui Pengajuan?',
                        text: 'Buku akan dipinjamkan dan stok akan berkurang.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#10b981',
                        cancelButtonColor: '#a1a1aa',
                        confirmButtonText: 'Ya, Setujui',
                        cancelButtonText: 'Batal'
                    }).then(result => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "{{ url('admin/loan-requests') }}/" + id + "/approve",
                                type: 'POST',
                                success: function (res) {
                                    window.dtTable.draw();
                                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.message, timer: 2000, showConfirmButton: false });
                                },
                                error: function (xhr) {
                                    let errorMsg = 'Terjadi kesalahan pada sistem.';
                                    if (xhr.status === 422 && xhr.responseJSON.errors.book_stock) {
                                        errorMsg = xhr.responseJSON.errors.book_stock[0];
                                    }
                                    Swal.fire({ icon: 'error', title: 'Gagal!', text: errorMsg });
                                }
                            });
                        }
                    });
                });

                $(document).on('click', '.btn-reject', function () {
                    var id = $(this).data('id');
                    window.dispatchEvent(new CustomEvent('open-reject-modal', { detail: { id: id } }));
                });
            });
        </script>
    @endpush
</x-app-layout>