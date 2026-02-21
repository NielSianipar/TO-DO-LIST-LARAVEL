<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    // 1. Ini method index yang sudah Anda miliki (jangan dihapus)
    public function index(Request $request) 
    {
        $query = Task::where('user_id', auth()->id());
        
        if ($request->has('date') && !empty($request->date)) {
            $query->whereDate('created_at', $request->date);
            $selectedDate = $request->date;
        } else {
            $selectedDate = null;
        }

        $tasks = $query->latest()->get(); 
        return view('tugas', compact('tasks', 'selectedDate')); 
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_tugas' => 'required'
        ]);

        Task::create([
            'nama_tugas' => $request->nama_tugas,
            'user_id' => auth()->id(),
            'status' => 'doing'
        ]);

        return redirect()->back();
    }

    public function destroy($id)
    {
        $task = Task::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        $task->delete();

        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        $task = Task::where('id', $id)->where('user_id', auth()->id())->firstOrFail();

        if ($request->has('status')) {
            $task->status = $request->status;
            if ($request->status === 'done') {
                $task->is_selesai = true;
            } else {
                $task->is_selesai = false;
            }
            $task->save();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true]);
            }
            return redirect()->back();
        }

        // Fallback for toggle
        $task->is_selesai = !$task->is_selesai;
        $task->status = $task->is_selesai ? 'done' : 'doing';
        $task->save();

        return redirect()->back();
    }
}