<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\LoanRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $memberId = Auth::guard('member')->id();
        $today = now()->startOfDay();

        $activeLoansCount = Loan::where('member_id', $memberId)
            ->where('status', 'borrowed')
            ->count();

        $waitingRequestsCount = LoanRequest::where('member_id', $memberId)
            ->whereIn('status', ['pending', 'queue'])
            ->count();

        $totalHistoryCount = Loan::where('member_id', $memberId)->count();

        $overdueCount = Loan::where('member_id', $memberId)
            ->where('status', 'borrowed')
            ->whereDate('return_date', '<', $today)
            ->count();

        $latestRequests = LoanRequest::with('book')
            ->where('member_id', $memberId)
            ->latest()
            ->take(5)
            ->get();

        $activeLoans = Loan::with('book')
            ->where('member_id', $memberId)
            ->where('status', 'borrowed')
            ->orderBy('return_date', 'asc')
            ->take(5)
            ->get();

        $activeLoans->each(function ($loan) use ($today) {
            $returnDate = $loan->return_date->startOfDay();
            $loan->days_late = $today->gt($returnDate) ? $today->diffInDays($returnDate) : 0;
        });

        return view('member.dashboard', compact(
            'activeLoansCount',
            'waitingRequestsCount',
            'totalHistoryCount',
            'overdueCount',
            'latestRequests',
            'activeLoans'
        ));
    }
}