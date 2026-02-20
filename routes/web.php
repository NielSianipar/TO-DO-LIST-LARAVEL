<?php
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'processLogin']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'processRegister']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Task Routes protected by auth
Route::middleware('auth')->group(function () {
    Route::get('/tugas', [TaskController::class, 'index'])->name('tugas');
    Route::post('/tugas', [TaskController::class, 'store']);
    Route::delete('/tugas/{id}', [TaskController::class, 'destroy']);
    Route::patch('/tugas/{id}', [TaskController::class, 'update']);
});

