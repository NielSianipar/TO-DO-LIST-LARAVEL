<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskMaster - Modern To-Do List</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-card {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.6);
        }
        .bg-animated {
            background: linear-gradient(-45deg, #f3e7e9, #e3eeff, #e0c3fc, #8ec5fc);
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
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
    <script>
        tailwind.config = {
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
<body class="bg-animated min-h-screen text-slate-800 p-4 md:p-8 flex items-center justify-center selection:bg-brand-500 selection:text-white">

    @php
        $total = count($tasks ?? []);
        $completed = 0;
        foreach($tasks ?? [] as $t) {
            if($t->is_selesai) $completed++;
        }
        $pending = $total - $completed;
        $progress = $total > 0 ? round(($completed / $total) * 100) : 0;
        
        $greetings = ['Good Morning', 'Good Afternoon', 'Good Evening'];
        $hour = date('H');
        $greeting = $hour < 12 ? $greetings[0] : ($hour < 18 ? $greetings[1] : $greetings[2]);
    @endphp

    <div class="w-full max-w-5xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">
        
        <!-- Sidebar Dashboard -->
        <div class="glass-card rounded-[2rem] p-8 shadow-soft lg:col-span-4 flex flex-col relative overflow-hidden group">
            <div class="absolute -top-24 -right-24 w-48 h-48 bg-brand-400 rounded-full mix-blend-multiply filter blur-3xl opacity-30 group-hover:opacity-50 transition-opacity duration-700"></div>
            <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-purple-400 rounded-full mix-blend-multiply filter blur-3xl opacity-30 group-hover:opacity-50 transition-opacity duration-700"></div>
            
            <div class="relative z-10 flex-1 flex flex-col">
                <div class="flex items-center gap-3 mb-10">
                    <div class="p-3 bg-gradient-to-br from-brand-500 to-purple-600 rounded-2xl shadow-lg flex items-center justify-center">
                        <i data-lucide="check-circle-2" class="text-white w-6 h-6"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">TaskMaster</h1>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Workspace</p>
                    </div>
                </div>

                <div class="space-y-4 mb-auto">
                    <div class="bg-white/60 p-5 rounded-2xl border border-white/80 shadow-sm backdrop-blur-md">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-sm font-bold text-slate-500">Total Tasks</h3>
                            <i data-lucide="layers" class="w-4 h-4 text-slate-400"></i>
                        </div>
                        <p class="text-4xl font-extrabold text-slate-800">{{ $total }}</p>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-white/60 p-4 rounded-2xl border border-white/80 shadow-sm backdrop-blur-md">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-xs font-bold text-slate-500">Done</h3>
                                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500"></i>
                            </div>
                            <p class="text-2xl font-extrabold text-emerald-600">{{ $completed }}</p>
                        </div>
                        <div class="bg-white/60 p-4 rounded-2xl border border-white/80 shadow-sm backdrop-blur-md">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-xs font-bold text-slate-500">Pending</h3>
                                <i data-lucide="clock" class="w-4 h-4 text-orange-500"></i>
                            </div>
                            <p class="text-2xl font-extrabold text-orange-600">{{ $pending }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-10 relative z-10 bg-white/40 p-5 rounded-2xl border border-white/60 shadow-sm">
                    <div class="flex justify-between items-end mb-3">
                        <div>
                            <span class="text-sm font-bold text-slate-700 block">Overall Progress</span>
                            <span class="text-xs font-medium text-slate-500">{{ $completed }} of {{ $total }} completed</span>
                        </div>
                        <span class="text-lg font-black text-brand-600">{{ $progress }}%</span>
                    </div>
                    <div class="w-full bg-slate-200/50 rounded-full h-2.5 overflow-hidden">
                        <div class="bg-gradient-to-r from-brand-500 to-purple-500 h-2.5 rounded-full transition-all duration-1000 ease-out" style="width: {{ $progress }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="glass-card rounded-[2rem] p-6 lg:p-10 shadow-soft lg:col-span-8 flex flex-col relative overflow-hidden bg-white/60 h-[85vh] lg:h-auto">
            
            <!-- Header section -->
            <div class="flex justify-between items-start mb-8">
                <div>
                    <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $greeting }}! <span class="text-2xl">😎</span></h2>
                    <p class="text-slate-500 font-medium mt-1 flex items-center gap-2">
                        <i data-lucide="calendar" class="w-4 h-4"></i>
                        {{ now()->format('l, j F Y') }}
                    </p>
                    <p class="text-slate-400 font-medium mt-1 flex items-center gap-2 text-sm">
                        <i data-lucide="clock" class="w-4 h-4"></i>
                        <span id="realtime-clock">Memuat waktu...</span> WIB
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="hidden sm:flex items-center gap-2 bg-white/80 px-4 py-2.5 rounded-xl shadow-sm border border-white">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.8)] animate-pulse"></div>
                        <span class="text-sm font-bold text-slate-700">{{ Auth::user()->name }}</span>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="flex items-center justify-center p-2.5 bg-white/80 hover:bg-red-500 text-slate-600 hover:text-white border border-white rounded-xl transition-all duration-300 shadow-sm hover:shadow-md" title="Keluar">
                            <i data-lucide="log-out" class="w-5 h-5"></i>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Add Task Form -->
            <form action="/tugas" method="POST" class="mb-8 relative group shrink-0">
                @csrf
                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                    <i data-lucide="plus" class="text-slate-400 group-focus-within:text-brand-600 transition-colors w-6 h-6"></i>
                </div>
                <input 
                    type="text" 
                    name="nama_tugas" 
                    placeholder="Add a new task..." 
                    autocomplete="off"
                    required
                    class="w-full pl-14 pr-36 py-4 bg-white border-2 border-transparent text-slate-800 rounded-2xl focus:outline-none focus:ring-0 focus:border-brand-500 transition-all shadow-sm focus:shadow-md text-lg font-semibold placeholder:text-slate-400 placeholder:font-medium"
                >
                <button 
                    type="submit" 
                    class="absolute right-2 top-2 bottom-2 bg-slate-900 hover:bg-brand-600 text-white font-bold px-8 rounded-xl transition-all duration-300 shadow-md hover:shadow-glow flex items-center gap-2"
                >
                    <span>Create</span>
                </button>
            </form>

            <div class="flex items-center gap-4 mb-4 shrink-0">
                <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-widest bg-slate-200/80 px-4 py-1.5 rounded-full">Tasks List</h3>
                <div class="h-px bg-slate-200 flex-1"></div>
            </div>

            <!-- Task List -->
            <div class="flex-1 overflow-y-auto pr-2 pb-4 -mr-2 space-y-3">
                @if(isset($tasks) && count($tasks) === 0)
                    <div class="flex flex-col items-center justify-center h-full min-h-[250px] text-center">
                        <div class="w-24 h-24 bg-white rounded-full shadow-sm flex items-center justify-center mb-5 relative">
                            <i data-lucide="sparkles" class="w-10 h-10 text-brand-400"></i>
                            <div class="absolute inset-0 border-2 border-brand-200 rounded-full animate-ping opacity-20"></div>
                        </div>
                        <h3 class="text-xl font-extrabold text-slate-800">You're all caught up!</h3>
                        <p class="text-slate-500 font-medium mt-2 max-w-sm">No pending tasks found. Enjoy your free time or create a new task to stay productive.</p>
                    </div>
                @else
                    @foreach($tasks as $index => $task)
                        <div class="task-item group flex items-center justify-between p-4 px-5 rounded-2xl bg-white hover:bg-slate-50/80 border border-slate-100 shadow-sm hover:shadow-md transition-all duration-300" style="animation-delay: {{ $index * 50 }}ms;">
                            
                            <div class="flex items-center gap-4 flex-1">
                                <form action="/tugas/{{ $task->id }}" method="POST" class="flex-shrink-0">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="w-7 h-7 rounded-full border-2 flex items-center justify-center transition-all duration-300 {{ $task->is_selesai ? 'bg-emerald-500 border-emerald-500 text-white shadow-md shadow-emerald-200' : 'border-slate-300 hover:border-brand-500 text-transparent hover:text-brand-200 bg-slate-50' }}" title="{{ $task->is_selesai ? 'Mark as incomplete' : 'Mark as complete' }}">
                                        <i data-lucide="check" class="w-4 h-4 {{ $task->is_selesai ? 'opacity-100' : 'opacity-0' }} transition-opacity stroke-[3]"></i>
                                    </button>
                                </form>
                                
                                <span class="text-slate-700 font-semibold text-[1.05rem] transition-all duration-300 {{ $task->is_selesai ? 'line-through text-slate-400' : '' }}">
                                    {{ $task->nama_tugas }}
                                </span>
                            </div>

                            <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300 ml-4 focus-within:opacity-100">
                                <form action="/tugas/{{ $task->id }}" method="POST" onsubmit="return confirm('Delete this task?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="flex items-center justify-center w-9 h-9 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-xl transition-all outline-none focus:ring-2 focus:ring-red-200" title="Delete Task">
                                        <i data-lucide="trash-2" class="w-4.5 h-4.5"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

        </div>
    </div>

    <!-- Init Lucide Icons -->
    <script>
        lucide.createIcons();

        // Real-time Clock WIB
        function updateClock() {
            const now = new Date();
            // Optional: You could use local time if it matches WIB, or force it using options
            const options = { timeZone: 'Asia/Jakarta', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };
            const timeString = now.toLocaleTimeString('id-ID', options);
            document.getElementById('realtime-clock').textContent = timeString;
        }
        
        // Initial call
        updateClock();
        // Update every second
        setInterval(updateClock, 1000);
    </script>
</body>
</html>