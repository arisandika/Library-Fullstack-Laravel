<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RejectLoanRequest;
use App\Models\LoanRequest;
use App\Services\Admin\LoanRequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class LoanRequestController extends Controller
{
    protected LoanRequestService $loanRequestService;

    public function __construct(LoanRequestService $loanRequestService)
    {
        $this->loanRequestService = $loanRequestService;
    }

    public function index(): View
    {
        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Pengajuan Peminjaman', 'url' => null],
        ];

        return view('admin.loan-requests.index', compact('breadcrumbs'));
    }

    public function data(Request $request): JsonResponse
    {
        $query = LoanRequest::with(['member', 'book'])->latest();

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
            ->addColumn('action', function ($row) {
                if ($row->status === 'pending') {
                    return '
                    <div class="flex items-center justify-center gap-1">
                        <button class="p-2 text-green-600 transition-colors rounded-lg btn-approve hover:bg-green-50" data-id="' . $row->id . '" title="Setujui">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                        </button>
                        <button class="p-2 text-red-600 transition-colors rounded-lg btn-reject hover:bg-red-50" data-id="' . $row->id . '" title="Tolak">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>';
                }

                return '<span class="text-xs italic text-zinc-600">Sudah Diproses</span>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function approve(LoanRequest $loanRequest): JsonResponse
    {
        $this->loanRequestService->approve($loanRequest);
        return response()->json(['message' => 'Pengajuan peminjaman berhasil disetujui.']);
    }

    public function reject(RejectLoanRequest $request, LoanRequest $loanRequest): JsonResponse
    {
        $this->loanRequestService->reject($loanRequest, $request->validated());
        return response()->json(['message' => 'Pengajuan peminjaman berhasil ditolak.']);
    }
}
