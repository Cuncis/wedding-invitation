<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name')) — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Brand palette — applied on top of Tailwind CDN so all rose-* classes
        // automatically render in the new brand blue, and a new accent-* gold
        // palette is available for highlights/CTAs.
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0f6fa', 100: '#d9e7f0', 200: '#b3cee0', 300: '#88b1ce',
                            400: '#4e87b0', 500: '#2d6388', 600: '#1C4965', 700: '#163a51',
                            800: '#112d3f', 900: '#0c1f2c', 950: '#061320',
                        },
                        accent: {
                            50: '#fffaeb', 100: '#fff1c6', 200: '#ffe389', 300: '#ffce47',
                            400: '#ffbe1f', 500: '#ffb904', 600: '#d68d00', 700: '#b06700',
                            800: '#8a4f06', 900: '#74410a', 950: '#432104',
                        },
                        // Remap rose to brand so existing rose-* utilities pick up the new colour.
                        rose: {
                            50: '#f0f6fa', 100: '#d9e7f0', 200: '#b3cee0', 300: '#88b1ce',
                            400: '#4e87b0', 500: '#2d6388', 600: '#1C4965', 700: '#163a51',
                            800: '#112d3f', 900: '#0c1f2c', 950: '#061320',
                        },
                    },
                },
            },
        };
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-900 antialiased">
    <nav class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between h-16 items-center">
            <a href="{{ route('home') }}" class="text-lg font-semibold text-rose-600">💌 {{ config('app.name') }}</a>
            <div class="flex items-center gap-4 text-sm">
                @auth
                    <a href="{{ route('dashboard') }}" class="text-slate-700 hover:text-slate-900">Dashboard</a>
                    @if(auth()->user()->isAdmin())
                        <a href="/admin" class="text-slate-700 hover:text-slate-900">Admin</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-slate-700 hover:text-slate-900">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-slate-700 hover:text-slate-900">Login</a>
                    <a href="{{ route('register') }}"
                        class="bg-accent-500 text-slate-900 font-semibold px-4 py-2 rounded-md hover:bg-accent-600">Daftar</a>
                @endauth
            </div>
        </div>
    </nav>

    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-md">
                {{ session('success') }}
            </div>
        </div>
    @endif

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>
</body>

</html>