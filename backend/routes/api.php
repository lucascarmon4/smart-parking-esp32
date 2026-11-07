<?php

use App\Http\Controllers\EstadoController;
use App\Http\Controllers\TelemetriaController;
use App\Http\Middleware\VerificaTokenDoEsp32;
use Illuminate\Support\Facades\Route;

Route::post('/telemetria', TelemetriaController::class)->middleware(VerificaTokenDoEsp32::class);

Route::get('/estado', EstadoController::class);
