<?php

use App\Http\Controllers\EventoVagaController;
use App\Http\Controllers\GaragemController;
use App\Http\Controllers\TelemetriaController;
use Illuminate\Support\Facades\Route;

// Chamado pelo ESP32 (físico ou Wokwi) sempre que o estado muda + heartbeat.
Route::post('/telemetria', TelemetriaController::class)->name('telemetria.store');

// Consumidos pelo dashboard.
Route::get('/garagem', [GaragemController::class, 'show'])->name('garagem.show');
Route::get('/garagem/eventos', [EventoVagaController::class, 'index'])->name('garagem.eventos');
