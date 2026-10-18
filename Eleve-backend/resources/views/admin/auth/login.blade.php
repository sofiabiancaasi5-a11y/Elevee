<!DOCTYPE html>
<html lang="en" class="h-full bg-[#171717]">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Director Sign In — ÉLEVÉ Paris</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,500;0,700;1,400&display=swap"
        rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .font-luxury-serif {
            font-family: 'Playfair Display', Georgia, serif;
        }
    </style>
</head>

<body class="h-full flex items-center justify-center p-6 text-stone-200">
    <div class="w-full max-w-md">
        <!-- Logo & Title -->
        <div class="text-center mb-10">
            <div
                class="w-16 h-16 mx-auto mb-4 rounded-full bg-stone-900 border border-stone-800 flex items-center justify-center shadow-2xl">
                <span class="font-luxury-serif text-3xl font-bold text-stone-100">É</span>
            </div>
            <h1 class="font-luxury-serif text-3xl tracking-[0.25em] font-semibold text-white">ÉLEVÉ</h1>
            <p class="text-xs uppercase tracking-[0.3em] text-stone-400 mt-1">Every Step, Elevated</p>
            <div class="w-12 h-[1px] bg-stone-700 mx-auto mt-4"></div>
        </div>

        <!-- Login Card -->
        <div class="bg-stone-900/90 border border-stone-800/80 rounded-2xl p-8 shadow-2xl backdrop-blur-xl">
            <h2 class="text-lg font-medium text-white mb-2 text-center">Atelier Portal</h2>
            <p class="text-xs text-stone-400 mb-6 text-center">Sign in to control catalog, inventory, and orders.</p>

            @if ($errors->any())
                <div class="mb-5 p-3 rounded-lg bg-rose-950/40 border border-rose-800/60 text-rose-300 text-xs">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label
                        class="block text-xs uppercase tracking-wider text-stone-300 mb-1.5 font-medium">Administrator
                        Email</label>
                    <input type="email" name="email" value="{{ old('email', 'admin@eleve.com') }}" required
                        autofocus
                        class="w-full px-4 py-3 rounded-lg bg-stone-950/80 border border-stone-800 text-white text-sm focus:outline-none focus:border-stone-400 transition-colors">
                </div>

                <div>
                    <label
                        class="block text-xs uppercase tracking-wider text-stone-300 mb-1.5 font-medium">Password</label>
                    <input type="password" name="password" value="password" required
                        class="w-full px-4 py-3 rounded-lg bg-stone-950/80 border border-stone-800 text-white text-sm focus:outline-none focus:border-stone-400 transition-colors">
                </div>

                <div class="flex items-center justify-between text-xs text-stone-400 pt-1">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="remember"
                            class="rounded bg-stone-950 border-stone-700 text-stone-200 focus:ring-0">
                        <span>Remember credentials</span>
                    </label>
                    <span class="text-[11px] text-stone-500">XAMPP Localhost</span>
                </div>

                <button type="submit"
                    class="w-full py-3.5 mt-2 rounded-lg bg-stone-100 hover:bg-white text-stone-950 text-xs font-semibold uppercase tracking-[0.2em] transition-all duration-200 shadow-md">
                    Access Dashboard
                </button>
            </form>

        </div>

        <p class="text-center text-[11px] text-stone-600 mt-8 tracking-wider">
            &copy; {{ date('Y') }} ÉLEVÉ Luxury Footwear. All rights reserved.
        </p>
    </div>
</body>

</html>
