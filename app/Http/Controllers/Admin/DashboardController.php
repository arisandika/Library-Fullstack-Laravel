<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Loan;
use App\Models\LoanRequest;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalBooks = Book::count();
        $activeMembers = Member::where('status', 'active')->count();
        
        $activeLoansQuery = Loan::where('status', 'borrowed');
        $activeLoans = $activeLoansQuery->count();
        
        $overdueLoansCount = (clone $activeLoansQuery)->whereDate('return_date', '<', now())->count();

        $latestRequests = LoanRequest::with(['member', 'book'])
            ->latest()
            ->take(5)
            ->get();
            
        $urgentLoans = Loan::with(['member', 'book'])
            ->where('status', 'borrowed')
            ->whereDate('return_date', '<=', now()->addDays(1))
            ->orderBy('return_date', 'asc')
            ->take(5)
            ->get();
            
        $today = now()->startOfDay();
        $urgentLoans->each(function ($loan) use ($today) {
            $returnDate = $loan->return_date->startOfDay();
            if ($today->gt($returnDate)) {
                $loan->days_late = $today->diffInDays($returnDate);
            } else {
                $loan->days_late = 0;
            }
        });

        return view('admin.dashboard', [
            'totalBooks' => $totalBooks,
            'activeMembers' => $activeMembers,
            'activeLoans' => $activeLoans,
            'overdueLoansCount' => $overdueLoansCount,
            'latestRequests' => $latestRequests,
            'urgentLoans' => $urgentLoans,
        ]);
    }
}