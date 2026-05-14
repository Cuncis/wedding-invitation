@extends('layouts.app')

@section('title', 'RSVP — ' . $invitation->coupleNames())

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Daftar RSVP</h1>
            <p class="text-slate-500 text-sm mt-0.5">{{ $invitation->coupleNames() }}</p>
        </div>
        <a href="{{ route('rsvp.export', $invitation) }}"
            class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition">
            ⬇ Unduh CSV
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-100 text-center">
            <p class="text-3xl font-bold text-slate-900">{{ $stats['total'] }}</p>
            <p class="text-xs text-slate-500 mt-1">Total Respon</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-emerald-100 text-center">
            <p class="text-3xl font-bold text-emerald-600">{{ $stats['hadir'] }}</p>
            <p class="text-xs text-slate-500 mt-1">Hadir</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-rose-100 text-center">
            <p class="text-3xl font-bold text-rose-500">{{ $stats['tidak_hadir'] }}</p>
            <p class="text-xs text-slate-500 mt-1">Tidak Hadir</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-amber-100 text-center">
            <p class="text-3xl font-bold text-amber-500">{{ $stats['total_pax'] }}</p>
            <p class="text-xs text-slate-500 mt-1">Total Tamu Hadir</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Nama</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">No. HP</th>
                    <th class="px-4 py-3 text-center font-semibold text-slate-600">Kehadiran</th>
                    <th class="px-4 py-3 text-center font-semibold text-slate-600">Tamu</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Pesan</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Tanggal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse ($responses as $rsvp)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $rsvp->guest_name }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $rsvp->guest_phone ?? '—' }}</td>
                        <td class="px-4 py-3 text-center">
                            @if ($rsvp->attendance === 'hadir')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">✅ Hadir</span>
                            @elseif ($rsvp->attendance === 'tidak_hadir')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-700">❌ Tidak Hadir</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">🤔 Mungkin</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center text-slate-700">{{ $rsvp->pax }}</td>
                        <td class="px-4 py-3 text-slate-500 max-w-xs truncate">{{ $rsvp->message ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-400 whitespace-nowrap">{{ $rsvp->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-slate-400 italic">Belum ada konfirmasi kehadiran.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if ($responses->hasPages())
            <div class="px-4 py-3 border-t border-slate-100">{{ $responses->links() }}</div>
        @endif
    </div>

    <div class="mt-4">
        <a href="{{ route('invitation.show', $invitation->slug) }}" target="_blank"
            class="text-sm text-rose-600 hover:underline">
            ← Lihat halaman undangan →
        </a>
    </div>

</div>
@endsection
