@php
    $colors     = $config?->colors ?? [];
    $typography = $config?->typography ?? [];
    $primary    = $colors['primary']   ?? '#c8756a';
    $secondary  = $colors['secondary'] ?? '#f5e6e0';
    $accent     = $colors['accent']    ?? '#8b4a42';
    $textColor  = $colors['text']      ?? '#3d2820';
    $headingFont = $typography['heading'] ?? 'Playfair Display';
    $bodyFont    = $typography['body']    ?? 'Lato';

    $themeView  = 'themes.' . str_replace('-', '_', $themeSlug ?? 'elegant_rose');
    $hasTheme   = View::exists($themeView);
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">

    <title>Undangan {{ $invitation->coupleNames() }}</title>
    <meta name="description" content="Undangan pernikahan {{ $invitation->coupleNames() }}. Kami mengundang kehadiran Anda.">

    {{-- Open Graph --}}
    <meta property="og:title" content="Undangan Pernikahan {{ $invitation->coupleNames() }}">
    <meta property="og:description" content="Kami mengundang kehadiran Bapak/Ibu/Saudara/i pada pernikahan kami.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family={{ urlencode($headingFont) }}:wght@400;700&family={{ urlencode($bodyFont) }}:wght@400;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary:    {{ $primary }};
            --secondary:  {{ $secondary }};
            --accent:     {{ $accent }};
            --text:       {{ $textColor }};
            --font-h:     '{{ $headingFont }}', serif;
            --font-b:     '{{ $bodyFont }}', sans-serif;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: var(--font-b); color: var(--text); background: var(--secondary); }
        h1,h2,h3 { font-family: var(--font-h); }
    </style>
    @vite('resources/css/app.css')
</head>
<body>

{{-- Theme-specific content --}}
@if ($hasTheme)
    @include($themeView, ['invitation' => $invitation, 'config' => $config])
@else
    @include('themes.default', ['invitation' => $invitation, 'config' => $config])
@endif

{{-- ── RSVP Section ─────────────────────────────── --}}
<section id="rsvp" style="background: var(--secondary); padding: 4rem 1rem;">
    <div style="max-width: 560px; margin: 0 auto;">
        <h2 style="font-family: var(--font-h); font-size: 2rem; color: var(--primary); text-align: center; margin-bottom: 0.5rem;">
            Konfirmasi Kehadiran
        </h2>
        <p style="text-align: center; color: var(--accent); margin-bottom: 2rem; font-size: 0.9rem;">
            {{ $rsvpStats['total'] }} tamu sudah mengonfirmasi · {{ $rsvpStats['hadir'] }} hadir ({{ $rsvpStats['total_pax'] }} orang)
        </p>

        @if (session('rsvp_success'))
            <div style="background: #d1fae5; border: 1px solid #6ee7b7; color: #065f46; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; text-align: center;">
                {{ session('rsvp_success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('rsvp.store', $invitation->slug) }}"
            style="background: white; border-radius: 0.5rem; padding: 2rem; border: 1px solid #e5e7eb;">
            @csrf

            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--accent); margin-bottom: 0.3rem;">
                    Nama Lengkap *
                </label>
                <input type="text" name="guest_name" required maxlength="100"
                    value="{{ old('guest_name') }}"
                    style="width: 100%; padding: 0.625rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.9rem;">
                @error('guest_name')<p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--accent); margin-bottom: 0.3rem;">
                    No. WhatsApp (opsional)
                </label>
                <input type="text" name="guest_phone" maxlength="20"
                    value="{{ old('guest_phone') }}"
                    style="width: 100%; padding: 0.625rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.9rem;">
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--accent); margin-bottom: 0.3rem;">
                    Konfirmasi Kehadiran *
                </label>
                <select name="attendance" required
                    style="width: 100%; padding: 0.625rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.9rem;">
                    <option value="">— Pilih —</option>
                    <option value="hadir" @selected(old('attendance') === 'hadir')>✅ Hadir</option>
                    <option value="tidak_hadir" @selected(old('attendance') === 'tidak_hadir')>❌ Tidak Hadir</option>
                    <option value="mungkin" @selected(old('attendance') === 'mungkin')>🤔 Mungkin Hadir</option>
                </select>
                @error('attendance')<p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--accent); margin-bottom: 0.3rem;">
                    Jumlah Orang
                </label>
                <input type="number" name="pax" min="1" max="10" value="{{ old('pax', 1) }}"
                    style="width: 80px; padding: 0.625rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.9rem;">
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--accent); margin-bottom: 0.3rem;">
                    Ucapan &amp; Doa (opsional)
                </label>
                <textarea name="message" rows="3" maxlength="500"
                    style="width: 100%; padding: 0.625rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.9rem; resize: vertical;">{{ old('message') }}</textarea>
            </div>

            <button type="submit"
                style="width: 100%; padding: 0.875rem; background: var(--primary); color: white; border: none; border-radius: 0.5rem; font-size: 1rem; font-weight: 600; cursor: pointer;">
                Kirim Konfirmasi
            </button>
        </form>
    </div>
</section>

</body>
</html>
