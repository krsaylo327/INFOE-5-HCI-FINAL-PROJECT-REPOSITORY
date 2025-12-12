<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Middleware\AdminOnly; // Make sure you use the Middleware you created

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Requires Login)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    // --- CORE DASHBOARD (Redirects based on role) ---

    // Can keep the default dashboard but use it as a role-based redirection point
    Route::get('dashboard', function () {
        $user = auth()->user();
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return Inertia::render('Dashboard'); // Student/Faculty default view
    })->name('dashboard');

    // --- THE MAIN APPLICATION ENTRY POINT ---

    // All users go here, but the component logic will handle what they see.
    Route::get('/tickets', function () {
        return Inertia::render('Tickets/TicketIndex');
    })->name('tickets.index');

    // --- TICKET DETAIL VIEW (Access checked by API Policy) ---

    // The frontend can display any ticket, but the API will only provide data
    // if the user is authorized (creator, assigned, or admin).
    Route::get('/tickets/{id}', function ($id) {
        return Inertia::render('Tickets/TicketDetail', ['ticketId' => $id]);
    })->name('tickets.show');

    // --- ADMIN-ONLY SECTION ---

    // Use the custom AdminOnly middleware to protect the route entirely
    Route::middleware([AdminOnly::class])->group(function () {

        Route::get('/admin/dashboard', function () {
            return Inertia::render('Admin/AdminDashboard');
        })->name('admin.dashboard');

        Route::get('/admin/users', function () {
            return Inertia::render('Admin/UserManagement');
        })->name('admin.users.index');
    });
});

require __DIR__.'/settings.php';
// require __DIR__.'/api.php'; // NOTE: API routes are loaded automatically via bootstrap/app.php
