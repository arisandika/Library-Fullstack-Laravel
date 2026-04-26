<?php

namespace App\Services\Member;

use App\Models\Book;
use App\Models\LoanRequest;
use App\Models\Member;
use Illuminate\Validation\ValidationException;

class LoanRequestService
{
    public function store(array $data, Member $member): LoanRequest
    {
        $bookId = $data['book_id'];

        $existingRequest = LoanRequest::where('member_id', $member->id)
            ->where('book_id', $bookId)
            ->whereIn('status', ['pending', 'queue', 'approved'])
            ->exists();

        if ($existingRequest) {
            throw ValidationException::withMessages([
                'book_id' => 'Anda sudah memiliki pengajuan atau sedang meminjam buku ini.',
            ]);
        }

        $book = Book::findOrFail($bookId);
        $status = ($book->stock > 0) ? 'pending' : 'queue';

        $loanRequest = LoanRequest::create([
            'member_id' => $member->id,
            'book_id' => $bookId,
            'borrow_date' => $data['borrow_date'],
            'return_date' => $data['return_date'],
            'rejection_reason' => $data['notes'] ?? null,
            'status' => $status,
        ]);

        return $loanRequest;
    }
}