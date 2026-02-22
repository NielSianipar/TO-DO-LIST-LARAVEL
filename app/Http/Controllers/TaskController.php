<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    // 1. Ini method index yang sudah Anda miliki (jangan dihapus)
    public function index(Request $request) 
    {
        $user = auth()->user();
        
        // Get owned projects and shared projects
        $ownedProjects = $user->projects()->latest()->get();
        $sharedProjects = $user->sharedProjects()->latest()->get();
        
        $projects = $ownedProjects->merge($sharedProjects)->sortByDesc('created_at');
        
        $selectedProjectId = $request->project_id;

        $query = Task::query();
        
        if ($selectedProjectId) {
            $selectedProject = $projects->firstWhere('id', $selectedProjectId);
            
            // Abort if user doesn't have access to this project
            if (!$selectedProject) {
                abort(403);
            }
            
            $query->where('project_id', $selectedProjectId);
        } else {
            // General tasks belong only to the user and have no project
            $query->where('user_id', $user->id)->whereNull('project_id');
            $selectedProject = null;
        }
        
        $tasks = $query->latest()->get(); 
        return view('tugas', compact('tasks', 'projects', 'selectedProjectId', 'selectedProject')); 
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_tugas' => 'required',
            'project_id' => 'nullable|exists:projects,id',
            'assigned_to' => 'nullable|exists:users,id',
            'durasi' => 'nullable|string|max:255'
        ]);
        
        $assigned_to = $request->assigned_to;
        
        if ($request->project_id) {
            $project = \App\Models\Project::find($request->project_id);
            // Jika user adalah member (bukan owner), otomatis assign ke diri sendiri
            if ($project && $project->user_id !== auth()->id()) {
                $assigned_to = auth()->id();
            }
        }

        Task::create([
            'nama_tugas' => $request->nama_tugas,
            'user_id' => auth()->id(),
            'project_id' => $request->project_id,
            'assigned_to' => $assigned_to,
            'durasi' => $request->durasi,
            'status' => 'doing'
        ]);

        return redirect()->back();
    }

    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        
        $user = auth()->user();
        
        // Authorization check: Only task creator, project owner, or assigned user can manage tasks
        if ($task->project_id) {
            $project = \App\Models\Project::findOrFail($task->project_id);
            $isProjectOwner = $project->user_id === $user->id;
            $isCreator = $task->user_id === $user->id;
            $isAssigned = $task->assigned_to === $user->id;
            
            if (!$isProjectOwner && !$isCreator && !$isAssigned) {
                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json(['error' => 'Unauthorized'], 403);
                }
                abort(403, 'Hanya pembuat tugas, ketua project, atau anggota yang ditugaskan yang bisa mengubah ini.');
            }
        } else {
            if ($task->user_id !== $user->id) {
                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json(['error' => 'Unauthorized'], 403);
                }
                abort(403);
            }
        }
        
        $task->delete();

        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        
        $user = auth()->user();
        
        // Authorization check: Only task creator, project owner, or assigned user can manage tasks
        if ($task->project_id) {
            $project = \App\Models\Project::findOrFail($task->project_id);
            $isProjectOwner = $project->user_id === $user->id;
            $isCreator = $task->user_id === $user->id;
            $isAssigned = $task->assigned_to === $user->id;
            
            if (!$isProjectOwner && !$isCreator && !$isAssigned) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['error' => 'Unauthorized'], 403);
                }
                abort(403, 'Hanya pembuat tugas, ketua project, atau anggota yang ditugaskan yang bisa mengubah ini.');
            }
        } else {
            if ($task->user_id !== $user->id) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['error' => 'Unauthorized'], 403);
                }
                abort(403);
            }
        }

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