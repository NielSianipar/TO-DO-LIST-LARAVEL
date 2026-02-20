<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - TaskMaster</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
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
        .glass-card {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.6);
        }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { 50: '#eef2ff', 100: '#e0e7ff', 500: '#6366f1', 600: '#4f46e5', 700: '#4338ca', }
                    },
                    boxShadow: { 'glow': '0 0 20px rgba(99, 102, 241, 0.4)' }
                }
            }
        }
    </script>
</head>
<body class="bg-animated min-h-screen text-slate-800 flex items-center justify-center p-4">

    <div class="glass-card rounded-[2.5rem] p-10 md:p-14 max-w-2xl w-full text-center relative overflow-hidden shadow-2xl">
        <div class="absolute -top-32 -right-32 w-64 h-64 bg-brand-400 rounded-full mix-blend-multiply filter blur-3xl opacity-40"></div>
        <div class="absolute -bottom-32 -left-32 w-64 h-64 bg-purple-400 rounded-full mix-blend-multiply filter blur-3xl opacity-40"></div>
        
        <div class="relative z-10 flex flex-col items-center">
            <div class="p-5 bg-gradient-to-br from-brand-500 to-purple-600 rounded-3xl shadow-xl flex items-center justify-center mb-8 rotate-3 hover:rotate-0 transition-transform duration-300">
                <i data-lucide="check-square" class="text-white w-12 h-12"></i>
            </div>
            
            <h1 class="text-5xl font-extrabold tracking-tight text-slate-900 mb-4">TaskMaster</h1>
            <p class="text-xl text-slate-600 font-medium mb-12 max-w-md">Tingkatkan produktivitas Anda dengan manajer tugas modern, cepat, dan elegan.</p>
            
            <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
                <a href="{{ route('login') }}" class="px-8 py-4 bg-slate-900 hover:bg-brand-600 text-white font-bold rounded-2xl transition-all duration-300 shadow-md hover:shadow-glow flex items-center justify-center gap-2">
                    <i data-lucide="log-in" class="w-5 h-5"></i>
                    Masuk Sekarang
                </a>
                <a href="{{ route('register') }}" class="px-8 py-4 bg-white/80 hover:bg-white text-slate-800 font-bold border-2 border-white rounded-2xl transition-all duration-300 shadow-sm hover:shadow-md flex items-center justify-center gap-2">
                    <i data-lucide="user-plus" class="w-5 h-5"></i>
                    Daftar Akun Baru
                </a>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>