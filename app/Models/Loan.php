<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Loan extends Model
{
    protected $fillable = [
        'loan_request_id',
        'member_id',
        'book_id',
        'borrow_date',
        'return_date',
        'returned_at',
        'status',
        'returned_confirmed_by'
    ];

    protected function casts(): array
    {
        return [
            'borrow_date' => 'date',
            'return_date' => 'date',
            'returned_at' => 'date',
        ];
    }

    public function loanRequest(): BelongsTo
    {
        return $this->belongsTo(LoanRequest::class);
    }
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'returned_confirmed_by');
    }
}
