<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\StoreLoanRequest;
use App\Models\Book;
use App\Services\Member\LoanRequestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoanRequestController extends Controller
{
    protected LoanRequestService $loanRequestService;

    public function __construct(LoanRequestService $loanRequestService)
    {
        $this->loanRequestService = $loanRequestService;
    }

    public function create(Book $book): View
    {
        $allBooks = Book::orderByRaw("id = ? desc", [$book->id])->get();

        return view('member.loans.create', [
            'selectedBook' => $book,
            'allBooks' => $allBooks,
        ]);
    }

    public function store(StoreLoanRequest $request): RedirectResponse
    {
        $this->loanRequestService->store(
            $request->validated(),
            Auth::guard('member')->user()
        );

        return redirect()->route('member.loan-history.index')
            ->with('success', 'Pengajuan peminjaman berhasil dikirim!');
    }
}