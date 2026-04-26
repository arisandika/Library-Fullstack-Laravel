<?php

use App\Http\Controllers\Api\BookApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/books', [BookApiController::class, 'index']);
Route::get('/books/{code}', [BookApiController::class, 'show']);
Route::post('/books', [BookApiController::class, 'store']);
Route::put('/books/{code}', [BookApiController::class, 'update']);
Route::delete('/books/{code}', [BookApiController::class, 'destroy']);
