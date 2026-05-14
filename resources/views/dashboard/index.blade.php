@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold">Halo, {{ auth()->user()->name }}</h1>
        <p class="text-slate-600">Kelola undangan digital pernikahan Anda</p>
    </div>
    <form method="POST" action="{{ route('dashboard.create') }}">
        @csrf
        <button type="submit" class="bg-rose-600 text-white px-4 py-2 rounded-md hover:bg-rose-700 font-medium">
            + Buat Undangan Baru
        </button>
    </form>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <div class="bg-white p-6 rounded-lg border border-slate-200">
        <p class="text-sm text-slate-500">Total Undangan</p>
        <p class="text-3xl font-bold mt-1">{{ $stats['total'] }}</p>
    </div>
    <div class="bg-white p-6 rounded-lg border border-slate-200">
        <p class="text-sm text-slate-500">Aktif</p>
        <p class="text-3xl font-bold mt-1 text-green-600">{{ $stats['active'] }}</p>
    </div>
    <div class="bg-white p-6 rounded-lg border border-slate-200">
        <p class="text-sm text-slate-500">Draft</p>
        <p class="text-3xl font-bold mt-1 text-amber-600">{{ $stats['draft'] }}</p>
    </div>
</div>

<div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-200">
        <h2 class="font-semibold">Undangan Anda</h2>
    </div>
    @forelse($invitations as $invitation)
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between last:border-0">
            <div>
                <p class="font-medium">{{ $invitation->coupleNames() ?: 'Undangan Belum Diisi' }}</p>
                <p class="text-sm text-slate-500">
                    Status:
                    <span class="font-medium {{ $invitation->status === 'active' ? 'text-green-600' : 'text-slate-700' }}">
                        {{ ucfirst($invitation->status) }}
                    </span>
                    @if($invitation->event_date)
                        · {{ $invitation->event_date->format('d M Y') }}
                    @endif
                </p>
            </div>
            <div class="flex gap-2">
                @if($invitation->isPublished())
                    <a href="{{ url('/' . $invitation->slug) }}" target="_blank"
                       class="text-sm px-3 py-1 border border-slate-300 rounded hover:bg-slate-50">Lihat</a>
                @endif
                <a href="#" class="text-sm bg-rose-600 text-white px-3 py-1 rounded hover:bg-rose-700">Edit</a>
            </div>
        </div>
    @empty
        <div class="px-6 py-12 text-center text-slate-500">
            <p>Belum ada undangan. Klik tombol di atas untuk membuat undangan pertama Anda.</p>
        </div>
    @endforelse
</div>
@endsection
