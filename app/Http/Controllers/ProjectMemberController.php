<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\User;

class ProjectMemberController extends Controller
{
    public function store(Request $request, Project $project)
    {
        // Only owner can invite
        if ($project->user_id !== auth()->id()) {
            abort(403, 'Hanya pemilik project yang bisa menambah member.');
        }

        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $user = User::where('email', $request->email)->first();

        // Prevent inviting self
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa mengundang diri sendiri.');
        }

        // Prevent duplicate invitation
        if ($project->members()->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'User sudah ada di project ini.');
        }

        $project->members()->attach($user->id);

        return back()->with('success', 'Member berhasil ditambahkan!');
    }

    public function destroy(Project $project, User $user)
    {
        // Only owner can remove members, or member leaving themselves
        if ($project->user_id !== auth()->id() && auth()->id() !== $user->id) {
            abort(403);
        }

        $project->members()->detach($user->id);

        return back()->with('success', 'Member berhasil dihapus dari project.');
    }
}
