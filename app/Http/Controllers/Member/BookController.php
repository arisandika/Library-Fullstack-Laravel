<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookController extends Controller
{
    public function index(Request $request): View
    {
        $query = Book::query();

        $query->withCount([
            'loanRequests' => function ($q) {
                $q->where('status', 'queue');
            }
        ]);

        if ($request->filled('search_query')) {
            $search = $request->input('search_query');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('book_code', 'like', "%{$search}%");
            });
        }

        $books = $query->latest()->paginate(12);

        return view('member.books.index', compact('books'));
    }
}
