<x-app-layout>
    <x-slot name="title">
        Manajemen Anggota
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
                <h2 class="text-2xl font-semibold text-zinc-800">Manajemen Anggota</h2>
                <p class="mt-1 text-sm text-zinc-500">Kelola data keanggotaan perpustakaan, status aktif, dan detail
                    profil.</p>
            </div>
            <button id="btn-add"
                class="flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors active:scale-95 w-fit">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah Anggota
            </button>
        </div>
    </x-slot>

    <div x-data="memberPage()">

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
                        placeholder="Cari nama, nomor anggota, email..." type="text" />
                    <span x-show="isSearching" class="absolute -translate-y-1/2 right-3 top-1/2">
                        <svg class="w-4 h-4 animate-spin text-primary" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                        </svg>
                    </span>
                    <button x-show="searchQuery && !isSearching" @click="clearSearch"
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
                <table id="members-table" class="text-sm text-left">
                    <thead class="w-full">
                        <tr class="border-b border-zinc-200 bg-zinc-50">
                            <th class="w-12 px-5 py-4 text-xs font-semibold tracking-wide uppercase text-zinc-500">No
                            </th>
                            <th class="px-5 py-4 text-xs font-semibold tracking-wide uppercase text-zinc-500">No.
                                Anggota</th>
                            <th class="px-5 py-4 text-xs font-semibold tracking-wide uppercase text-zinc-500">Nama</th>
                            <th class="px-5 py-4 text-xs font-semibold tracking-wide uppercase text-zinc-500">Email</th>
                            <th class="px-5 py-4 text-xs font-semibold tracking-wide uppercase text-zinc-500">No. HP
                            </th>
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

        <div id="member-modal"
            class="fixed inset-0 z-50 flex items-start justify-center hidden p-4 overflow-auto bg-zinc-900/50 backdrop-blur-sm scroll-bar-hidden">
            <div class="w-full max-w-3xl bg-white rounded-xl">
                <div class="flex items-center justify-between px-6 py-4 border-b border-zinc-100">
                    <h3 id="modal-title" class="text-base font-semibold text-zinc-800">Tambah Anggota Baru</h3>
                    <button id="btn-close-modal"
                        class="p-1 transition-colors rounded-md text-zinc-400 hover:text-zinc-600 hover:bg-zinc-100">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form id="member-form" class="p-6 space-y-4" autocomplete="off">
                    <input type="hidden" id="member_id" name="member_id">

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label for="member_number"
                                class="block mb-1.5 text-xs font-semibold uppercase tracking-wide text-zinc-500">No.
                                Anggota</label>
                            <input type="text" id="member_number" name="member_number"
                                class="w-full px-3 py-2.5 text-sm text-zinc-800 placeholder-zinc-400 bg-white border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary/60 transition"
                                placeholder="Contoh: MBR-001">
                            <p id="error-member_number" class="hidden mt-1 text-xs text-red-500"></p>
                        </div>
                        <div>
                            <label for="name"
                                class="block mb-1.5 text-xs font-semibold uppercase tracking-wide text-zinc-500">Nama
                                Lengkap</label>
                            <input type="text" id="name" name="name"
                                class="w-full px-3 py-2.5 text-sm text-zinc-800 placeholder-zinc-400 bg-white border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary/60 transition"
                                placeholder="Nama lengkap anggota">
                            <p id="error-name" class="hidden mt-1 text-xs text-red-500"></p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label for="email"
                                class="block mb-1.5 text-xs font-semibold uppercase tracking-wide text-zinc-500">Email</label>
                            <input type="email" id="email" name="email"
                                class="w-full px-3 py-2.5 text-sm text-zinc-800 placeholder-zinc-400 bg-white border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary/60 transition"
                                placeholder="email@contoh.com">
                            <p id="error-email" class="hidden mt-1 text-xs text-red-500"></p>
                        </div>
                        <div>
                            <label for="password"
                                class="block mb-1.5 text-xs font-semibold uppercase tracking-wide text-zinc-500">Password</label>
                            <input type="password" id="password" name="password"
                                class="w-full px-3 py-2.5 text-sm text-zinc-800 placeholder-zinc-400 bg-white border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary/60 transition"
                                placeholder="Minimal 8 karakter">
                            <p id="password-help" class="hidden mt-1 text-[11px] text-zinc-400">Kosongkan jika tidak
                                ingin mengubah password.</p>
                            <p id="error-password" class="hidden mt-1 text-xs text-red-500"></p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label for="phone"
                                class="block mb-1.5 text-xs font-semibold uppercase tracking-wide text-zinc-500">No.
                                HP</label>
                            <input type="text" id="phone" name="phone"
                                class="w-full px-3 py-2.5 text-sm text-zinc-800 placeholder-zinc-400 bg-white border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary/60 transition"
                                placeholder="Contoh: 081234567890">
                            <p id="error-phone" class="hidden mt-1 text-xs text-red-500"></p>
                        </div>
                        <div>
                            <label for="status"
                                class="block mb-1.5 text-xs font-semibold uppercase tracking-wide text-zinc-500">Status</label>
                            <select id="status" name="status"
                                class="w-full px-3 py-2.5 text-sm text-zinc-800 bg-white border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary/60 transition">
                                <option value="active">Aktif</option>
                                <option value="inactive">Non-Aktif</option>
                            </select>
                            <p id="error-status" class="hidden mt-1 text-xs text-red-500"></p>
                        </div>
                    </div>

                    <div>
                        <label for="address"
                            class="block mb-1.5 text-xs font-semibold uppercase tracking-wide text-zinc-500">Alamat</label>
                        <textarea id="address" name="address" rows="3"
                            class="w-full px-3 py-2.5 text-sm text-zinc-800 placeholder-zinc-400 bg-white border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary/60 transition"
                            placeholder="Alamat lengkap anggota"></textarea>
                        <p id="error-address" class="hidden mt-1 text-xs text-red-500"></p>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-4 border-t border-zinc-100">
                        <button type="button" id="btn-cancel-modal"
                            class="px-4 py-2.5 text-sm font-medium text-zinc-600 bg-zinc-100 border border-zinc-200 rounded-lg hover:bg-zinc-200 transition-colors">
                            Batal
                        </button>
                        <button type="submit" id="btn-save"
                            class="px-4 py-2.5 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors">
                            Simpan Data
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
            #members-table_wrapper>.dt-layout-row:not(:has(table)) {
                display: none !important;
            }

            #members-table {
                width: 100% !important;
            }

            #members-table tbody tr {
                transition: background 0.1s;
            }

            #members-table tbody tr:hover {
                background-color: #fafafa;
            }

            #members-table thead td,
            #members-table tbody td {
                padding: 0.875rem 1.25rem;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            #members-table tbody td.text-center {
                text-align: center;
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
            function memberPage() {
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

                window.dtTable = $('#members-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('admin.members.data') }}",
                        data: function (d) {
                            d.search_query = $('#search-input').val();
                        }
                    },
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center font-semibold' },
                        { data: 'member_number', name: 'member_number', className: 'font-semibold' },
                        { data: 'name', name: 'name', className: 'font-semibold' },
                        { data: 'email', name: 'email', className: 'text-zinc-600' },
                        { data: 'phone', name: 'phone', className: 'text-zinc-600' },
                        {
                            data: 'status', name: 'status', className: 'text-center capitalize', render: function (data) {
                                let statusText = '';
                                let cls = '';

                                if (data == 'active') {
                                    statusText = 'Aktif';
                                    cls = 'bg-green-50 text-green-600';
                                } else if (data == 'inactive') {
                                    statusText = 'Tidak Aktif';
                                    cls = 'bg-red-50 text-red-600';
                                }

                                return '<span class="inline-block px-2 py-1 text-xs font-semibold rounded-md ' + cls + '">' + statusText + '</span>';
                            }
                        },
                        { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center font-semibold' }
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

                const modal = $('#member-modal');
                const form = $('#member-form');

                function openModal() { modal.removeClass('hidden'); }
                function closeModal() { modal.addClass('hidden'); }
                function clearErrors() {
                    $('[id^="error-"]').text('').addClass('hidden');
                }
                function resetForm() {
                    form[0].reset();
                    $('#member_id').val('');
                    $('#password-help').addClass('hidden');
                    clearErrors();
                }
                function showError(field, msg) {
                    $('#error-' + field).text(msg).removeClass('hidden');
                }

                $('#btn-add').on('click', function () {
                    resetForm(); $('#modal-title').text('Tambah Anggota Baru'); openModal();
                });
                $('#btn-close-modal, #btn-cancel-modal').on('click', closeModal);
                modal.on('click', function (e) { if ($(e.target).is(modal)) closeModal(); });

                form.on('submit', function (e) {
                    e.preventDefault(); clearErrors();
                    var memberId = $('#member_id').val();
                    var url = memberId ? "{{ url('admin/members') }}/" + memberId : "{{ route('admin.members.store') }}";
                    var method = memberId ? 'PUT' : 'POST';
                    $.ajax({
                        url, type: method, data: $(this).serialize(),
                        success: function (res) {
                            closeModal(); window.dtTable.draw();
                            Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.message, timer: 2000, showConfirmButton: false });
                        },
                        error: function (xhr) {
                            if (xhr.status === 422)
                                $.each(xhr.responseJSON.errors, (key, val) => showError(key, val[0]));
                        }
                    });
                });

                $(document).on('click', '.btn-edit', function () {
                    var memberId = $(this).data('id');
                    $.get("{{ url('admin/members') }}/" + memberId, function (data) {
                        resetForm();
                        $('#modal-title').text('Edit Data Anggota');
                        $('#password-help').removeClass('hidden');

                        $('#member_id').val(data.id);
                        $('#member_number').val(data.member_number);
                        $('#name').val(data.name);
                        $('#email').val(data.email);
                        $('#phone').val(data.phone);
                        $('#status').val(data.status);
                        $('#address').val(data.address);

                        openModal();
                    });
                });

                $(document).on('click', '.btn-delete', function () {
                    var memberId = $(this).data('id');
                    Swal.fire({
                        title: 'Hapus anggota ini?', text: 'Data akan terhapus permanen.',
                        icon: 'warning', showCancelButton: true,
                        confirmButtonColor: '#ef4444', cancelButtonColor: '#a1a1aa',
                        confirmButtonText: 'Ya, hapus', cancelButtonText: 'Batal'
                    }).then(result => {
                        if (result.isConfirmed)
                            $.ajax({
                                url: "{{ url('admin/members') }}/" + memberId, type: 'DELETE',
                                success: res => { window.dtTable.draw(); Swal.fire({ icon: 'success', title: 'Dihapus!', text: res.message, timer: 2000, showConfirmButton: false }); }
                            });
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>