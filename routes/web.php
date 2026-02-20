<?php
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


// Route Khusus Bagian Tugas
Route::get('/tugas', [TaskController::class, 'index']);
Route::post('/tugas', [TaskController::class, 'store']);
Route::delete('/tugas/{id}', [TaskController::class, 'destroy']);
Route::patch('/tugas/{id}', [TaskController::class, 'update']);

