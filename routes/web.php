<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    // Se recomienda agregar un middleware de rol/admin aquí
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Ruta para la gestión de usuarios (panel admin)
    Route::get('/admin/users', function () {
        return view('admin.users');
    })->name('admin.users');
});
