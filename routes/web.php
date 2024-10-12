<?php

use App\Controllers\MainController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MainController::class, 'index']);
Route::get('/overrides', [MainController::class, 'overrides']);

Route::get('/override', [MainController::class, 'override']);
Route::post('/override', [MainController::class, 'persistOverride']);
