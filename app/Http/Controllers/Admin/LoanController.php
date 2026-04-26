<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Services\Admin\LoanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class LoanController extends Controller
{
    protected LoanService $loanService;

    protected int $finePerDay = 5000;

    public function __construct(LoanService $loanService)
    {
        $this->loanService = $loanService;
    }

    public function index(): View
    {
        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Peminjaman Aktif', 'url' => null],
        ];

        return view('admin.loans.index', compact('breadcrumbs'));
    }

    public function data(Request $request): JsonResponse
    {
        $query = Loan::with(['member', 'book'])
            ->latest('borrow_date');

        if ($request->filled('search_query')) {
            $search = $request->input('search_query');
            $query->where(function ($q) use ($search) {
                $q->whereHas('member', function ($mq) use ($search) {
                    $mq->where('name', 'like', "%{$search}%");
                })->orWhereHas('book', function ($bq) use ($search) {
                    $bq->where('title', 'like', "%{$search}%");
                });
            });
        }

        $today = now()->startOfDay();

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('member_name', function ($row) {
                return $row->member->name ?? '-';
            })
            ->addColumn('book_title', function ($row) {
                return $row->book->title ?? '-';
            })
            ->editColumn('borrow_date', function ($row) {
                return $row->borrow_date ? $row->borrow_date->format('d M Y') : '-';
            })
            ->editColumn('return_date', function ($row) {
                return $row->return_date ? $row->return_date->format('d M Y') : '-';
            })
            ->addColumn('lateness', function ($row) use ($today) {
                if (!$row->return_date)
                    return '-';

                if ($row->status === 'returned') {
                    return '<span class="inline-block px-2 py-1 text-xs font-semibold rounded-md bg-zinc-100 text-zinc-600">Selesai</span>';
                }

                $returnDate = clone $row->return_date->startOfDay();

                if ($today->gt($returnDate)) {
                    $daysLate = $today->diffInDays($returnDate);
                    return '<span class="inline-block px-2 py-1 text-xs font-semibold text-red-600 rounded-md bg-red-50">Telat +' . $daysLate . ' hari</span>';

                } elseif ($today->lt($returnDate)) {
                    $daysLeft = $today->diffInDays($returnDate);
                    return '<span class="inline-block px-2 py-1 text-xs font-semibold rounded-md bg-green-50 text-green-600">Sisa ' . $daysLeft . ' hari</span>';

                } else {
                    return '<span class="inline-block px-2 py-1 text-xs font-semibold rounded-md bg-orange-50 text-orange-600">Batas Hari Ini</span>';
                }
            })
            ->addColumn('action', function ($row) {
                if ($row->status === 'borrowed') {
                    return '
                        <button class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors btn-return" data-id="' . $row->id . '">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            Selesaikan
                        </button>';
                }
                return '<span class="text-xs italic text-zinc-600">Sudah selesai</span>';
            })
            ->setRowClass(function ($row) use ($today) {
                if ($row->status === 'borrowed' && $row->return_date && $today->gt($row->return_date->startOfDay())) {
                    return 'bg-red-50/50 hover:bg-red-50 transition-colors duration-150';
                }
                return '';
            })
            ->rawColumns(['lateness', 'status', 'action'])
            ->make(true);
    }

    public function show(Loan $loan): JsonResponse
    {
        $today = now()->startOfDay();
        $returnDate = clone $loan->return_date->startOfDay();

        $daysLate = 0;
        $fine = 0;

        if ($today->gt($returnDate)) {
            $daysLate = $today->diffInDays($returnDate);
            $fine = $daysLate * $this->finePerDay;
        }

        return response()->json([
            'id' => $loan->id,
            'member_name' => $loan->member->name ?? '-',
            'book_title' => $loan->book->title ?? '-',
            'borrow_date' => $loan->borrow_date->format('d M Y'),
            'return_date' => $loan->return_date->format('d M Y'),
            'days_late' => $daysLate,
            'fine' => $fine,
            'formatted_fine' => 'Rp ' . number_format($fine, 0, ',', '.')
        ]);
    }

    public function returnBook(Loan $loan): JsonResponse
    {
        if ($loan->status !== 'borrowed') {
            return response()->json(['message' => 'Buku ini sudah dikembalikan.'], 400);
        }

        $this->loanService->returnBook($loan);

        return response()->json([
            'message' => 'Buku berhasil dikonfirmasi telah dikembalikan, dan stok telah diperbarui.'
        ]);
    }
}