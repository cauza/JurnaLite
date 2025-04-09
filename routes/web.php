<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/popup/journal-entry/{id}', function ($id) {
    $entry = \App\Models\JournalEntry::with('details.account')->findOrFail($id);
    return view('filament.components.journal-entry-popup-view', ['entry' => $entry]);
});
