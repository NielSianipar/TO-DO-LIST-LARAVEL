<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    // 1. Ini method index yang sudah Anda miliki (jangan dihapus)
    public function index() 
    {
        $tasks = Task::all(); 
        return view('tugas', compact('tasks')); 
    }

    // 2. TAMBAHKAN METHOD INI: Koki diajari cara menyimpan data
    public function store(Request $request)
    {
        // Validasi: Pastikan kotaknya tidak kosong
        $request->validate([
            'nama_tugas' => 'required'
        ]);

        // Simpan ke database
        Task::create([
            'nama_tugas' => $request->nama_tugas
        ]);

        // Setelah sukses simpan, otomatis refresh (kembali ke halaman tadi)
        return redirect()->back();
    }

    // Method untuk menghapus tugas
    public function destroy($id)
    {
        // 1. Cari tugas di database berdasarkan ID-nya
        $task = Task::find($id);

        // 2. Hancurkan/Hapus data tersebut
        $task->delete();

        // 3. Kembali ke halaman utama
        return redirect()->back();
    }

    // Method untuk mengupdate status tugas
    public function update($id)
    {
        // 1. Cari tugasnya
        $task = Task::find($id);

        // 2. Ubah statusnya menjadi kebalikannya 
        // (Kalau true jadi false, kalau false jadi true)
        $task->is_selesai = !$task->is_selesai;

        // 3. Simpan perubahan ke database
        $task->save();

        // 4. Kembali ke halaman utama
        return redirect()->back();
    }
}