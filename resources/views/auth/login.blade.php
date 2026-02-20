<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TaskMaster</title>
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
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.8);
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

    <div class="glass-card rounded-[2.5rem] p-8 md:p-12 max-w-md w-full relative overflow-hidden shadow-2xl">
        <div class="absolute -top-20 -right-20 w-48 h-48 bg-brand-400 rounded-full mix-blend-multiply filter blur-3xl opacity-40"></div>
        <div class="absolute -bottom-20 -left-20 w-48 h-48 bg-purple-400 rounded-full mix-blend-multiply filter blur-3xl opacity-40"></div>
        
        <div class="relative z-10">
            <div class="text-center mb-8">
                <a href="{{ route('welcome') }}" class="inline-flex justify-center items-center mb-6 hover:scale-105 transition-transform duration-300">
                    <div class="p-4 bg-gradient-to-br from-brand-500 to-purple-600 rounded-2xl shadow-lg">
                        <i data-lucide="check-square" class="text-white w-8 h-8"></i>
                    </div>
                </a>
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Selamat Datang Kembali</h2>
                <p class="text-slate-500 font-medium mt-2">Masuk untuk melanjutkan kegiatan Anda.</p>
            </div>

            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf
                
                @if($errors->any())
                    <div class="p-4 bg-red-50 border border-red-200 text-red-600 rounded-2xl text-sm font-medium">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Alamat Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i data-lucide="mail" class="text-slate-400 w-5 h-5"></i>
                        </div>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                            class="w-full pl-11 pr-4 py-3.5 bg-white border-2 border-slate-100 text-slate-800 rounded-2xl focus:outline-none focus:ring-0 focus:border-brand-500 transition-all shadow-sm focus:shadow-md font-semibold placeholder:text-slate-400 placeholder:font-medium" placeholder="nama@email.com">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Kata Sandi</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i data-lucide="lock" class="text-slate-400 w-5 h-5"></i>
                        </div>
                        <input type="password" name="password" required
                            class="w-full pl-11 pr-4 py-3.5 bg-white border-2 border-slate-100 text-slate-800 rounded-2xl focus:outline-none focus:ring-0 focus:border-brand-500 transition-all shadow-sm focus:shadow-md font-semibold placeholder:text-slate-400 placeholder:font-medium" placeholder="••••••••">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-4 bg-slate-900 hover:bg-brand-600 text-white font-bold rounded-2xl transition-all duration-300 shadow-md hover:shadow-glow flex items-center justify-center gap-2">
                        Masuk <i data-lucide="arrow-right" class="w-5 h-5"></i>
                    </button>
                </div>
            </form>

            <div class="mt-8 text-center">
                <p class="text-slate-500 font-medium">Belum punya akun? <a href="{{ route('register') }}" class="text-brand-600 font-bold hover:underline hover:text-brand-700 transition-colors">Daftar sekarang</a></p>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
