@extends('layouts.app')

@section('title', 'Checkout — ' . $invitation->coupleNames())

@section('content')
<div class="max-w-2xl mx-auto py-6">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('dashboard') }}" class="btn btn-ghost btn-sm btn-circle">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold">Ringkasan Pesanan</h1>
            <p class="text-base-content/60 text-sm mt-0.5">{{ $invitation->coupleNames() }}</p>
        </div>
    </div>

    @if ($errors->has('checkout'))
        <div role="alert" class="alert alert-error mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ $errors->first('checkout') }}</span>
        </div>
    @endif

    @if (!$pricing['theme'])
        <div role="alert" class="alert alert-warning mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <span>Anda belum memilih tema. Silakan pilih tema terlebih dahulu melalui builder.</span>
        </div>
    @endif

    <div class="card bg-base-100 shadow mb-6">
        <div class="card-body p-0">
            <div class="px-6 py-4 border-b border-base-200 bg-base-200/50 rounded-t-2xl">
                <h2 class="font-semibold text-sm uppercase tracking-wider text-base-content/60">Detail Harga</h2>
            </div>

            <div class="divide-y divide-base-200">
                {{-- Theme --}}
                <div class="flex justify-between items-center px-6 py-4">
                    <div>
                        <p class="font-medium">Tema</p>
                        <p class="text-sm text-base-content/50">{{ $pricing['theme']['name'] ?? 'Belum dipilih' }}</p>
                    </div>
                    <span class="font-semibold">{{ \App\Services\PricingService::format($pricing['theme_price']) }}</span>
                </div>

                {{-- Animation Pack --}}
                <div class="flex justify-between items-center px-6 py-4">
                    <div>
                        <p class="font-medium">Paket Animasi</p>
                        <p class="text-sm text-base-content/50">{{ $pricing['animation']['name'] ?? 'Tidak ada' }}</p>
                    </div>
                    <span class="font-semibold">{{ \App\Services\PricingService::format($pricing['animation_price']) }}</span>
                </div>

                {{-- Addons --}}
                @if (count($pricing['addons']))
                    @foreach ($pricing['addons'] as $addon)
                        <div class="flex justify-between items-center px-6 py-4">
                            <div>
                                <p class="font-medium">{{ $addon['name'] }}</p>
                                <span class="badge badge-outline badge-xs mt-0.5">Add-on</span>
                            </div>
                            <span class="font-semibold">{{ \App\Services\PricingService::format($addon['price']) }}</span>
                        </div>
                    @endforeach
                @else
                    <div class="flex justify-between items-center px-6 py-4">
                        <p class="text-base-content/50">Add-on</p>
                        <span class="text-base-content/30">Tidak ada</span>
                    </div>
                @endif
            </div>

            {{-- Total --}}
            <div class="flex justify-between items-center px-6 py-5 bg-primary/5 border-t border-primary/20 rounded-b-2xl">
                <p class="text-lg font-bold">Total Pembayaran</p>
                <p class="text-2xl font-bold text-primary">{{ $pricing['total_formatted'] }}</p>
            </div>
        </div>
    </div>

    @if ($pricing['theme'])
        <form method="POST" action="{{ route('checkout.store', $invitation) }}">
            @csrf
            <button type="submit" class="btn btn-primary w-full btn-lg">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>
                </svg>
                Buat Pesanan &amp; Lanjut ke Pembayaran
            </button>
        </form>
    @endif

    <a href="{{ route('dashboard') }}" class="btn btn-ghost w-full mt-3">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
        </svg>
        Kembali ke Dashboard
    </a>
</div>
@endsection
