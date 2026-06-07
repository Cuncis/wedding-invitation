@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="flex items-center justify-center min-h-[70vh]">
    <div class="card bg-base-100 shadow-xl w-full max-w-md">
        <div class="card-body gap-5">

            <div class="text-center">
                <div class="flex justify-center mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-10 h-10 text-primary">
                        <path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold">Masuk ke Akun Anda</h1>
                <p class="text-sm text-base-content/60 mt-1">Selamat datang kembali</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <label class="form-control w-full">
                    <div class="label"><span class="label-text font-medium">Email</span></div>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        placeholder="nama@email.com"
                        class="input input-bordered w-full @error('email') input-error @enderror">
                    @error('email')
                        <div class="label"><span class="label-text-alt text-error">{{ $message }}</span></div>
                    @enderror
                </label>

                <label class="form-control w-full">
                    <div class="label"><span class="label-text font-medium">Password</span></div>
                    <input type="password" name="password" required
                        placeholder="••••••••"
                        class="input input-bordered w-full @error('password') input-error @enderror">
                    @error('password')
                        <div class="label"><span class="label-text-alt text-error">{{ $message }}</span></div>
                    @enderror
                </label>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="remember" id="remember" class="checkbox checkbox-primary checkbox-sm">
                    <label for="remember" class="text-sm cursor-pointer">Ingat saya</label>
                </div>

                <button type="submit" class="btn btn-primary w-full">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
                    </svg>
                    Masuk
                </button>
            </form>

            <div class="divider text-xs text-base-content/40">Belum punya akun?</div>

            <a href="{{ route('register') }}" class="btn btn-outline btn-primary w-full">Daftar Sekarang</a>
        </div>
    </div>
</div>
@endsection
