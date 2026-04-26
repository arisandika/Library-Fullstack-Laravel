<?php

namespace App\Services\Admin;

use App\Models\Book;
use Illuminate\Support\Facades\DB;

class BookService
{
    /**
     * @param array $data
     * @return Book
     */

    public function store(array $data): Book
    {
        return DB::transaction(function () use ($data) {
            return Book::create($data);
        });
    }

    /**
     * @param Book $book
     * @param array $data
     * @return Book
     */

    public function update(Book $book, array $data): Book
    {
        return DB::transaction(function () use ($book, $data) {
            $book->update($data);
            return $book;
        });
    }
}