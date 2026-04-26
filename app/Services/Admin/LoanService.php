<?php

namespace App\Services\Admin;

use App\Models\Loan;
use App\Models\LoanRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoanService
{
    public function returnBook(Loan $loan): void
    {
        DB::transaction(function () use ($loan) {
            $loan->update([
                'status' => 'returned',
                'returned_at' => now(),
                'returned_confirmed_by' => Auth::id(),
            ]);

            LoanRequest::where('id', $loan->loan_request_id)
                ->update([
                    'status' => 'returned',
                ]);

            $loan->book()->increment('stock');
        });
    }
}