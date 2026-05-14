@extends('layouts.app')

@section('title', 'Daftar Akun')

@section('content')
<div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow-sm border border-slate-200">
    <h1 class="text-2xl font-bold mb-6">Daftar Akun Baru</h1>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name') }}" required autofocus
                class="w-full px-3 py-2 border border-slate-300 rounded-md focus:ring-2 focus:ring-rose-500 focus:border-rose-500">
            @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                class="w-full px-3 py-2 border border-slate-300 rounded-md focus:ring-2 focus:ring-rose-500 focus:border-rose-500">
            @error('email')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">No. WhatsApp</label>
            <input type="text" name="whatsapp" value="{{ old('whatsapp') }}" placeholder="6281234567890"
                class="w-full px-3 py-2 border border-slate-300 rounded-md focus:ring-2 focus:ring-rose-500 focus:border-rose-500">
            <p class="text-xs text-slate-500 mt-1">Gunakan format 62 di awal (tanpa tanda +)</p>
            @error('whatsapp')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
            <input type="password" name="password" required
                class="w-full px-3 py-2 border border-slate-300 rounded-md focus:ring-2 focus:ring-rose-500 focus:border-rose-500">
            @error('password')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" required
                class="w-full px-3 py-2 border border-slate-300 rounded-md focus:ring-2 focus:ring-rose-500 focus:border-rose-500">
        </div>

        <button type="submit" class="w-full bg-rose-600 text-white py-2 rounded-md hover:bg-rose-700 font-medium">
            Daftar
        </button>
    </form>

    <p class="text-center text-sm text-slate-600 mt-6">
        Sudah punya akun? <a href="{{ route('login') }}" class="text-rose-600 font-medium hover:underline">Masuk di sini</a>
    </p>
</div>
@endsection
