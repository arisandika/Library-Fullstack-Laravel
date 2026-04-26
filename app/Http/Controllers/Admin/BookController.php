<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BookRequest;
use App\Models\Book;
use App\Services\Admin\BookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class BookController extends Controller
{
    protected BookService $bookService;

    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }

    public function index(): View
    {
        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Manajemen Buku', 'url' => null],
        ];

        return view('admin.books.index', compact('breadcrumbs'));
    }

    public function data(Request $request): JsonResponse
    {
        $query = Book::query();

        if ($request->filled('search_query')) {
            $search = $request->input('search_query');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('book_code', 'like', "%{$search}%");
            });
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('cover', function ($book) {
                $imgUrl = $book->image_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($book->title) . '&background=fff7ed&color=d97757';
                return '
                    <div class="flex items-center justify-center">
                        <img src="' . $imgUrl . '" alt="Cover" class="object-cover w-12 h-16 border rounded shadow-sm border-zinc-200">
                    </div>
                ';
            })
            ->addColumn('action', function ($book) {
                return '
                    <button class="p-2 text-blue-600 rounded-lg btn-edit hover:bg-blue-50" data-id="' . $book->id . '">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                    </button>
                    <button class="p-2 text-red-600 rounded-lg btn-delete hover:bg-red-50" data-id="' . $book->id . '">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.134-2.033-2.134H8.033c-1.12 0-2.033.954-2.033 2.134v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                    </button>';
            })
            ->rawColumns(['cover', 'action'])
            ->make(true);
    }

    public function store(BookRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('books', $filename, 'public');
            $data['image'] = $filename;
        }

        $this->bookService->store($data);
        return response()->json(['message' => 'Buku berhasil ditambahkan.'], 201);
    }

    public function show(Book $book): JsonResponse
    {
        return response()->json($book);
    }

    public function update(BookRequest $request, Book $book): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($book->image && Storage::disk('public')->exists('books/' . $book->image)) {
                Storage::disk('public')->delete('books/' . $book->image);
            }

            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('books', $filename, 'public');
            $data['image'] = $filename;
        }

        $this->bookService->update($book, $data);
        return response()->json(['message' => 'Buku berhasil diperbarui.']);
    }

    public function destroy(Book $book): JsonResponse
    {
        if ($book->image && Storage::disk('public')->exists('books/' . $book->image)) {
            Storage::disk('public')->delete('books/' . $book->image);
        }

        $book->delete();
        return response()->json(['message' => 'Buku berhasil dihapus.']);
    }
}
