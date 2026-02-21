<div class="task-item group flex flex-col p-3 px-4 rounded-2xl bg-white hover:bg-slate-50/90 border border-slate-100 shadow-sm hover:shadow-md transition-all duration-300 relative overflow-hidden" data-id="{{ $task->id }}">
    <div class="flex items-start justify-between mb-2">
        <span class="task-text text-slate-700 font-semibold text-[0.95rem] leading-snug {{ ($task->status === 'done' || $task->is_selesai) ? 'line-through text-slate-400' : '' }}">
            {{ $task->nama_tugas }}
        </span>
        
        <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 ml-2 focus-within:opacity-100">
            <form action="/tugas/{{ $task->id }}" method="POST" onsubmit="return confirm('Delete this task?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="flex items-center justify-center p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all outline-none focus:ring-2 focus:ring-red-200" title="Delete Task">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
            </form>
        </div>
    </div>
    <div class="flex items-center gap-2 mt-auto">
        <i data-lucide="grip-horizontal" class="w-4 h-4 text-slate-300 group-hover:text-slate-500 transition-colors cursor-grab active:cursor-grabbing"></i>
        <div class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest">{{ $task->created_at->diffForHumans() }}</div>
    </div>
</div>
