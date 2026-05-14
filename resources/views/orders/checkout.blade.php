@extends('layouts.app')

@section('title', 'Checkout — ' . $invitation->coupleNames())

@section('content')
<div class="max-w-2xl mx-auto py-10 px-4">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Ringkasan Pesanan</h1>
        <p class="text-slate-500 mt-1">{{ $invitation->coupleNames() }}</p>
    </div>

    @if ($errors->has('checkout'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
            {{ $errors->first('checkout') }}
        </div>
    @endif

    @if (!$pricing['theme'])
        <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-lg text-amber-800 text-sm">
            Anda belum memilih tema. Silakan pilih tema terlebih dahulu melalui builder.
        </div>
    @endif

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
            <h2 class="font-semibold text-slate-700 text-sm uppercase tracking-wide">Detail Harga</h2>
        </div>

        <div class="divide-y divide-slate-100">
            {{-- Theme --}}
            <div class="flex justify-between items-center px-6 py-4">
                <div>
                    <p class="font-medium text-slate-800">Tema</p>
                    <p class="text-sm text-slate-500">{{ $pricing['theme']['name'] ?? 'Belum dipilih' }}</p>
                </div>
                <span class="font-semibold text-slate-800">
                    {{ \App\Services\PricingService::format($pricing['theme_price']) }}
                </span>
            </div>

            {{-- Animation Pack --}}
            <div class="flex justify-between items-center px-6 py-4">
                <div>
                    <p class="font-medium text-slate-800">Paket Animasi</p>
                    <p class="text-sm text-slate-500">{{ $pricing['animation']['name'] ?? 'Tidak ada' }}</p>
                </div>
                <span class="font-semibold text-slate-800">
                    {{ \App\Services\PricingService::format($pricing['animation_price']) }}
                </span>
            </div>

            {{-- Addons --}}
            @if (count($pricing['addons']))
                @foreach ($pricing['addons'] as $addon)
                    <div class="flex justify-between items-center px-6 py-4">
                        <div>
                            <p class="font-medium text-slate-800">{{ $addon['name'] }}</p>
                            <p class="text-sm text-slate-500">Add-on</p>
                        </div>
                        <span class="font-semibold text-slate-800">
                            {{ \App\Services\PricingService::format($addon['price']) }}
                        </span>
                    </div>
                @endforeach
            @else
                <div class="flex justify-between items-center px-6 py-4">
                    <p class="text-slate-500">Add-on</p>
                    <span class="text-slate-400">Tidak ada</span>
                </div>
            @endif
        </div>

        {{-- Total --}}
        <div class="flex justify-between items-center px-6 py-5 bg-rose-50 border-t border-rose-100">
            <p class="text-lg font-bold text-slate-800">Total Pembayaran</p>
            <p class="text-2xl font-bold text-rose-600">{{ $pricing['total_formatted'] }}</p>
        </div>
    </div>

    @if ($pricing['theme'])
        <form method="POST" action="{{ route('checkout.store', $invitation) }}">
            @csrf
            <button type="submit"
                class="w-full py-3 px-6 bg-rose-600 hover:bg-rose-700 text-white font-semibold rounded-lg transition">
                Buat Pesanan &amp; Lanjut ke Pembayaran
            </button>
        </form>
    @endif

    <a href="{{ route('dashboard') }}"
        class="block text-center mt-4 text-sm text-slate-500 hover:text-slate-700">
        ← Kembali ke Dashboard
    </a>
</div>
@endsection
