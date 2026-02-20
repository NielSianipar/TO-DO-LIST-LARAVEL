<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    // 1. Ini method index yang sudah Anda miliki (jangan dihapus)
    public function index() 
    {
        $tasks = Task::where('user_id', auth()->id())->get(); 
        return view('tugas', compact('tasks')); 
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_tugas' => 'required'
        ]);

        Task::create([
            'nama_tugas' => $request->nama_tugas,
            'user_id' => auth()->id()
        ]);

        return redirect()->back();
    }

    public function destroy($id)
    {
        $task = Task::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        $task->delete();

        return redirect()->back();
    }

    public function update($id)
    {
        $task = Task::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        $task->is_selesai = !$task->is_selesai;
        $task->save();

        return redirect()->back();
    }
}