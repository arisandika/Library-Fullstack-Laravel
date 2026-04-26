<?php

namespace App\Services\Admin;

use App\Models\Loan;
use App\Models\LoanRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LoanRequestService
{
    public function approve(LoanRequest $loanRequest): void
    {
        if ($loanRequest->book->stock < 1) {
            throw ValidationException::withMessages([
                'book_stock' => 'Gagal menyetujui, stok buku saat ini sedang kosong.'
            ]);
        }

        DB::transaction(function () use ($loanRequest) {
            $loanRequest->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            Loan::create([
                'loan_request_id' => $loanRequest->id,
                'member_id' => $loanRequest->member_id,
                'book_id' => $loanRequest->book_id,
                'borrow_date' => $loanRequest->borrow_date,
                'return_date' => $loanRequest->return_date,
                'status' => 'borrowed',
            ]);

            $loanRequest->book()->decrement('stock');

        });
    }

    public function reject(LoanRequest $loanRequest, array $data): void
    {
        $loanRequest->update([
            'status' => 'rejected',
            'rejection_reason' => $data['rejection_reason'],
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);
    }
}