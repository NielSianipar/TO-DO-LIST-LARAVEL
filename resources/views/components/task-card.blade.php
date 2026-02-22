@php
    $canManage = true;
    if ($task->project_id) {
        $isProjectOwner = $task->project->user_id === auth()->id();
        $isCreator = $task->user_id === auth()->id();
        $isAssigned = $task->assigned_to === auth()->id();
        
        $canManage = $isProjectOwner || $isCreator || $isAssigned;
    } else {
        $canManage = $task->user_id === auth()->id();
    }
@endphp

<div class="task-item group flex flex-col p-3 px-4 rounded-2xl shadow-sm {{ $canManage ? 'hover:shadow-md transition-all duration-300' : 'opacity-80 bg-slate-50 dark:bg-slate-800/40 cursor-not-allowed' }} relative overflow-hidden {{ !$canManage ? 'not-draggable' : '' }}" data-id="{{ $task->id }}">
    <div class="flex items-start justify-between mb-2">
        <div class="flex flex-col">
            <span class="task-text font-semibold text-[0.95rem] leading-snug">
                {{ $task->nama_tugas }}
            </span>
            <div class="flex flex-col gap-1 mt-1.5 opacity-80">
                @if($task->assignedTo)
                <div class="flex items-center gap-1.5">
                    <i data-lucide="user" class="w-3 h-3 text-brand-500 dark:text-brand-400"></i>
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400">{{ $task->assignedTo->name }}</span>
                </div>
                @else
                <div class="flex items-center gap-1.5">
                    <i data-lucide="user-circle" class="w-3 h-3 text-slate-400"></i>
                    <span class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-wider">Unassigned</span>
                </div>
                @endif
                
                @if($task->durasi)
                <div class="flex items-center gap-1.5">
                    <i data-lucide="timer" class="w-3 h-3 text-orange-500 dark:text-orange-400"></i>
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400">{{ $task->durasi }}</span>
                </div>
                @endif
            </div>
        </div>
        
        @if($canManage)
        <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 ml-2 focus-within:opacity-100">
            <form action="/tugas/{{ $task->id }}" method="POST" onsubmit="return confirm('Delete this task?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="task-delete flex items-center justify-center p-1.5 rounded-lg transition-all outline-none focus:ring-2" title="Delete Task">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
            </form>
        </div>
        @endif
    </div>
    <div class="flex items-center gap-2 mt-auto">
        @if($canManage)
            <i data-lucide="grip-horizontal" class="task-grip w-4 h-4 transition-colors cursor-grab active:cursor-grabbing"></i>
        @else
            <i data-lucide="lock" class="w-3.5 h-3.5 text-slate-400/70" title="Locked (Not your task)"></i>
        @endif
        <div class="task-time text-[0.65rem] font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400">{{ $task->created_at->diffForHumans() }}</div>
    </div>
</div>
