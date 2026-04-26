<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\LoanRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class LoanHistoryController extends Controller
{
    protected int $finePerDay = 5000;

    public function index(): View
    {
        return view('member.loan-history.index');
    }

    public function data(Request $request): JsonResponse
    {
        $query = LoanRequest::with(['book', 'loan'])
            ->where('member_id', Auth::guard('member')->id())
            ->latest();

        if ($request->filled('search_query')) {
            $search = $request->input('search_query');
            $query->whereHas('book', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            });
        }

        $today = now()->startOfDay();

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('book_title', function ($row) {
                return $row->book->title ?? '-';
            })
            ->editColumn('borrow_date', function ($row) {
                return $row->borrow_date ? $row->borrow_date->format('d M Y') : '-';
            })
            ->editColumn('return_date', function ($row) {
                return $row->return_date ? $row->return_date->format('d M Y') : '-';
            })
            ->addColumn('returned_at', function ($row) {
                if ($row->loan && $row->loan->returned_at) {
                    return $row->loan->returned_at->format('d M Y');
                }
                return '-';
            })
            ->addColumn('fine', function ($row) use ($today) {
                $fine = 0;

                if ($row->loan) {
                    $returnDate = clone $row->return_date->startOfDay();

                    if ($row->loan->status === 'returned') {
                        $returnedAt = clone $row->loan->returned_at->startOfDay();
                        if ($returnedAt->gt($returnDate)) {
                            $daysLate = $returnedAt->diffInDays($returnDate);
                            $fine = $daysLate * $this->finePerDay;
                        }
                    } else if ($row->loan->status === 'borrowed') {
                        if ($today->gt($returnDate)) {
                            $daysLate = $today->diffInDays($returnDate);
                            $fine = $daysLate * $this->finePerDay;
                        }
                    }
                }

                if ($fine > 0) {
                    return '<span class="font-semibold text-red-600">Rp ' . number_format($fine, 0, ',', '.') . '</span>';
                }
                return '<span class="text-zinc-400">-</span>';
            })
            ->addColumn('combined_status', function ($row) {
                if ($row->status === 'approved' && $row->loan) {
                    return $row->loan->status;
                }
                return $row->status;
            })
            ->setRowClass(function ($row) use ($today) {
                if ($row->status === 'approved' && $row->loan && $row->loan->status === 'borrowed') {
                    $returnDate = clone $row->return_date->startOfDay();
                    if ($today->gt($returnDate)) {
                        return 'bg-red-50/50 hover:bg-red-50 transition-colors duration-150';
                    }
                }
                return '';
            })
            ->rawColumns(['fine', 'combined_status'])
            ->make(true);
    }
}