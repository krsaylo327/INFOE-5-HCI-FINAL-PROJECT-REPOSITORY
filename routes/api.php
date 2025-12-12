<?php

use App\Http\Controllers\TicketController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\AdminUserController;
use App\Http\Middleware\AdminOnly;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ---PROTECTED ROUTES (Requires Authentication for all users) ---

Route::middleware(['auth:sanctum'])->group(function () {

    // CORE TICKET MANAGEMENT (CRUD)
    // index, store, show, update, destroy are all protected by the TicketPolicy.
    Route::resource('tickets', TicketController::class)->except(['create', 'edit']);

    // COMMENT ENDPOINT (Nested Resource)
    // POST /api/tickets/{ticket}/comments
    Route::post('tickets/{ticket}/comments', [CommentController::class, 'store'])->name('comments.store');
});


// ---ADMIN-ONLY ROUTES (Requires Authentication + Admin Role) ---

Route::middleware(['auth:sanctum', AdminOnly::class])->group(function () {

    // USER MANAGEMENT
    Route::get('admin/users', [AdminUserController::class, 'index']);
    Route::patch('admin/users/{user}/role', [AdminUserController::class, 'updateRole']);
});
