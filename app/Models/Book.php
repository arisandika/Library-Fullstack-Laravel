<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'book_code',
        'title',
        'publish_year',
        'author',
        'stock',
        'image',
    ];

    protected $appends = ['image_url'];

    public function loanRequests(): HasMany
    {
        return $this->hasMany(LoanRequest::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/books/' . $this->image);
        }

        return null;
    }
}
