@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow-sm border border-slate-200">
    <h1 class="text-2xl font-bold mb-6">Masuk ke Akun Anda</h1>

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                class="w-full px-3 py-2 border border-slate-300 rounded-md focus:ring-2 focus:ring-rose-500 focus:border-rose-500">
            @error('email')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
            <input type="password" name="password" required
                class="w-full px-3 py-2 border border-slate-300 rounded-md focus:ring-2 focus:ring-rose-500 focus:border-rose-500">
            @error('password')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="flex items-center">
            <input type="checkbox" name="remember" id="remember" class="rounded border-slate-300">
            <label for="remember" class="ml-2 text-sm text-slate-700">Ingat saya</label>
        </div>

        <button type="submit" class="w-full bg-rose-600 text-white py-2 rounded-md hover:bg-rose-700 font-medium">
            Masuk
        </button>
    </form>

    <p class="text-center text-sm text-slate-600 mt-6">
        Belum punya akun? <a href="{{ route('register') }}" class="text-rose-600 font-medium hover:underline">Daftar di sini</a>
    </p>
</div>
@endsection
