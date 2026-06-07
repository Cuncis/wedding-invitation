@extends('layouts.app')

@section('title', 'Daftar Akun')

@section('content')
<div class="flex items-center justify-center min-h-[70vh] py-6">
    <div class="card bg-base-100 shadow-xl w-full max-w-md">
        <div class="card-body gap-4">

            <div class="text-center">
                <div class="flex justify-center mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 text-primary">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold">Buat Akun Baru</h1>
                <p class="text-sm text-base-content/60 mt-1">Mulai buat undangan impianmu</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-3">
                @csrf

                <label class="form-control w-full">
                    <div class="label"><span class="label-text font-medium">Nama Lengkap</span></div>
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus
                        placeholder="Nama lengkap Anda"
                        class="input input-bordered w-full @error('name') input-error @enderror">
                    @error('name')
                        <div class="label"><span class="label-text-alt text-error">{{ $message }}</span></div>
                    @enderror
                </label>

                <label class="form-control w-full">
                    <div class="label"><span class="label-text font-medium">Email</span></div>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        placeholder="nama@email.com"
                        class="input input-bordered w-full @error('email') input-error @enderror">
                    @error('email')
                        <div class="label"><span class="label-text-alt text-error">{{ $message }}</span></div>
                    @enderror
                </label>

                <label class="form-control w-full">
                    <div class="label">
                        <span class="label-text font-medium">No. WhatsApp</span>
                        <span class="label-text-alt text-base-content/50">Format: 628xxx</span>
                    </div>
                    <input type="text" name="whatsapp" value="{{ old('whatsapp') }}" placeholder="6281234567890"
                        class="input input-bordered w-full @error('whatsapp') input-error @enderror">
                    @error('whatsapp')
                        <div class="label"><span class="label-text-alt text-error">{{ $message }}</span></div>
                    @enderror
                </label>

                <label class="form-control w-full">
                    <div class="label"><span class="label-text font-medium">Password</span></div>
                    <input type="password" name="password" required
                        placeholder="Min. 8 karakter"
                        class="input input-bordered w-full @error('password') input-error @enderror">
                    @error('password')
                        <div class="label"><span class="label-text-alt text-error">{{ $message }}</span></div>
                    @enderror
                </label>

                <label class="form-control w-full">
                    <div class="label"><span class="label-text font-medium">Konfirmasi Password</span></div>
                    <input type="password" name="password_confirmation" required
                        placeholder="Ulangi password"
                        class="input input-bordered w-full">
                </label>

                <button type="submit" class="btn btn-primary w-full mt-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Daftar Sekarang
                </button>
            </form>

            <div class="divider text-xs text-base-content/40">Sudah punya akun?</div>
            <a href="{{ route('login') }}" class="btn btn-outline btn-primary w-full">Masuk</a>
        </div>
    </div>
</div>
@endsection
