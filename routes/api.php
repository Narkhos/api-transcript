<?php

use App\Http\Controllers\TranscriptController;
use Illuminate\Support\Facades\Route;

Route::post(
    '/transcripts',
    [TranscriptController::class, 'store']
);

Route::get(
    '/transcripts',
    [TranscriptController::class, 'index']
);

Route::get(
    '/games',
    [TranscriptController::class, 'games']
);
