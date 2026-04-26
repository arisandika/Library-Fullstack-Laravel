<x-app-layout>
    <x-slot name="title">
        Manajemen Buku
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
                <h2 class="text-2xl font-semibold text-zinc-800">Manajemen Buku</h2>
                <p class="mt-1 text-sm text-zinc-500">Kelola koleksi buku perpustakaan secara efisien.</p>
            </div>
            <button id="btn-add"
                class="flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors active:scale-95 w-fit">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah Buku
            </button>
        </div>
    </x-slot>

    <div x-data="bookPage()">

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
                        placeholder="Cari judul, penulis, kode buku..." type="text" />
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
                <table id="books-table" class="text-sm text-left">
                    <thead class="w-full">
                        <tr class="border-b border-zinc-200 bg-zinc-50">
                            <th class="w-12 px-5 py-4 text-xs font-semibold tracking-wide uppercase text-zinc-500">No
                            </th>
                            <th
                                class="px-5 py-4 text-xs font-semibold tracking-wide text-center uppercase text-zinc-500">
                                Cover</th>
                            <th class="px-5 py-4 text-xs font-semibold tracking-wide uppercase text-zinc-500">Kode</th>
                            <th class="px-5 py-4 text-xs font-semibold tracking-wide uppercase text-zinc-500">Judul</th>
                            <th class="px-5 py-4 text-xs font-semibold tracking-wide uppercase text-zinc-500">Penulis
                            </th>
                            <th
                                class="px-5 py-4 text-xs font-semibold tracking-wide text-center uppercase text-zinc-500">
                                Tahun</th>
                            <th
                                class="px-5 py-4 text-xs font-semibold tracking-wide text-center uppercase text-zinc-500">
                                Stok</th>
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

        <div id="book-modal"
            class="fixed inset-0 z-50 flex items-start justify-center hidden p-4 overflow-auto bg-zinc-900/50 backdrop-blur-sm scroll-bar-hidden">
            <div class="w-full max-w-3xl bg-white rounded-xl">
                <div class="flex items-center justify-between px-6 py-4 border-b border-zinc-100">
                    <h3 id="modal-title" class="text-base font-semibold text-zinc-800">Tambah Buku Baru</h3>
                    <button id="btn-close-modal"
                        class="p-1 transition-colors rounded-md text-zinc-400 hover:text-zinc-600 hover:bg-zinc-100">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form id="book-form" class="p-6 space-y-4" autocomplete="off" enctype="multipart/form-data">
                    <input type="hidden" id="book_id" name="book_id">

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-12">

                        <div class="space-y-4 md:col-span-8">
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <label for="book_code"
                                        class="block mb-1.5 text-xs font-semibold uppercase tracking-wide text-zinc-500">Kode
                                        Buku</label>
                                    <input type="text" id="book_code" name="book_code"
                                        class="w-full px-3 py-2.5 text-sm text-zinc-800 placeholder-zinc-400 bg-white border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary/60 transition"
                                        placeholder="Contoh: B-001">
                                    <p id="error-book_code" class="hidden mt-1 text-xs text-red-500"></p>
                                </div>
                                <div>
                                    <label for="title"
                                        class="block mb-1.5 text-xs font-semibold uppercase tracking-wide text-zinc-500">Judul
                                        Buku</label>
                                    <input type="text" id="title" name="title"
                                        class="w-full px-3 py-2.5 text-sm text-zinc-800 placeholder-zinc-400 bg-white border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary/60 transition"
                                        placeholder="Contoh: Filosofi Teras">
                                    <p id="error-title" class="hidden mt-1 text-xs text-red-500"></p>
                                </div>
                                <div>
                                    <label for="author"
                                        class="block mb-1.5 text-xs font-semibold uppercase tracking-wide text-zinc-500">Penulis</label>
                                    <input type="text" id="author" name="author"
                                        class="w-full px-3 py-2.5 text-sm text-zinc-800 placeholder-zinc-400 bg-white border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary/60 transition"
                                        placeholder="Nama penulis">
                                    <p id="error-author" class="hidden mt-1 text-xs text-red-500"></p>
                                </div>
                                <div>
                                    <label for="publish_year"
                                        class="block mb-1.5 text-xs font-semibold uppercase tracking-wide text-zinc-500">Tahun
                                        Terbit</label>
                                    <input type="number" id="publish_year" name="publish_year"
                                        class="w-full px-3 py-2.5 text-sm text-zinc-800 placeholder-zinc-400 bg-white border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary/60 transition"
                                        placeholder="2026">
                                    <p id="error-publish_year" class="hidden mt-1 text-xs text-red-500"></p>
                                </div>
                                <div>
                                    <label for="stock"
                                        class="block mb-1.5 text-xs font-semibold uppercase tracking-wide text-zinc-500">Stok</label>
                                    <input type="number" id="stock" name="stock"
                                        class="w-full px-3 py-2.5 text-sm text-zinc-800 placeholder-zinc-400 bg-white border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary/60 transition"
                                        placeholder="0">
                                    <p id="error-stock" class="hidden mt-1 text-xs text-red-500"></p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4 md:col-span-4">
                            <div class="flex flex-col md:col-span-3">
                                <label
                                    class="block mb-1.5 text-xs font-semibold uppercase tracking-wide text-zinc-500">Foto
                                    Cover</label>

                                <div class="relative flex-1 min-h-[300px] flex flex-col items-center justify-center w-full border-2 border-dashed rounded-lg cursor-pointer border-zinc-300 bg-zinc-50 hover:bg-zinc-100 overflow-hidden"
                                    onclick="document.getElementById('image').click()">

                                    <div class="flex flex-col items-center justify-center px-4 pt-5 pb-6 text-center"
                                        id="dropzone-text">
                                        <svg class="w-8 h-8 mb-2 text-zinc-400" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <p class="text-xs text-zinc-500"><span
                                                class="font-semibold text-primary">Klik</span> / Pilih File</p>
                                        <p class="mt-1 text-[10px] text-zinc-400">PNG, JPG (Maks. 2MB)</p>
                                    </div>

                                    <img id="image-preview" class="absolute inset-0 hidden object-cover w-full h-full"
                                        alt="Cover Preview" />

                                    <input id="image" name="image" type="file" class="hidden"
                                        accept="image/png, image/jpeg, image/jpg, image/webp"
                                        onchange="previewImage(this)" />
                                </div>
                                <p id="error-image" class="hidden mt-1 text-xs text-red-500"></p>
                            </div>
                        </div>

                    </div>

                    <div class="flex items-center justify-end gap-2 pt-4 mt-4 border-t border-zinc-100">
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
            #books-table_wrapper>.dt-layout-row:not(:has(table)) {
                display: none !important;
            }

            #books-table {
                width: 100% !important;
            }

            #books-table tbody tr {
                transition: background 0.1s;
            }

            #books-table tbody tr:hover {
                background-color: #fafafa;
            }

            #books-table thead td,
            #books-table tbody td {
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
            function previewImage(input) {
                const preview = document.getElementById('image-preview');
                const dropzoneText = document.getElementById('dropzone-text');

                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        preview.src = e.target.result;
                        preview.classList.remove('hidden');
                        dropzoneText.classList.add('hidden');
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }

            function bookPage() {
                return {
                    searchQuery: '',
                    isSearching: false,
                    debounceTimer: null,
                    filters: {
                        yearFrom: '', yearTo: '',
                        stockStatus: '',
                        createdFrom: '', createdTo: '',
                    },
                    get hasActiveFilters() {
                        return Object.values(this.filters).some(v => v !== '');
                    },
                    get activeFilterCount() {
                        return Object.values(this.filters).filter(v => v !== '').length;
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

                window.dtTable = $('#books-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('admin.books.data') }}",
                        data: function (d) {
                            d.search_query = $('#search-input').val();
                        }
                    },
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center font-semibold' },
                        { data: 'cover', name: 'cover', orderable: false, searchable: false, className: 'text-center' },
                        { data: 'book_code', name: 'book_code', className: 'font-semibold' },
                        { data: 'title', name: 'title', className: 'font-semibold' },
                        { data: 'author', name: 'author', className: '' },
                        { data: 'publish_year', name: 'publish_year', className: 'text-center' },
                        {
                            data: 'stock', name: 'stock', className: 'text-center', render: function (data) {
                                let cls = data == 0
                                    ? 'bg-red-50 text-red-600'
                                    : data < 5
                                        ? 'bg-orange-50 text-orange-600'
                                        : 'bg-green-50 text-green-600';
                                        
                                return '<span class="inline-block px-2 py-1 text-xs font-semibold rounded-md ' + cls + '">' + data + '</span>';
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

                const modal = $('#book-modal');
                const form = $('#book-form');

                function openModal() { modal.removeClass('hidden'); }
                function closeModal() { modal.addClass('hidden'); }
                function clearErrors() {
                    $('[id^="error-"]').text('').addClass('hidden');
                }

                function resetForm() {
                    form[0].reset();
                    $('#book_id').val('');

                    $('#image-preview').attr('src', '').addClass('hidden');
                    $('#dropzone-text').removeClass('hidden');

                    clearErrors();
                }

                function showError(field, msg) {
                    $('#error-' + field).text(msg).removeClass('hidden');
                }

                $('#btn-add').on('click', function () {
                    resetForm(); $('#modal-title').text('Tambah Buku Baru'); openModal();
                });
                $('#btn-close-modal, #btn-cancel-modal').on('click', closeModal);
                modal.on('click', function (e) { if ($(e.target).is(modal)) closeModal(); });

                form.on('submit', function (e) {
                    e.preventDefault();
                    clearErrors();

                    var bookId = $('#book_id').val();
                    var url = bookId ? "{{ url('admin/books') }}/" + bookId : "{{ route('admin.books.store') }}";

                    var formData = new FormData(this);

                    if (bookId) {
                        formData.append('_method', 'PUT');
                    }

                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (res) {
                            closeModal();
                            window.dtTable.draw();
                            Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.message, timer: 2000, showConfirmButton: false });
                        },
                        error: function (xhr) {
                            if (xhr.status === 422) {
                                $.each(xhr.responseJSON.errors, (key, val) => showError(key, val[0]));
                            }
                        }
                    });
                });

                $(document).on('click', '.btn-edit', function () {
                    var bookId = $(this).data('id');
                    $.get("{{ url('admin/books') }}/" + bookId, function (data) {
                        resetForm();
                        $('#modal-title').text('Edit Data Buku');

                        $('#book_id').val(data.id);
                        $('#book_code').val(data.book_code);
                        $('#title').val(data.title);
                        $('#author').val(data.author);
                        $('#publish_year').val(data.publish_year);
                        $('#stock').val(data.stock);

                        if (data.image_url) {
                            $('#image-preview').attr('src', data.image_url).removeClass('hidden');
                            $('#dropzone-text').addClass('hidden');
                        }

                        openModal();
                    });
                });

                $(document).on('click', '.btn-delete', function () {
                    var bookId = $(this).data('id');
                    Swal.fire({
                        title: 'Hapus buku ini?', text: 'Data akan terhapus.',
                        icon: 'warning', showCancelButton: true,
                        confirmButtonColor: '#ef4444', cancelButtonColor: '#a1a1aa',
                        confirmButtonText: 'Ya, hapus', cancelButtonText: 'Batal'
                    }).then(result => {
                        if (result.isConfirmed)
                            $.ajax({
                                url: "{{ url('admin/books') }}/" + bookId, type: 'DELETE',
                                success: res => { window.dtTable.draw(); Swal.fire({ icon: 'success', title: 'Dihapus!', text: res.message, timer: 2000, showConfirmButton: false }); }
                            });
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>