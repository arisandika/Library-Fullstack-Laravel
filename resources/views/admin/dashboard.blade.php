<x-app-layout>
    <x-slot name="title">
        Dashboard
    </x-slot>

    <x-slot name="header">
        <div>
            <h2 class="text-2xl font-semibold text-zinc-800">Welcome back, {{ Auth::user()->name }}</h2>
            <p class="mt-1 text-sm text-zinc-500">Here's what's happening in your library today.</p>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="flex items-start gap-4 p-5 bg-white border rounded-xl border-zinc-200/80">
            <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-orange-50 text-primary">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold tracking-wide uppercase text-zinc-500">Total Buku</p>
                <p class="mt-1 text-2xl font-bold text-zinc-800">{{ number_format($totalBooks) }}</p>
            </div>
        </div>
        <div class="flex items-start gap-4 p-5 bg-white border rounded-xl border-zinc-200/80">
            <div class="flex items-center justify-center w-12 h-12 text-blue-600 rounded-lg bg-blue-50">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold tracking-wide uppercase text-zinc-500">Member Aktif</p>
                <p class="mt-1 text-2xl font-bold text-zinc-800">{{ number_format($activeMembers) }}</p>
            </div>
        </div>
        <div class="flex items-start gap-4 p-5 bg-white border rounded-xl border-zinc-200/80">
            <div class="flex items-center justify-center w-12 h-12 text-green-600 rounded-lg bg-green-50">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold tracking-wide uppercase text-zinc-500">Peminjaman Aktif</p>
                <p class="mt-1 text-2xl font-bold text-zinc-800">{{ number_format($activeLoans) }}</p>
            </div>
        </div>
        <div class="flex items-start gap-4 p-5 bg-white border rounded-xl border-zinc-200/80 {{ $overdueLoansCount > 0 ? 'bg-red-50 border-red-200' : '' }}">
            <div class="flex items-center justify-center w-12 h-12 text-red-600 bg-red-100 rounded-lg">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold tracking-wide uppercase {{ $overdueLoansCount > 0 ? 'text-red-500' : 'text-zinc-500' }}">Keterlambatan</p>
                <p class="mt-1 text-2xl font-bold {{ $overdueLoansCount > 0 ? 'text-red-800' : 'text-zinc-800' }}">{{ number_format($overdueLoansCount) }}</p>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 gap-6 mt-6 lg:grid-cols-12">
        
        <div class="p-5 bg-white border lg:col-span-7 rounded-xl border-zinc-200/80">
            <div class="flex items-center justify-between pb-5">
                <h3 class="font-semibold text-zinc-800">Pengajuan Terbaru</h3>
                <a href="{{ route('admin.loan-requests.index') }}" class="text-sm font-medium transition-colors text-primary hover:text-primary/80">Lihat Semua</a>
            </div>

            <div class="pt-5 overflow-x-auto border-t border-zinc-200">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="text-xs uppercase text-zinc-500 bg-zinc-50">
                            <th class="px-4 py-2.5 font-semibold">No</th>
                            <th class="px-4 py-2.5 font-semibold">Nama</th>
                            <th class="px-4 py-2.5 font-semibold">Judul</th>
                            <th class="px-4 py-2.5 font-semibold">Tgl Ajukan</th>
                            <th class="px-4 py-2.5 font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 text-zinc-800">
                        @forelse ($latestRequests as $request)
                        <tr>
                            <td class="px-4 py-2.5 font-semibold">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-4 py-2.5 font-semibold">{{ $request->member->name ?? '-' }}</td>
                            <td class="px-4 py-2.5 font-semibold">{{ $request->book->title ?? '-' }}</td>
                            <td class="px-4 py-2.5">{{ optional($request->borrow_date)->format('d M Y') ?? '-' }}</td>
                            <td class="px-4 py-2.5">
                                @switch($request->status)
                                    @case('pending')
                                        <span class="inline-block px-2 py-1 text-[11px] font-semibold rounded-md bg-orange-50 text-orange-600">Menunggu</span>
                                        @break
                                    @case('queue')
                                        <span class="inline-block px-2 py-1 text-[11px] font-semibold rounded-md bg-orange-50 text-orange-600">Antrian</span>
                                        @break
                                    @case('returned')
                                        <span class="inline-block px-2 py-1 text-xs font-semibold border rounded-md text-zinc-600 border-zinc-100 bg-zinc-50">Selesai</span>
                                        @break
                                    @case('approved')
                                        <span class="inline-block px-2 py-1 text-[11px] font-semibold rounded-md bg-green-50 text-green-600">Disetujui</span>
                                        @break
                                    @case('rejected')
                                        <span class="inline-block px-2 py-1 text-[11px] font-semibold rounded-md bg-red-50 text-red-600">Ditolak</span>
                                        @break
                                @endswitch
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-zinc-500">Tidak ada pengajuan terbaru.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="p-5 bg-white border lg:col-span-5 rounded-xl border-zinc-200/80">
            <h3 class="pb-5 font-semibold text-zinc-800">Peminjaman Hampir/Terlambat</h3>
            
            <div class="pt-5 overflow-x-auto border-t border-zinc-200">
                @forelse ($urgentLoans as $loan)
                <div class="p-4 border rounded-lg {{ $loan->days_late > 0 ? 'bg-red-50/50 border-red-200' : 'bg-white border-zinc-200' }}">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="font-semibold text-zinc-800">{{ $loan->member->name }}</p>
                            <p class="text-xs text-zinc-500">{{ $loan->book->title }}</p>
                        </div>
                        @if ($loan->days_late > 0)
                                <span class="inline-block px-2 py-1 text-xs font-semibold text-red-600 rounded-md bg-red-50">Telat +{{$loan->days_late}} Hari</span>
                            @else
                                <span class="inline-block px-2 py-1 text-xs font-semibold text-green-600 rounded-md bg-green-50">Aktif</span>
                        @endif
                    </div>
                    <div class="flex items-end justify-between mt-3 text-xs border-t {{ $loan->days_late > 0 ? 'border-red-200/80' : 'border-zinc-200/80' }} pt-2">
                        <p class="text-zinc-500">Kembali: {{ $loan->return_date->format('d M Y') }}</p>
                    </div>
                </div>
                @empty
                    <div class="py-10 text-center">
                        <p class="text-sm text-zinc-500">Tidak ada data peminjaman yang akan atau sudah jatuh tempo.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-5">
                <a href="{{ route('admin.loans.index') }}" class="flex items-center justify-center w-full py-2 text-sm transition-colors rounded-lg text-zinc-600 bg-zinc-100 hover:bg-zinc-200">
                    Tampilkan Semua Data Peminjaman
                </a>
            </div>
        </div>

    </div>
</x-app-layout>