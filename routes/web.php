<?php

use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LoanController;
use App\Http\Controllers\Admin\LoanRequestController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Auth\MemberAuthenticatedSessionController;
use App\Http\Controllers\Member\LoanHistoryController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Member\BookController as MemberBookController;
use App\Http\Controllers\Member\LoanRequestController as MemberLoanRequestController;
use App\Http\Controllers\Member\DashboardController as MemberDashboardController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::redirect('/', '/admin/login');

/*
|--------------------------------------------------------------------------
| Admin Routes (Guard 'web')
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';

Route::middleware('auth')
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Dashboard
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::controller(ProfileController::class)->group(function () {
            Route::get('profile', 'edit')->name('profile.edit');
            Route::patch('profile', 'update')->name('profile.update');
            Route::delete('profile', 'destroy')->name('profile.destroy');
        });

        // Book
        Route::get('books/data', [BookController::class, 'data'])->name('books.data');
        Route::resource('books', BookController::class);

        // Member
        Route::get('members/data', [MemberController::class, 'data'])->name('members.data');
        Route::resource('members', MemberController::class);

        // Loan Request
        Route::get('loan-requests', [LoanRequestController::class, 'index'])->name('loan-requests.index');
        Route::get('loan-requests/data', [LoanRequestController::class, 'data'])->name('loan-requests.data');
        Route::post('loan-requests/{loanRequest}/approve', [LoanRequestController::class, 'approve'])->name('loan-requests.approve');
        Route::post('loan-requests/{loanRequest}/reject', [LoanRequestController::class, 'reject'])->name('loan-requests.reject');

        // Loan
        Route::get('loans', [LoanController::class, 'index'])->name('loans.index');
        Route::get('loans/data', [LoanController::class, 'data'])->name('loans.data');
        Route::get('loans/{loan}', [LoanController::class, 'show'])->name('loans.show');
        Route::post('loans/{loan}/return', [LoanController::class, 'returnBook'])->name('loans.return');
    });


/*
|--------------------------------------------------------------------------
| Member Routes (Guard 'member')
|--------------------------------------------------------------------------
*/
Route::prefix('member')->name('member.')->group(function () {
    // Login Member
    Route::middleware('guest:member')->controller(MemberAuthenticatedSessionController::class)->group(function () {
        Route::get('login', 'create')->name('login');
        Route::post('login', 'store');
    });

    // Dashboard Member
    Route::middleware('auth:member')->group(function () {
        Route::get('/dashboard', [MemberDashboardController::class, 'index'])->name('dashboard');

        Route::post('logout', [MemberAuthenticatedSessionController::class, 'destroy'])->name('logout');

        // List Book
        Route::get('books', [MemberBookController::class, 'index'])->name('books.index');

        // Member Loan Request
        Route::get('loan-requests/', [MemberLoanRequestController::class, 'create'])->name('loans.index');
        Route::get('loan-requests/{book}/request', [MemberLoanRequestController::class, 'create'])->name('loans.create');
        Route::post('loan-requests', [MemberLoanRequestController::class, 'store'])->name('loans.store');

        // Loan History
        Route::get('loan-history', [LoanHistoryController::class, 'index'])->name('loan-history.index');
        Route::get('loan-history/data', [LoanHistoryController::class, 'data'])->name('loan-history.data');
    });

});