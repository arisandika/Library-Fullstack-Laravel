<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LoanRequest extends Model
{
    protected $fillable = [
        'member_id',
        'book_id',
        'borrow_date',
        'return_date',
        'status',
        'rejection_reason',
        'approved_by',
        'approved_at'
    ];

    protected function casts(): array
    {
        return [
            'borrow_date' => 'date',
            'return_date' => 'date',
            'approved_at' => 'datetime',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function loan(): HasOne
    {
        return $this->hasOne(Loan::class);
    }
}
