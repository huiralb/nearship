<?php

use Inertia\Inertia;
use App\Services\AgenService;
use App\Services\DistrictService;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgenController;
use App\Http\Controllers\CekOngkirController;
use App\Http\Controllers\VillageController;
use App\Http\Controllers\DistrictController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/cek-ongkir', function () {
        DistrictService::get();
        AgenService::get();
        return Inertia::render('CekOngkir', ['title' => 'Cek Ongkir']);
    })->name('cek-ongkir');

    Route::post('/cek-ongkir', [CekOngkirController::class, 'handle']);
});


Route::get('/api/villages/search', [VillageController::class, 'search']);
Route::get('/api/districts/search', [DistrictController::class, 'search']);
Route::post('/api/districts/agent', [DistrictController::class, 'searchByAgent']);
Route::post('/api/agen/nearby', [AgenController::class, 'nearby']);

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
