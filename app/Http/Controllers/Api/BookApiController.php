<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BookApiController extends Controller
{
    /**
     * a. GET /api/books
     * Mengambil data semua buku
     */
    public function index()
    {
        $books = Book::all();
        return response()->json([
            'success' => true,
            'message' => 'Daftar semua buku berhasil diambil.',
            'data'    => $books
        ], 200);
    }

    /**
     * b. GET /api/books/{code}
     * Mengambil data buku sesuai dengan kode
     */
    public function show($code)
    {
        $book = Book::where('book_code', $code)->first();

        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Buku dengan kode ' . $code . ' tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail buku ditemukan.',
            'data'    => $book
        ], 200);
    }

    /**
     * c. POST /api/books
     * Membuat buku baru
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'book_code'    => 'required|string|max:50|unique:books,book_code',
            'title'        => 'required|string|max:255',
            'author'       => 'required|string|max:255',
            'publish_year' => 'required|integer',
            'stock'        => 'required|integer|min:0',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $data = $request->all();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('books', $filename, 'public');
            $data['image'] = $filename;
        }

        $book = Book::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Buku baru berhasil ditambahkan.',
            'data'    => $book
        ], 201);
    }

    /**
     * d. PUT /api/books/{code}
     * Mengubah data buku sesuai dengan kode
     */
    public function update(Request $request, $code)
    {
        $book = Book::where('book_code', $code)->first();

        if (!$book) {
            return response()->json(['success' => false, 'message' => 'Buku tidak ditemukan.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'title'        => 'sometimes|required|string|max:255',
            'author'       => 'sometimes|required|string|max:255',
            'publish_year' => 'sometimes|required|integer',
            'stock'        => 'sometimes|required|integer|min:0',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $request->all();

        if ($request->hasFile('image')) {
            if ($book->image) {
                Storage::disk('public')->delete('books/' . $book->image);
            }
            
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('books', $filename, 'public');
            $data['image'] = $filename;
        }

        $book->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Data buku berhasil diperbarui.',
            'data'    => $book
        ], 200);
    }

    /**
     * e. DELETE /api/books/{code}
     * Menghapus data buku sesuai dengan kode
     */
    public function destroy($code)
    {
        $book = Book::where('book_code', $code)->first();

        if (!$book) {
            return response()->json(['success' => false, 'message' => 'Buku tidak ditemukan.'], 404);
        }

        if ($book->image) {
            Storage::disk('public')->delete('books/' . $book->image);
        }

        $book->delete();

        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil dihapus dari sistem.'
        ], 200);
    }
}