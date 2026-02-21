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
            <div class="absolute -top-24 -right-24 w-48 h-48 bg-brand-400 rounded-full mix-blend-multiply filter blur-3xl opacity-30 group-hover:opacity-50 transition-opacity duration-700"></div>
            <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-purple-400 rounded-full mix-blend-multiply filter blur-3xl opacity-30 group-hover:opacity-50 transition-opacity duration-700"></div>
            
            <div class="relative z-10 flex-1 flex flex-col">
                <div class="flex items-center gap-3 mb-10">
                    <div class="p-3 bg-gradient-to-br from-brand-500 to-purple-600 rounded-2xl shadow-lg flex items-center justify-center">
                        <i data-lucide="trello" class="text-white w-6 h-6"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">TaskBoard</h1>
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
                            <p class="text-2xl font-extrabold text-emerald-600" id="stat-done">{{ $completed }}</p>
                        </div>
                        <div class="bg-white/60 p-4 rounded-2xl border border-white/80 shadow-sm backdrop-blur-md">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-xs font-bold text-slate-500">Pending</h3>
                                <i data-lucide="clock" class="w-4 h-4 text-orange-500"></i>
                            </div>
                            <p class="text-2xl font-extrabold text-orange-600" id="stat-pending">{{ $pending }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-10 relative z-10 bg-white/40 p-5 rounded-2xl border border-white/60 shadow-sm">
                    <div class="flex justify-between items-end mb-3">
                        <div>
                            <span class="text-sm font-bold text-slate-700 block">Overall Progress</span>
                            <span class="text-xs font-medium text-slate-500" id="stat-text"><span id="stat-completed">{{ $completed }}</span> of {{ $total }} completed</span>
                        </div>
                        <span class="text-lg font-black text-brand-600" id="stat-percent">{{ $progress }}%</span>
                    </div>
                    <div class="w-full bg-slate-200/50 rounded-full h-2.5 overflow-hidden">
                        <div id="stat-progress-bar" class="bg-gradient-to-r from-brand-500 to-purple-500 h-2.5 rounded-full transition-all duration-1000 ease-out" style="width: {{ $progress }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="glass-card rounded-[2rem] p-6 lg:p-8 shadow-soft xl:col-span-9 flex flex-col relative overflow-hidden bg-white/60 h-[85vh] lg:h-auto">
            
            <!-- Header section -->
            <div class="flex justify-between items-start mb-6">
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

            <!-- Controls block: Filter & Add Task -->
            <div class="flex flex-col sm:flex-row gap-4 mb-6 shrink-0 z-10">
                <!-- Date Filter -->
                <form action="/tugas" method="GET" class="relative group sm:w-1/3 lg:w-1/4 xl:w-1/5">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i data-lucide="calendar" class="text-slate-400 group-focus-within:text-brand-500 w-5 h-5"></i>
                    </div>
                    <input 
                        type="date" 
                        name="date" 
                        value="{{ $selectedDate ?? '' }}"
                        onchange="this.form.submit()"
                        class="w-full pl-11 pr-10 py-4 bg-white border-2 border-transparent text-slate-700 rounded-2xl focus:outline-none focus:ring-0 focus:border-brand-500 transition-all shadow-sm hover:shadow-md focus:shadow-md font-semibold cursor-pointer"
                        title="Filter tugas berdasarkan tanggal"
                    >
                    @if(isset($selectedDate) && $selectedDate)
                        <a href="/tugas" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-red-500 transition-colors" title="Hapus Filter">
                            <i data-lucide="x-circle" class="w-5 h-5"></i>
                        </a>
                    @endif
                </form>

                <!-- Add Task Form -->
                <form action="/tugas" method="POST" class="relative group flex-1">
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
                        class="w-full pl-14 pr-32 py-4 bg-white border-2 border-transparent text-slate-800 rounded-2xl focus:outline-none focus:ring-0 focus:border-brand-500 transition-all shadow-sm focus:shadow-md text-lg font-semibold placeholder:text-slate-400 placeholder:font-medium"
                    >
                    <button 
                        type="submit" 
                        class="absolute right-2 top-2 bottom-2 bg-slate-900 hover:bg-brand-600 text-white font-bold px-6 rounded-xl transition-all duration-300 shadow-md hover:shadow-glow flex items-center gap-2"
                    >
                        <span>Create</span>
                    </button>
                </form>
            </div>

            <!-- Kanban Board -->
            <div class="flex-1 min-h-0 overflow-x-auto overflow-y-hidden pb-2 -mx-2 px-2">
                <div class="flex gap-6 h-full min-w-[900px]">
                    
                    <!-- Doing Column -->
                    <div class="flex flex-col w-1/3 bg-slate-100/60 rounded-3xl p-4 border border-slate-200/80 shadow-inner h-full">
                        <div class="flex items-center justify-between mb-4 px-2">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full bg-slate-400"></div>
                                <h3 class="text-sm font-extrabold text-slate-700 uppercase tracking-wider">Doing</h3>
                            </div>
                            <span class="bg-slate-200 text-slate-600 text-xs font-bold px-2 py-1 rounded-full count-badge">{{ $doingTasks->count() }}</span>
                        </div>
                        <div id="col-doing" class="kanban-col flex-1 overflow-y-auto space-y-3 p-1 min-h-[150px]" data-status="doing">
                            @foreach($doingTasks as $task)
                                @include('components.task-card', ['task' => $task])
                            @endforeach
                        </div>
                    </div>

                    <!-- On Progress Column -->
                    <div class="flex flex-col w-1/3 bg-blue-50/60 rounded-3xl p-4 border border-blue-100/80 shadow-inner h-full">
                        <div class="flex items-center justify-between mb-4 px-2">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full bg-blue-500 animate-pulse"></div>
                                <h3 class="text-sm font-extrabold text-blue-800 uppercase tracking-wider">On Progress</h3>
                            </div>
                            <span class="bg-blue-200 text-blue-800 text-xs font-bold px-2 py-1 rounded-full count-badge">{{ $onProgressTasks->count() }}</span>
                        </div>
                        <div id="col-on_progress" class="kanban-col flex-1 overflow-y-auto space-y-3 p-1 min-h-[150px]" data-status="on_progress">
                            @foreach($onProgressTasks as $task)
                                @include('components.task-card', ['task' => $task])
                            @endforeach
                        </div>
                    </div>

                    <!-- Done Column -->
                    <div class="flex flex-col w-1/3 bg-emerald-50/60 rounded-3xl p-4 border border-emerald-100/80 shadow-inner h-full">
                        <div class="flex items-center justify-between mb-4 px-2">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                                <h3 class="text-sm font-extrabold text-emerald-800 uppercase tracking-wider">Done</h3>
                            </div>
                            <span class="bg-emerald-200 text-emerald-800 text-xs font-bold px-2 py-1 rounded-full count-badge">{{ $doneTasks->count() }}</span>
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

    <!-- Init Lucide Icons -->
    <script>
        lucide.createIcons();

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
                                  // Update internal styling if moved to done or out of done
                                  const textEl = itemEl.querySelector('.task-text');
                                  if(toCol === 'done') {
                                      textEl.classList.add('line-through', 'text-slate-400');
                                  } else {
                                      textEl.classList.remove('line-through', 'text-slate-400');
                                  }
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