<?php

use App\Http\Controllers\DistrictController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\VillageController;
use App\Services\DistrictService;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/cek-ongkir', function () {
    DistrictService::get();
    return Inertia::render('CekOngkir', ['title' => 'Cek Ongkir']);
})->name('cek-ongkir');

Route::get('/api/villages/search', [VillageController::class, 'search']);
Route::get('/api/districts/search', [DistrictController::class, 'search']);

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
