@php
    $config     = $invitation->config;
    $theme      = $config?->theme;
    $colors     = $config?->colors ?? [];
    $typography = $config?->typography ?? [];
    $content    = $config?->content ?? [];
    $addonIds   = $config?->addon_ids ?? [];

    $primary   = $colors['primary']   ?? '#c8756a';
    $secondary = $colors['secondary'] ?? '#f5e6e0';
    $accent    = $colors['accent']    ?? '#8b4a42';
    $text      = $colors['text']      ?? '#3d2820';

    $headingFont = $typography['heading'] ?? 'Playfair Display';
    $bodyFont    = $typography['body']    ?? 'Lato';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview — {{ $invitation->coupleNames() }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family={{ urlencode($headingFont) }}:wght@400;700&family={{ urlencode($bodyFont) }}:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --color-primary:   {{ $primary }};
            --color-secondary: {{ $secondary }};
            --color-accent:    {{ $accent }};
            --color-text:      {{ $text }};
            --font-heading: '{{ $headingFont }}', serif;
            --font-body:    '{{ $bodyFont }}', sans-serif;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: var(--font-body);
            color: var(--color-text);
            background: var(--color-secondary);
            min-height: 100vh;
        }
        h1, h2, h3 { font-family: var(--font-heading); font-weight: 700; }
        .cover {
            min-height: 100vh;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            text-align: center; padding: 2rem;
            background: linear-gradient(180deg, var(--color-secondary), #fff);
        }
        .cover .opening {
            font-style: italic; color: var(--color-accent);
            margin-bottom: 1rem;
        }
        .cover h1 {
            font-size: clamp(2.5rem, 6vw, 4.5rem);
            color: var(--color-primary);
            margin: 1rem 0;
        }
        .cover .tagline {
            font-size: 1.125rem; color: var(--color-accent);
            letter-spacing: 0.2em; text-transform: uppercase;
        }
        .section {
            max-width: 600px; margin: 0 auto;
            padding: 4rem 2rem; text-align: center;
        }
        .section h2 {
            color: var(--color-primary);
            font-size: 2rem; margin-bottom: 2rem;
        }
        .couple-card {
            background: white; border-radius: 1rem;
            padding: 2rem; margin-bottom: 1rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }
        .couple-card h3 {
            color: var(--color-primary);
            font-size: 1.5rem; margin-bottom: 0.5rem;
        }
        .couple-card p { color: var(--color-text); opacity: 0.7; }
        .event-card {
            background: var(--color-primary); color: white;
            border-radius: 1rem; padding: 2rem; margin: 1rem 0;
        }
        .event-card h3 { font-size: 1.5rem; margin-bottom: 0.5rem; }
        .event-card .date { font-size: 1.125rem; margin: 0.5rem 0; }
        .footer {
            background: var(--color-accent); color: white;
            padding: 2rem; text-align: center;
        }
        .badge-addons {
            position: fixed; bottom: 1rem; right: 1rem;
            background: rgba(0,0,0,0.7); color: white;
            padding: 0.5rem 1rem; border-radius: 999px;
            font-size: 0.75rem;
        }
    </style>
</head>
<body>

<section class="cover">
    @if (! empty($content['cover']['opening_text']))
        <p class="opening">{{ $content['cover']['opening_text'] }}</p>
    @endif
    <h1>{{ $invitation->coupleNames() }}</h1>
    <p class="tagline">{{ $content['cover']['tagline'] ?? 'Save the Date' }}</p>
    @if ($invitation->event_date)
        <p style="margin-top: 2rem; font-size: 1.25rem; color: var(--color-accent);">
            {{ \Carbon\Carbon::parse($invitation->event_date)->isoFormat('dddd, D MMMM Y') }}
        </p>
    @endif
</section>

<section class="section">
    <h2>Mempelai</h2>
    @if (! empty($content['couple']['groom_fullname']))
        <div class="couple-card">
            <h3>{{ $content['couple']['groom_fullname'] }}</h3>
            <p>{{ $content['couple']['groom_parents'] ?? '' }}</p>
        </div>
    @endif
    @if (! empty($content['couple']['bride_fullname']))
        <div class="couple-card">
            <h3>{{ $content['couple']['bride_fullname'] }}</h3>
            <p>{{ $content['couple']['bride_parents'] ?? '' }}</p>
        </div>
    @endif
</section>

@if (! empty($content['event']))
<section class="section">
    <h2>Acara</h2>
    @foreach (['akad' => 'Akad Nikah', 'resepsi' => 'Resepsi'] as $key => $label)
        @if (! empty($content['event'][$key]))
            <div class="event-card">
                <h3>{{ $label }}</h3>
                <p class="date">{{ $content['event'][$key]['date'] ?? '' }} · {{ $content['event'][$key]['time'] ?? '' }}</p>
                <p>{{ $content['event'][$key]['venue'] ?? '' }}</p>
                <p style="opacity: 0.85;">{{ $content['event'][$key]['address'] ?? '' }}</p>
            </div>
        @endif
    @endforeach
</section>
@endif

<footer class="footer">
    <p>{{ $content['closing']['thank_you'] ?? 'Merupakan suatu kehormatan apabila Bapak/Ibu/Saudara/i berkenan hadir.' }}</p>
    <p style="margin-top: 1rem; opacity: 0.7;">— {{ $invitation->coupleNames() }} —</p>
</footer>

@if (! empty($addonIds))
    <div class="badge-addons">{{ count($addonIds) }} addon aktif</div>
@endif

</body>
</html>
