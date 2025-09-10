<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\EventSummaryController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\EventPhotoController;

// Redirection de la page d'accueil
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    // Dashboard
    // Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Profil utilisateur
Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    
    // Groups routes
    Route::resource('groups', GroupController::class);
    Route::post('/groups/join', [GroupController::class, 'join'])->name('groups.join');
    Route::delete('/groups/{group}/leave', [GroupController::class, 'leave'])->name('groups.leave');
    Route::delete('/groups/{group}/members/{user}', [GroupController::class, 'removeMember'])->name('groups.remove-member');
    
    // Events routes
    Route::resource('events', EventController::class);
    
    // Votes routes
    Route::post('/event-dates/{eventDate}/vote', [VoteController::class, 'voteDate'])->name('dates.vote');
    Route::delete('/event-dates/{eventDate}/vote', [VoteController::class, 'removeVoteDate'])->name('dates.vote.remove');
    Route::post('/event-activities/{eventActivity}/vote', [VoteController::class, 'voteActivity'])->name('activities.vote');
    Route::delete('/event-activities/{eventActivity}/vote', [VoteController::class, 'removeVoteActivity'])->name('activities.vote.remove');
    
    // Expenses routes
    Route::post('/events/{event}/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::put('/expenses/{expense}', [ExpenseController::class, 'update'])->name('expenses.update');
    Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');
    Route::get('/events/{event}/balance', [ExpenseController::class, 'getBalance'])->name('expenses.balance');
    
    // Event Summary routes - PHASE 3
    Route::get('/events/{event}/summary', [EventSummaryController::class, 'show'])->name('events.summary');
    Route::post('/events/{event}/finalize', [EventSummaryController::class, 'finalize'])->name('events.finalize');
    Route::delete('/events/{event}/finalize', [EventSummaryController::class, 'unfinalize'])->name('events.unfinalize');
    Route::post('/events/{event}/reminders', [EventSummaryController::class, 'sendReminders'])->name('events.reminders');
    Route::get('/events/{event}/export', [EventSummaryController::class, 'export'])->name('events.export');

    Route::post('/events/{event}/photos', [EventPhotoController::class, 'store'])
         ->name('events.photos.store');
    Route::delete('/event-photos/{photo}', [EventPhotoController::class, 'destroy'])
         ->name('event-photos.destroy');
    Route::patch('/event-photos/{photo}/caption', [EventPhotoController::class, 'updateCaption'])
         ->name('event-photos.update-caption');
});

// Route pour vérification automatique des rappels (optionnelle pour cron job)
Route::get('/check-reminders', [EventSummaryController::class, 'checkReminders'])->name('reminders.check');

require __DIR__.'/auth.php';
