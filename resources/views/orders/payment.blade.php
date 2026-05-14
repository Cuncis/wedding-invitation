@extends('layouts.app')

@section('title', 'Pembayaran — ' . $order->order_number)

@section('content')
<div class="max-w-2xl mx-auto py-10 px-4">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Selesaikan Pembayaran</h1>
        <p class="text-slate-500 mt-1">{{ $order->order_number }} · {{ $order->invitation?->coupleNames() }}</p>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden mb-6">
        <div class="flex justify-between items-center px-6 py-5 bg-rose-50 border-b border-rose-100">
            <p class="text-lg font-bold text-slate-800">Total Pembayaran</p>
            <p class="text-2xl font-bold text-rose-600">
                {{ \App\Services\PricingService::format($order->total_amount) }}
            </p>
        </div>
        <div class="px-6 py-4 text-sm text-slate-500">
            Order kadaluarsa pada <strong>{{ $order->expires_at?->format('d M Y, H:i') }}</strong>.
        </div>
    </div>

    <div id="snap-container" class="mb-6">
        <button id="pay-button"
            class="w-full py-3 px-6 bg-rose-600 hover:bg-rose-700 text-white font-semibold rounded-lg transition">
            Bayar Sekarang
        </button>
    </div>

    <a href="{{ route('orders.show', $order) }}"
        class="block text-center mt-4 text-sm text-slate-500 hover:text-slate-700">
        ← Lihat Status Order
    </a>
</div>

@if ($order->payments->isNotEmpty())
    @php $snapToken = $order->payments->last()?->raw_payload['snap_token'] ?? null; @endphp
    @if ($snapToken)
        <script src="{{ config('midtrans.snap_url') }}" data-client-key="{{ config('midtrans.client_key') }}"></script>
        <script>
            document.getElementById('pay-button').addEventListener('click', function () {
                snap.pay('{{ $snapToken }}', {
                    onSuccess: function(result) {
                        window.location.href = '{{ route('orders.show', $order) }}';
                    },
                    onPending: function(result) {
                        window.location.href = '{{ route('orders.show', $order) }}';
                    },
                    onError: function(result) {
                        alert('Pembayaran gagal. Silakan coba lagi.');
                    },
                });
            });
        </script>
    @endif
@endif
@endsection
