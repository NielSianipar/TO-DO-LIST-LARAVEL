<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TaskMaster - Modern To-Do List</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; transition: background-color 0.3s, color 0.3s; }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            transition: all 0.3s ease;
        }
        
        .dark .glass-card {
            background: rgba(10, 15, 30, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.5);
        }

        .bg-light-theme {
            background: linear-gradient(-45deg, #f8fafc, #eef2ff, #f1f5f9, #e0e7ff);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
        }
        
        .dark .bg-dark-theme {
            background: linear-gradient(-45deg, #020617, #000000, #0a0a0a, #0f172a);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        .task-item {
            animation: slideIn 0.3s ease-out forwards;
            cursor: grab;
        }
        .task-item:active {
            cursor: grabbing;
        }
        .sortable-ghost {
            opacity: 0.4;
            background: #e2e8f0;
            border: 2px dashed #94a3b8;
            box-shadow: none;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        /* Column scrollbars */
        .kanban-col::-webkit-scrollbar { width: 4px; }
    </style>
    <style type="text/tailwindcss">
        @layer components {
            #col-doing .task-item { @apply bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 border-l-4 border-l-slate-500 shadow-md; }
            #col-doing .task-text { @apply text-slate-800 dark:text-slate-200 font-bold; }
            #col-doing .task-time { @apply text-slate-500 dark:text-slate-400; }
            #col-doing .task-grip { @apply text-slate-400 dark:text-slate-500 group-hover:text-slate-600 dark:group-hover:text-slate-300; }
            #col-doing .task-delete { @apply text-slate-400 dark:text-slate-500 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/30 focus:ring-red-300; }
            
            #col-on_progress .task-item { @apply bg-blue-50 dark:bg-blue-900/40 hover:bg-blue-100 dark:hover:bg-blue-900/60 border border-blue-200 dark:border-blue-800 border-l-4 border-l-blue-600 shadow-md; }
            #col-on_progress .task-text { @apply text-blue-900 dark:text-blue-100 font-bold; }
            #col-on_progress .task-time { @apply text-blue-700 dark:text-blue-300; }
            #col-on_progress .task-grip { @apply text-blue-500 dark:text-blue-400 group-hover:text-blue-700 dark:group-hover:text-blue-200; }
            #col-on_progress .task-delete { @apply text-blue-500 dark:text-blue-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/30 focus:ring-red-300; }
            
            #col-done .task-item { @apply bg-emerald-50 dark:bg-emerald-900/40 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 border border-emerald-200 dark:border-emerald-800 border-l-4 border-l-emerald-600 shadow-md; }
            #col-done .task-text { @apply text-emerald-900/70 dark:text-emerald-100/70 font-bold line-through; }
            #col-done .task-time { @apply text-emerald-700 dark:text-emerald-400; }
            #col-done .task-grip { @apply text-emerald-500 dark:text-emerald-400 group-hover:text-emerald-700 dark:group-hover:text-emerald-200; }
            #col-done .task-delete { @apply text-emerald-500 dark:text-emerald-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/30 focus:ring-red-300; }
        }
    </style>
    <script>
        // Check theme before render to prevent FOUC
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                        }
                    },
                    boxShadow: {
                        'glow': '0 0 20px rgba(99, 102, 241, 0.4)',
                        'soft': '0 10px 40px -10px rgba(0,0,0,0.08)',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-light-theme dark:bg-dark-theme min-h-screen text-slate-800 dark:text-slate-200 p-4 md:p-8 flex items-center justify-center selection:bg-brand-500 selection:text-white transition-colors duration-500">

    @php
        $total = count($tasks ?? []);
        $completed = 0;
        foreach($tasks ?? [] as $t) {
            if($t->status === 'done' || $t->is_selesai) $completed++;
        }
        $pending = $total - $completed;
        $progress = $total > 0 ? round(($completed / $total) * 100) : 0;
        
        $greetings = ['Good Morning', 'Good Afternoon', 'Good Evening'];
        $hour = date('H');
        $greeting = $hour < 12 ? $greetings[0] : ($hour < 18 ? $greetings[1] : $greetings[2]);

        $doingTasks = collect($tasks)->filter(function($t) { return $t->status === 'doing' || (!$t->status && !$t->is_selesai); });
        $onProgressTasks = collect($tasks)->filter(function($t) { return $t->status === 'on_progress'; });
        $doneTasks = collect($tasks)->filter(function($t) { return $t->status === 'done' || $t->is_selesai && $t->status !== 'on_progress' && $t->status !== 'doing'; });
    @endphp

    <div class="w-full max-w-[1400px] mx-auto grid grid-cols-1 xl:grid-cols-12 gap-6 lg:gap-8">
        
        <!-- Sidebar Dashboard -->
        <div class="glass-card rounded-[2rem] p-8 shadow-soft xl:col-span-3 flex flex-col relative overflow-hidden group">
            <div class="absolute -top-24 -right-24 w-48 h-48 bg-brand-400 rounded-full mix-blend-multiply dark:mix-blend-lighten filter blur-3xl opacity-30 dark:opacity-20 group-hover:opacity-50 dark:group-hover:opacity-40 transition-opacity duration-700"></div>
            <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-purple-400 rounded-full mix-blend-multiply dark:mix-blend-lighten filter blur-3xl opacity-30 dark:opacity-20 group-hover:opacity-50 dark:group-hover:opacity-40 transition-opacity duration-700"></div>
            
            <div class="relative z-10 flex-1 flex flex-col">
                <div class="flex items-center gap-3 mb-10">
                    <div class="p-3 bg-gradient-to-br from-brand-500 to-purple-600 rounded-2xl shadow-lg flex items-center justify-center">
                        <i data-lucide="trello" class="text-white w-6 h-6"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">TaskBoard</h1>
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-300 uppercase tracking-wider">Workspace</p>
                    </div>
                </div>

                <div class="space-y-4 mb-auto">
                    <!-- Projects Section -->
                    <div class="bg-white/60 dark:bg-slate-800/60 p-5 rounded-2xl border border-white/80 dark:border-white/10 shadow-sm backdrop-blur-md">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200">Projects</h3>
                            <i data-lucide="folder" class="w-4 h-4 text-slate-400 dark:text-slate-500"></i>
                        </div>
                        
                        <div class="space-y-1 mb-4 max-h-[160px] overflow-y-auto pr-1" style="scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent;">
                            <a href="{{ route('tugas') }}" class="flex items-center gap-3 p-2 rounded-xl transition-all {{ !$selectedProjectId ? 'bg-brand-50 dark:bg-brand-900/40 text-brand-700 dark:text-brand-300 font-bold shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700/50' }}">
                                <div class="w-2 h-2 rounded-full {{ !$selectedProjectId ? 'bg-brand-500' : 'bg-slate-300 dark:bg-slate-600' }}"></div>
                                <span class="text-sm">General Tasks</span>
                            </a>
                            @foreach($projects as $project)
                            <div class="flex items-center justify-between group">
                                <a href="{{ route('tugas', ['project_id' => $project->id]) }}" class="flex-1 flex items-center gap-3 p-2 rounded-xl transition-all {{ $selectedProjectId == $project->id ? 'bg-brand-50 dark:bg-brand-900/40 text-brand-700 dark:text-brand-300 font-bold shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700/50' }}">
                                    <div class="w-2 h-2 rounded-full {{ $selectedProjectId == $project->id ? 'bg-brand-500' : 'bg-slate-300 dark:bg-slate-600' }}"></div>
                                    <span class="text-sm truncate">{{ $project->name }}</span>
                                </a>
                                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    @if($project->user_id === auth()->id())
                                    <form action="{{ route('projects.destroy', $project->id) }}" method="POST" onsubmit="return confirm('Hapus project ini beserta tugas di dalamnya?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-400 dark:text-slate-500 hover:text-red-500 dark:hover:text-red-400" title="Delete Project">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </form>
                                    @else
                                    <form action="{{ route('projects.members.destroy', [$project->id, auth()->id()]) }}" method="POST" onsubmit="return confirm('Keluar dari project ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-400 dark:text-slate-500 hover:text-orange-500 dark:hover:text-orange-400" title="Leave Project">
                                            <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <form action="{{ route('projects.store') }}" method="POST" class="relative group">
                            @csrf
                            <input type="text" name="name" placeholder="New Project..." required class="w-full pl-4 pr-10 py-2 bg-white dark:bg-slate-900 border-2 border-transparent text-slate-800 dark:text-slate-200 text-sm rounded-xl focus:outline-none focus:border-brand-500 transition-all shadow-sm placeholder:text-slate-400 dark:placeholder:text-slate-500">
                            <button type="submit" class="absolute right-1.5 top-1.5 bottom-1.5 p-1 bg-slate-100 dark:bg-slate-800 hover:bg-brand-100 dark:hover:bg-brand-900/50 text-brand-600 dark:text-brand-400 rounded-lg transition-colors flex items-center justify-center">
                                <i data-lucide="plus" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>

                    <div class="bg-white/60 dark:bg-slate-800/80 p-5 rounded-2xl border border-white/80 dark:border-white/10 shadow-sm backdrop-blur-md">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-sm font-bold text-slate-500 dark:text-slate-300">Total Tasks</h3>
                            <i data-lucide="layers" class="w-4 h-4 text-slate-400 dark:text-slate-400"></i>
                        </div>
                        <p class="text-4xl font-extrabold text-slate-800 dark:text-white">{{ $total }}</p>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-white/60 dark:bg-slate-800/80 p-4 rounded-2xl border border-white/80 dark:border-white/10 shadow-sm backdrop-blur-md">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-xs font-bold text-slate-500 dark:text-slate-300">Done</h3>
                                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500 dark:text-emerald-400"></i>
                            </div>
                            <p class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400" id="stat-done">{{ $completed }}</p>
                        </div>
                        <div class="bg-white/60 dark:bg-slate-800/80 p-4 rounded-2xl border border-white/80 dark:border-white/10 shadow-sm backdrop-blur-md">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-xs font-bold text-slate-500 dark:text-slate-300">Pending</h3>
                                <i data-lucide="clock" class="w-4 h-4 text-orange-500 dark:text-orange-400"></i>
                            </div>
                            <p class="text-2xl font-extrabold text-orange-600 dark:text-orange-400" id="stat-pending">{{ $pending }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-10 relative z-10 bg-white/40 dark:bg-slate-800/40 p-5 rounded-2xl border border-white/60 dark:border-white/10 shadow-sm">
                    <div class="flex justify-between items-end mb-3">
                        <div>
                            <span class="text-sm font-bold text-slate-700 dark:text-slate-300 block">Overall Progress</span>
                            <span class="text-xs font-medium text-slate-500 dark:text-slate-400" id="stat-text"><span id="stat-completed">{{ $completed }}</span> of {{ $total }} completed</span>
                        </div>
                        <span class="text-lg font-black text-brand-600" id="stat-percent">{{ $progress }}%</span>
                    </div>
                    <div class="w-full bg-slate-200/50 dark:bg-slate-700/50 rounded-full h-2.5 overflow-hidden">
                        <div id="stat-progress-bar" class="bg-gradient-to-r from-brand-500 to-purple-500 h-2.5 rounded-full transition-all duration-1000 ease-out" style="width: {{ $progress }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="glass-card rounded-[2rem] p-6 lg:p-8 shadow-soft xl:col-span-9 flex flex-col relative overflow-hidden bg-white/60 dark:bg-slate-800/60 h-[85vh] lg:h-auto">
            
            <!-- Header section -->
            <div class="flex justify-between items-start mb-6">
                <div>
                    <div class="flex items-center gap-3">
                        <h2 class="text-3xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight">{{ $greeting }}! <span class="text-2xl">😎</span></h2>
                        @if($selectedProject)
                            <span class="px-3 py-1 bg-brand-100 dark:bg-brand-900/60 text-brand-700 dark:text-brand-300 text-sm font-bold rounded-full border border-brand-200 dark:border-brand-800 shadow-sm flex items-center gap-1.5">
                                <i data-lucide="folder" class="w-3.5 h-3.5"></i> 
                                {{ $selectedProject->name }}
                                @if($selectedProject->user_id !== auth()->id())
                                    <span class="ml-1 text-xs opacity-75">(Shared Dashboard)</span>
                                @endif
                            </span>
                        @else
                            <span class="px-3 py-1 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-sm font-bold rounded-full border border-slate-200 dark:border-slate-600 shadow-sm flex items-center gap-1.5"><i data-lucide="layers" class="w-3.5 h-3.5"></i> General Tasks</span>
                        @endif
                    </div>
                    <p class="text-slate-500 dark:text-slate-300 font-medium mt-1 flex items-center gap-2">
                        <i data-lucide="calendar" class="w-4 h-4 text-brand-500 dark:text-brand-400"></i>
                        <span class="dark:text-slate-100">{{ now()->format('l, j F Y') }}</span>
                    </p>
                    <p class="text-slate-400 dark:text-slate-400 font-medium mt-2 flex items-center gap-2 text-sm bg-white/50 dark:bg-slate-800/80 px-3 py-1.5 rounded-lg w-fit border border-slate-100 dark:border-slate-700/50 shadow-sm">
                        <i data-lucide="clock" class="w-4 h-4 text-brand-500 dark:text-brand-400"></i>
                        <span id="realtime-clock" class="font-bold text-slate-700 dark:text-slate-100 text-base tracking-wide">Memuat waktu...</span> <span class="dark:text-slate-300 font-semibold">WIB</span>
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <button id="theme-toggle" class="flex items-center justify-center p-2.5 bg-white/80 dark:bg-slate-800/80 text-slate-600 dark:text-slate-300 hover:text-brand-500 border border-white dark:border-slate-700 rounded-xl transition-all duration-300 shadow-sm hover:shadow-md" title="Toggle Theme">
                        <i data-lucide="moon" class="w-5 h-5 hidden dark:block"></i>
                        <i data-lucide="sun" class="w-5 h-5 block dark:hidden"></i>
                    </button>
                    <div class="hidden sm:flex items-center gap-2 bg-white/80 dark:bg-slate-800/80 px-4 py-2.5 rounded-xl shadow-sm border border-white dark:border-slate-700">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.8)] animate-pulse"></div>
                        <span class="text-sm font-bold text-slate-700 dark:text-slate-200">{{ Auth::user()->name }}</span>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="flex items-center justify-center p-2.5 bg-white/80 dark:bg-slate-800/80 hover:bg-red-500 dark:hover:bg-red-600 text-slate-600 dark:text-slate-300 hover:text-white border border-white dark:border-slate-700 rounded-xl transition-all duration-300 shadow-sm hover:shadow-md" title="Keluar">
                            <i data-lucide="log-out" class="w-5 h-5"></i>
                        </button>
                    </form>
                </div>
            </div>
            
            @if(session('success'))
                <div class="mb-4 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 p-4 rounded-xl text-sm font-bold border border-emerald-200 dark:border-emerald-800 flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 p-4 rounded-xl text-sm font-bold border border-red-200 dark:border-red-800 flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4"></i>
                    {{ session('error') }}
                </div>
            @endif
            @error('email')
                <div class="mb-4 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 p-4 rounded-xl text-sm font-bold border border-red-200 dark:border-red-800 flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4"></i>
                    {{ $message }}
                </div>
            @enderror

            <!-- Controls block: Filter & Add Task -->
            <div class="flex flex-col sm:flex-row gap-4 mb-6 shrink-0 z-10 w-full">
                <!-- Trigger Modal Button -->
                <button 
                    onclick="document.getElementById('taskModal').classList.remove('hidden'); document.getElementById('taskModal').classList.add('flex');"
                    class="flex-1 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 border-2 border-dashed border-slate-300 dark:border-slate-700 text-slate-500 dark:text-slate-400 rounded-2xl py-4 px-6 focus:outline-none transition-all shadow-sm hover:shadow-md hover:border-brand-500 hover:text-brand-500 text-lg font-semibold flex items-center gap-3"
                >
                    <i data-lucide="plus-circle" class="w-6 h-6"></i>
                    Add a new task...
                </button>
                
                @if($selectedProject && $selectedProject->user_id === auth()->id())
                <!-- Add Member Form (Visible only for Project Owner) -->
                <form action="{{ route('projects.members.store', $selectedProject->id) }}" method="POST" class="relative group sm:w-1/3 min-w-[300px]">
                    @csrf
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i data-lucide="users" class="text-slate-400 dark:text-slate-500 group-focus-within:text-brand-500 transition-colors w-5 h-5"></i>
                    </div>
                    <input 
                        type="email" 
                        name="email" 
                        placeholder="Invite member by email..." 
                        required
                        class="w-full pl-12 pr-28 py-4 bg-white/70 dark:bg-slate-800/80 border-2 border-transparent text-slate-800 dark:text-slate-100 rounded-2xl focus:outline-none focus:ring-0 focus:border-brand-500 transition-all shadow-sm text-sm font-semibold placeholder:text-slate-400 dark:placeholder:text-slate-500"
                    >
                    <button 
                        type="submit" 
                        class="absolute right-2 top-2 bottom-2 bg-brand-500 hover:bg-brand-600 text-white font-bold px-4 rounded-xl transition-all duration-300 shadow-md text-sm flex items-center gap-1.5"
                    >
                        <i data-lucide="user-plus" class="w-4 h-4"></i> <span>Invite</span>
                    </button>
                </form>
                @endif
            </div>

            <!-- Team Members Bar -->
            @if($selectedProject && $selectedProject->members->count() > 0)
            <div class="flex flex-wrap items-center gap-2 mb-6 p-3 bg-white/40 dark:bg-slate-800/40 rounded-2xl border border-slate-200/50 dark:border-slate-700/50">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 px-2 uppercase tracking-wider flex items-center gap-1.5"><i data-lucide="users" class="w-3.5 h-3.5"></i> Team Members:</span>
                
                <!-- Owner Badge -->
                <div class="flex items-center gap-1.5 py-1 px-3 bg-brand-50 dark:bg-brand-900/30 text-brand-700 dark:text-brand-300 rounded-lg border border-brand-200/50 dark:border-brand-800/50 text-xs font-bold shadow-sm">
                    <i data-lucide="crown" class="w-3.5 h-3.5 text-yellow-500"></i>
                    {{ $selectedProject->user->name }}
                </div>
                
                <!-- Members -->
                @foreach($selectedProject->members as $member)
                <div class="flex items-center gap-1 py-1 pl-3 pr-1 bg-white/80 dark:bg-slate-700/80 text-slate-700 dark:text-slate-200 rounded-lg border border-slate-200 dark:border-slate-600 text-xs font-semibold shadow-sm group">
                    {{ $member->name }}
                    
                    @if($selectedProject->user_id === auth()->id())
                    <form action="{{ route('projects.members.destroy', [$selectedProject->id, $member->id]) }}" method="POST" onsubmit="return confirm('Hapus {{ $member->name }} dari project ini?')" class="ml-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-1 text-slate-400 dark:text-slate-400 hover:text-red-500 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-md transition-colors" title="Remove Member">
                            <i data-lucide="x" class="w-3 h-3"></i>
                        </button>
                    </form>
                    @endif
                </div>
                @endforeach
            </div>
            @endif

            <!-- Kanban Board -->
            <div class="flex-1 min-h-0 overflow-x-auto overflow-y-hidden pb-2 -mx-2 px-2">
                <div class="flex gap-6 h-full min-w-[900px]">
                    
                    <!-- Doing Column -->
                    <div class="flex flex-col w-1/3 bg-slate-100/60 dark:bg-slate-800/60 rounded-3xl p-4 border border-slate-200/80 dark:border-slate-700/60 shadow-inner h-full">
                        <div class="flex items-center justify-between mb-4 px-2">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full bg-slate-400 dark:bg-slate-500"></div>
                                <h3 class="text-sm font-extrabold text-slate-700 dark:text-slate-100 uppercase tracking-wider">Doing</h3>
                            </div>
                            <span class="bg-slate-200 dark:bg-slate-700/80 text-slate-600 dark:text-slate-200 text-xs font-bold px-2 py-1 rounded-full count-badge">{{ $doingTasks->count() }}</span>
                        </div>
                        <div id="col-doing" class="kanban-col flex-1 overflow-y-auto space-y-3 p-1 min-h-[150px]" data-status="doing">
                            @foreach($doingTasks as $task)
                                @include('components.task-card', ['task' => $task])
                            @endforeach
                        </div>
                    </div>

                    <!-- On Progress Column -->
                    <div class="flex flex-col w-1/3 bg-blue-50/60 dark:bg-blue-900/40 rounded-3xl p-4 border border-blue-100/80 dark:border-blue-800/60 shadow-inner h-full">
                        <div class="flex items-center justify-between mb-4 px-2">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full bg-blue-500 animate-pulse"></div>
                                <h3 class="text-sm font-extrabold text-blue-800 dark:text-blue-100 uppercase tracking-wider">On Progress</h3>
                            </div>
                            <span class="bg-blue-200 dark:bg-blue-800/80 text-blue-800 dark:text-blue-100 text-xs font-bold px-2 py-1 rounded-full count-badge">{{ $onProgressTasks->count() }}</span>
                        </div>
                        <div id="col-on_progress" class="kanban-col flex-1 overflow-y-auto space-y-3 p-1 min-h-[150px]" data-status="on_progress">
                            @foreach($onProgressTasks as $task)
                                @include('components.task-card', ['task' => $task])
                            @endforeach
                        </div>
                    </div>

                    <!-- Done Column -->
                    <div class="flex flex-col w-1/3 bg-emerald-50/60 dark:bg-emerald-900/40 rounded-3xl p-4 border border-emerald-100/80 dark:border-emerald-800/60 shadow-inner h-full">
                        <div class="flex items-center justify-between mb-4 px-2">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                                <h3 class="text-sm font-extrabold text-emerald-800 dark:text-emerald-100 uppercase tracking-wider">Done</h3>
                            </div>
                            <span class="bg-emerald-200 dark:bg-emerald-800/80 text-emerald-800 dark:text-emerald-100 text-xs font-bold px-2 py-1 rounded-full count-badge">{{ $doneTasks->count() }}</span>
                        </div>
                        <div id="col-done" class="kanban-col flex-1 overflow-y-auto space-y-3 p-1 min-h-[150px]" data-status="done">
                            @foreach($doneTasks as $task)
                                @include('components.task-card', ['task' => $task])
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <!-- Task Creation Modal -->
    <div id="taskModal" class="fixed inset-0 z-50 hidden items-center justify-center">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeTaskModal()"></div>
        
        <!-- Modal Content -->
        <div class="relative w-full max-w-lg mx-4 bg-white dark:bg-slate-900 rounded-[2rem] shadow-2xl overflow-hidden border border-slate-200/50 dark:border-slate-700/50 animate-[slideIn_0.3s_ease-out]">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50">
                <h3 class="text-xl font-extrabold text-slate-800 dark:text-white flex items-center gap-2">
                    <i data-lucide="plus-circle" class="w-5 h-5 text-brand-500"></i>
                    Create New Task
                </h3>
                <button onclick="closeTaskModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <!-- Body -->
            <form action="/tugas" method="POST" class="p-6">
                @csrf
                @if($selectedProjectId)
                    <input type="hidden" name="project_id" value="{{ $selectedProjectId }}">
                @endif
                
                <div class="space-y-5">
                    <!-- Task Name -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5 ml-1">Task Title <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i data-lucide="edit-3" class="text-slate-400 w-5 h-5"></i>
                            </div>
                            <input 
                                type="text" 
                                name="nama_tugas" 
                                placeholder="What needs to be done?" 
                                autocomplete="off"
                                required
                                class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-800 border self-center border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all font-semibold placeholder:text-slate-400 dark:placeholder:text-slate-500"
                            >
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Assignee -->
                        @if($selectedProject && $selectedProject->user_id === auth()->id())
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5 ml-1">Assign To</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i data-lucide="user" class="text-slate-400 w-4 h-4"></i>
                                </div>
                                <select name="assigned_to" class="w-full pl-9 pr-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all font-semibold appearance-none cursor-pointer text-sm">
                                    <option value="">👤 Unassigned</option>
                                    <option value="{{ $selectedProject->user->id }}">👑 {{ $selectedProject->user->name }}</option>
                                    @foreach($selectedProject->members as $member)
                                        <option value="{{ $member->id }}">👤 {{ $member->name }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <i data-lucide="chevron-down" class="text-slate-400 w-4 h-4"></i>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <!-- Duration -->
                        <div class="{{ (!$selectedProject || $selectedProject->user_id !== auth()->id()) ? 'sm:col-span-2' : '' }}">
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5 ml-1">Duration Estimation</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i data-lucide="timer" class="text-slate-400 w-4 h-4"></i>
                                </div>
                                <input 
                                    type="text" 
                                    name="durasi" 
                                    placeholder="e.g. 2 Jam, 3 Hari" 
                                    class="w-full pl-9 pr-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all font-semibold placeholder:text-slate-400 dark:placeholder:text-slate-500 text-sm"
                                >
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-8 flex gap-3">
                    <button type="button" onclick="closeTaskModal()" class="flex-1 py-3 px-4 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 py-3 px-4 bg-brand-600 hover:bg-brand-500 text-white font-bold rounded-xl transition-colors shadow-md hover:shadow-lg hover:shadow-brand-500/30">
                        Create Task
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Init Lucide Icons -->
    <script>
        lucide.createIcons();
        
        // Modal functions
        function closeTaskModal() {
            const modal = document.getElementById('taskModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Theme Toggle Logic
        const themeToggleBtn = document.getElementById('theme-toggle');
        
        themeToggleBtn.addEventListener('click', function() {
            document.documentElement.classList.toggle('dark');
            
            if (document.documentElement.classList.contains('dark')) {
                localStorage.setItem('theme', 'dark');
            } else {
                localStorage.setItem('theme', 'light');
            }
        });

        // Real-time Clock WIB
        function updateClock() {
            const now = new Date();
            const options = { timeZone: 'Asia/Jakarta', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };
            const timeString = now.toLocaleTimeString('id-ID', options);
            document.getElementById('realtime-clock').textContent = timeString;
        }
        
        updateClock();
        setInterval(updateClock, 1000);

        // Sortable JS Init
        const columns = ['doing', 'on_progress', 'done'];
        
        columns.forEach(col => {
            const el = document.getElementById(`col-${col}`);
            new Sortable(el, {
                group: 'trello-board',
                animation: 200,
                ghostClass: 'sortable-ghost',
                filter: '.not-draggable',
                preventOnFilter: true,
                delay: 50,
                delayOnTouchOnly: true,
                onEnd: function (evt) {
                    const itemEl = evt.item;
                    const taskId = itemEl.getAttribute('data-id');
                    const toCol = evt.to.getAttribute('data-status');
                    const fromCol = evt.from.getAttribute('data-status');
                    
                    if(toCol !== fromCol) {
                        // Update Badges
                        updateBadges();
                        
                        // Send AJAX
                        fetch(`/tugas/${taskId}`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ status: toCol })
                        }).then(res => res.json())
                          .then(data => {
                              if(data.success) {
                                  // The styling uses parent container CSS, so it updates automatically
                              } else {
                                  alert('Gagal update status tugas.');
                                  window.location.reload();
                              }
                          }).catch(err => {
                              console.error(err);
                              window.location.reload();
                          });
                    }
                }
            });
        });

        function updateBadges() {
            let totalDone = 0;
            columns.forEach(col => {
                const el = document.getElementById(`col-${col}`);
                const count = el.children.length;
                el.parentElement.querySelector('.count-badge').textContent = count;
                if(col === 'done') totalDone = count;
            });

            // Update Global Stats
            const total = {{ $total }};
            const pending = total - totalDone;
            const progress = total > 0 ? Math.round((totalDone / total) * 100) : 0;

            document.getElementById('stat-done').textContent = totalDone;
            document.getElementById('stat-pending').textContent = pending;
            document.getElementById('stat-completed').textContent = totalDone;
            document.getElementById('stat-percent').textContent = progress + '%';
            document.getElementById('stat-progress-bar').style.width = progress + '%';
        }
    </script>
</body>
</html>