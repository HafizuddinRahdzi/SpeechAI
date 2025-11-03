<?php

use App\Http\Controllers\FeedbackController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    // Fetch specific columns from the 'users' table
    $passage = DB::table('passages')->select('id', 'title', 'content')->first();

    // Pass the data to the view using compact()
    return view('welcome', compact('passage'));
});

Route::post('/feedback.store', [FeedbackController::class, 'store'])->name('feedback.store');
