@extends('layouts.app')

@section('title', 'Order ' . $order->order_number)

@section('content')
<div class="max-w-2xl mx-auto py-10 px-4">

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">{{ $order->order_number }}</h1>
            <p class="text-slate-500 mt-1">{{ $order->invitation?->coupleNames() }}</p>
        </div>
        <span @class([
            'px-3 py-1 rounded-full text-sm font-semibold',
            'bg-yellow-100 text-yellow-700' => $order->status === 'pending',
            'bg-green-100 text-green-700'  => $order->status === 'paid',
            'bg-red-100 text-red-700'      => in_array($order->status, ['failed', 'refunded']),
        ])>
            {{ ucfirst($order->status) }}
        </span>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
            <h2 class="font-semibold text-slate-700 text-sm uppercase tracking-wide">Rincian Pesanan</h2>
        </div>
        <div class="divide-y divide-slate-100">
            <div class="flex justify-between px-6 py-4">
                <span class="text-slate-500">Harga Tema</span>
                <span class="font-medium">{{ \App\Services\PricingService::format($order->theme_price) }}</span>
            </div>
            <div class="flex justify-between px-6 py-4">
                <span class="text-slate-500">Harga Add-on</span>
                <span class="font-medium">{{ \App\Services\PricingService::format($order->addon_price) }}</span>
            </div>
            <div class="flex justify-between px-6 py-4">
                <span class="text-slate-500">Harga Animasi</span>
                <span class="font-medium">{{ \App\Services\PricingService::format($order->animation_price) }}</span>
            </div>
            <div class="flex justify-between px-6 py-5 bg-slate-50">
                <span class="font-bold text-slate-800">Total</span>
                <span class="font-bold text-rose-600 text-lg">{{ \App\Services\PricingService::format($order->total_amount) }}</span>
            </div>
        </div>
    </div>

    @if ($order->isPending())
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-5 mb-6">
            <p class="font-semibold text-amber-800">Menunggu Pembayaran</p>
            <p class="text-sm text-amber-700 mt-1">
                Order ini kadaluarsa pada
                <strong>{{ $order->expires_at?->format('d M Y, H:i') }}</strong>.
                Selesaikan pembayaran sebelum batas waktu.
            </p>
        </div>
    @endif

    @if ($order->payments->isNotEmpty())
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
                <h2 class="font-semibold text-slate-700 text-sm uppercase tracking-wide">Riwayat Pembayaran</h2>
            </div>
            @foreach ($order->payments as $payment)
                <div class="flex justify-between items-center px-6 py-4 border-b border-slate-100 last:border-0">
                    <div>
                        <p class="font-medium text-slate-800">{{ $payment->gateway }}</p>
                        <p class="text-sm text-slate-500">{{ $payment->payment_type }} · {{ $payment->created_at->format('d M Y') }}</p>
                    </div>
                    <span @class([
                        'px-2 py-0.5 rounded text-xs font-semibold',
                        'bg-green-100 text-green-700'  => $payment->status === 'settlement',
                        'bg-red-100 text-red-700'      => in_array($payment->status, ['cancel','deny','expire']),
                        'bg-yellow-100 text-yellow-700' => $payment->status === 'pending',
                    ])>
                        {{ $payment->status }}
                    </span>
                </div>
            @endforeach
        </div>
    @endif

    <a href="{{ route('dashboard') }}"
        class="block text-center mt-4 text-sm text-slate-500 hover:text-slate-700">
        ← Kembali ke Dashboard
    </a>
</div>
@endsection
