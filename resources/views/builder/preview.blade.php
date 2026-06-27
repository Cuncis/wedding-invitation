@php
    use Illuminate\Support\Str;
    use App\Models\AnimationPack;

    $config = $invitation->config;
    $theme = $config?->theme;
    $colors = $config?->colors ?? [];
    $typography = $config?->typography ?? [];
    $content = $config?->content ?? [];
    $addonKeys = $addonKeys ?? [];
    $animationKey = $animationKey ?? 'free';
    $wishes = $wishes ?? collect();

    // Colors
    $primary = $colors['primary'] ?? '#c8756a';
    $secondary = $colors['secondary'] ?? '#f5e6e0';
    $accent = $colors['accent'] ?? '#8b4a42';
    $text = $colors['text'] ?? '#3d2820';

    // Fonts
    $headingFont = $typography['heading'] ?? 'Playfair Display';
    $bodyFont = $typography['body'] ?? 'Lato';

    // Section content shortcuts with safe defaults
    $cover = $content['cover'] ?? [];
    $doa = $content['doa'] ?? [];
    $couple = $content['couple'] ?? [];
    $event = $content['event'] ?? [];
    $gallery = $content['gallery'] ?? [];
    $gift = $content['gift'] ?? [];
    $wishesC = $content['wishes'] ?? [];
    $closing = $content['closing'] ?? [];
    $bgs = $content['backgrounds'] ?? [];

    $eventDate = $invitation->event_date ?? null;
    $groomShort = $cover['groom_short'] ?? ($couple['groom_fullname'] ?? '');
    $brideShort = $cover['bride_short'] ?? ($couple['bride_fullname'] ?? '');

    // Resolve a section background URL, honoring the global override toggle.
    $sectionBg = function (string $key) use ($bgs) {
        if (!empty($bgs['use_global']) && !empty($bgs['global'])) {
            return $bgs['global'];
        }
        return $bgs[$key] ?? null;
    };

    // Build a Google Calendar event URL from a date + time + venue.
    $gcalUrl = function (array $e, string $title) {
        if (empty($e['date'])) {
            return null;
        }
        try {
            $date = $e['date'];
            $time = $e['time'] ?? '00:00';
            $start = \Carbon\Carbon::parse($date . ' ' . $time, 'Asia/Jakarta');
            $end = $start->copy()->addHours(2);
            $fmt = 'Ymd\THis';
            $dates = $start->format($fmt) . '/' . $end->format($fmt);
            return 'https://calendar.google.com/calendar/render?action=TEMPLATE' .
                '&text=' .
                urlencode($title) .
                '&dates=' .
                $dates .
                '&details=' .
                urlencode($title) .
                '&location=' .
                urlencode(($e['venue'] ?? '') . ' ' . ($e['address'] ?? '')) .
                '&ctz=Asia/Jakarta';
        } catch (\Throwable $ex) {
            return null;
        }
    };

    $gallerySafe = array_slice($gallery['photos'] ?? [], 0, 30);
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview — {{ $invitation->coupleNames() }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family={{ urlencode($headingFont) }}:wght@400;600;700&family={{ urlencode($bodyFont) }}:wght@300;400;600&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --color-primary:
                {{ $primary }}
            ;
            --color-secondary:
                {{ $secondary }}
            ;
            --color-accent:
                {{ $accent }}
            ;
            --color-text:
                {{ $text }}
            ;
            --font-heading: '{{ $headingFont }}', serif;
            --font-body: '{{ $bodyFont }}', sans-serif;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: var(--font-body);
            color: var(--color-text);
            background: var(--color-secondary);
            min-height: 100vh;
            line-height: 1.6;
        }

        body:not(.invitation-opened) {
            overflow: hidden;
        }

        h1,
        h2,
        h3,
        h4 {
            font-family: var(--font-heading);
            font-weight: 700;
            line-height: 1.2;
        }

        /* ─── Cover gate ─── */
        .cover-gate {
            position: fixed;
            inset: 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 2rem;
            background: var(--color-secondary);
            transition: transform 0.9s cubic-bezier(.7, 0, .3, 1), opacity 0.7s ease;
        }

        body.invitation-opened .cover-gate {
            transform: translateY(-100%);
            opacity: 0;
            pointer-events: none;
        }

        .cover-photos {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            justify-content: center;
        }

        .cover-photo {
            width: 110px;
            height: 150px;
            border-radius: 0.5rem;
            background: var(--color-secondary);
            background-size: cover;
            background-position: center;
        }

        .cover-photo:nth-child(2) {
            transform: translateY(20px);
        }

        .cover-photo.empty {
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255, 255, 255, 0.7);
            font-size: 2rem;
        }

        .cover-heading {
            font-style: italic;
            color: var(--color-accent);
            font-size: 1rem;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
        }

        .cover-names {
            font-size: clamp(2.2rem, 7vw, 4rem);
            color: var(--color-primary);
            margin: 0.5rem 0;
        }

        .cover-amp {
            font-size: 0.9em;
            font-style: italic;
            opacity: 0.8;
        }

        .cover-date {
            color: var(--color-accent);
            margin-top: 1rem;
            font-size: 1rem;
            letter-spacing: 0.1em;
        }

        .cover-guest {
            margin-top: 2.5rem;
            font-size: 0.85rem;
            color: var(--color-text);
            opacity: 0.75;
            white-space: pre-line;
        }

        .cover-btn {
            margin-top: 1.25rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--color-primary);
            color: white;
            border: none;
            border-radius: 0.5rem;
            padding: 0.85rem 2rem;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .cover-btn:hover {
            transform: translateY(-2px);
        }

        /* ─── Generic section ─── */
        .section {
            position: relative;
            padding: 4.5rem 1.5rem;
            text-align: center;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .section-inner {
            max-width: 640px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }

        .section h2 {
            color: var(--color-primary);
            font-size: 2rem;
            margin-bottom: 1.5rem;
        }

        .section .subtitle {
            color: var(--color-accent);
            font-size: 0.8rem;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
        }

        .overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, var(--overlay, 0.4));
            z-index: 1;
        }

        .has-bg {
            color: white;
        }

        .has-bg h2,
        .has-bg .subtitle {
            color: white;
        }

        /* ─── Section 2 Doa ─── */
        .doa-text {
            font-size: 1.05rem;
            line-height: 1.9;
            font-style: italic;
            max-width: 540px;
            margin: 0 auto;
        }

        /* ─── Section 3 Mempelai ─── */
        .intro-text {
            font-size: 0.95rem;
            color: var(--color-text);
            opacity: 0.85;
            max-width: 540px;
            margin: 0 auto 3rem;
        }

        .has-bg .intro-text {
            color: white;
            opacity: 0.92;
        }

        .couple-row {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
            align-items: start;
        }

        @media (min-width: 720px) {
            .couple-row {
                grid-template-columns: 1fr auto 1fr;
            }
        }

        .couple-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(6px);
            border-radius: 0.5rem;
            padding: 1.75rem 1.5rem;
            color: var(--color-text);
        }

        .couple-photo {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            margin: 0 auto 1rem;
            background: var(--color-secondary);
            background-size: cover;
            background-position: center;
            border: 4px solid white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: rgba(255, 255, 255, 0.7);
        }

        .couple-name {
            color: var(--color-primary);
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .couple-parents {
            font-size: 0.85rem;
            opacity: 0.75;
        }

        .couple-social {
            display: flex;
            justify-content: center;
            gap: 0.6rem;
            margin-top: 1rem;
        }

        .couple-social a {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--color-primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            text-decoration: none;
            transition: transform 0.2s;
        }

        .couple-social a:hover {
            transform: scale(1.1);
        }

        .couple-amp {
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--font-heading);
            font-size: 3rem;
            color: var(--color-primary);
            padding: 0 1rem;
        }

        .has-bg .couple-amp {
            color: white;
        }

        /* ─── Section 4 Event ─── */
        .countdown {
            display: flex;
            justify-content: center;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin: 2rem 0;
        }

        .cd-box {
            background: var(--color-primary);
            color: white;
            border-radius: 0.375rem;
            padding: 0.85rem 1rem;
            min-width: 72px;
            text-align: center;
        }

        .cd-box .num {
            display: block;
            font-family: var(--font-heading);
            font-size: 2rem;
            font-weight: 700;
        }

        .cd-box small {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            opacity: 0.85;
        }

        .event-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.25rem;
            margin: 2rem 0;
        }

        @media (min-width: 720px) {
            .event-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        .event-card {
            background: rgba(255, 255, 255, 0.96);
            border-radius: 0.5rem;
            padding: 1.5rem;
            text-align: left;
            color: var(--color-text);
        }

        .event-card .label {
            color: var(--color-primary);
            font-weight: 700;
            font-size: 0.75rem;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            margin-bottom: 0.75rem;
        }

        .event-card h3 {
            color: var(--color-accent);
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
        }

        .event-card .when {
            font-weight: 600;
            margin: 0.4rem 0 0.8rem;
        }

        .event-card .addr {
            font-size: 0.85rem;
            opacity: 0.8;
            margin-bottom: 1rem;
        }

        .event-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .btn-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: var(--color-primary);
            color: white;
            border: none;
            border-radius: 0.5rem;
            padding: 0.55rem 1.1rem;
            font-size: 0.8rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: transform 0.15s;
        }

        .btn-pill:hover {
            transform: translateY(-1px);
        }

        .btn-pill.ghost {
            background: transparent;
            color: var(--color-primary);
            border: 1.5px solid var(--color-primary);
        }

        /* ─── Section 5 Gallery ─── */
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .gallery-grid .cell {
            height: 200px;
            border-radius: 0.375rem;
            overflow: hidden;
            background: var(--color-secondary);
            cursor: zoom-in;
            position: relative;
        }

        .gallery-grid .cell img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            display: block;
            transition: transform 0.4s;
        }

        .gallery-grid .cell:hover img {
            transform: scale(1.06);
        }

        .gallery-grid .cell.empty {
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255, 255, 255, 0.85);
            font-size: 1.4rem;
            cursor: default;
        }

        .lightbox {
            position: fixed;
            inset: 0;
            z-index: 2000;
            background: rgba(0, 0, 0, 0.92);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .lightbox.open {
            display: flex;
        }

        .lightbox img {
            max-width: 100%;
            max-height: 92vh;
            border-radius: 0.375rem;
        }

        .lightbox .close-btn {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            background: rgba(255, 255, 255, 0.15);
            color: white;
            border: none;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            font-size: 1.5rem;
            cursor: pointer;
        }

        /* ─── Section 6 Gift ─── */
        .gift-receiver {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            text-align: left;            color: var(--color-text);
        }

        .gift-receiver .label {
            font-size: 0.7rem;
            color: var(--color-accent);
            font-weight: 600;
            letter-spacing: 0.15em;
            text-transform: uppercase;
        }

        .gift-receiver .name {
            font-family: var(--font-heading);
            font-size: 1.25rem;
            color: var(--color-primary);
            margin: 0.25rem 0;
        }

        .gift-receiver .addr {
            font-size: 0.85rem;
            opacity: 0.75;
        }

        .bank-list {
            display: grid;
            gap: 0.75rem;
        }

        .bank-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 0.5rem;
            padding: 1.25rem;
            text-align: left;            display: flex;
            align-items: center;
            gap: 1rem;
            color: var(--color-text);
        }

        .bank-logo {
            width: 56px;
            height: 56px;
            border-radius: 0.375rem;
            background: var(--color-secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--font-heading);
            font-weight: 700;
            color: var(--color-primary);
            font-size: 0.85rem;
            flex-shrink: 0;
            overflow: hidden;
        }

        .bank-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 6px;
        }

        .bank-meta {
            flex: 1;
            min-width: 0;
        }

        .bank-meta .bname {
            font-size: 0.7rem;
            color: var(--color-accent);
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }

        .bank-meta .bno {
            font-family: var(--font-heading);
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--color-text);
            margin: 0.15rem 0;
            word-break: break-all;
        }

        .bank-meta .bholder {
            font-size: 0.78rem;
            opacity: 0.7;
        }

        .copy-btn {
            background: var(--color-primary);
            color: white;
            border: none;
            border-radius: 0.5rem;
            padding: 0.45rem 1rem;
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            flex-shrink: 0;
            transition: background 0.2s;
        }

        .copy-btn.copied {
            background: #10b981;
        }

        /* ─── E-Wallet list ─── */
        .ewallet-list {
            display: grid;
            gap: 0.75rem;
            margin-top: 1.25rem;
        }

        .ewallet-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 0.5rem;
            padding: 1.25rem;
            text-align: left;            display: flex;
            align-items: center;
            gap: 1rem;
            color: var(--color-text);
        }

        .ewallet-icon {
            width: 56px;
            height: 56px;
            border-radius: 0.375rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.75rem;
            flex-shrink: 0;
            letter-spacing: 0.02em;
        }

        .ewallet-icon.gopay {
            background: #00aed6;
            color: #fff;
        }

        .ewallet-icon.dana {
            background: #118eea;
            color: #fff;
        }

        .ewallet-icon.ovo {
            background: #4c3494;
            color: #fff;
        }

        .ewallet-icon.shopeepay {
            background: #f7441e;
            color: #fff;
        }

        .ewallet-icon.linkaja {
            background: #e82529;
            color: #fff;
        }

        .ewallet-icon.qris {
            background: #cc2128;
            color: #fff;
        }

        .gift-section-label {
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: var(--color-accent);
            margin: 1.75rem 0 0.5rem;
        }

        /* ─── Live Streaming ─── */
        .livestream-card {
            background: rgba(255, 255, 255, 0.97);
            border-radius: 0.5rem;
            overflow: hidden;            color: var(--color-text);
        }

        .livestream-embed {
            position: relative;
            width: 100%;
            padding-top: 56.25%;
            background: #000;
        }

        .livestream-embed iframe {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            border: none;
            display: block;
        }

        .livestream-body {
            padding: 1.5rem;
            text-align: center;
        }

        .livestream-body .ls-desc {
            font-size: 0.88rem;
            line-height: 1.6;
            opacity: 0.8;
            margin-bottom: 1rem;
        }

        .livestream-schedule {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--color-secondary);
            color: var(--color-accent);
            font-size: 0.78rem;
            font-weight: 600;
            padding: 0.4rem 1rem;
            border-radius: 999px;
            margin-bottom: 1.25rem;
        }

        .ls-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--color-primary);
            color: #fff;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 0.8rem 2rem;
            border-radius: 0.5rem;
            text-decoration: none;
            transition: opacity 0.2s;
        }

        .ls-btn:hover {
            opacity: 0.9;
        }

        /* ─── Love Story Timeline ─── */
        .timeline {
            position: relative;
            padding: 1rem 0;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 50%;
            top: 0;
            bottom: 0;
            width: 2px;
            background: var(--color-primary);
            opacity: 0.25;
            transform: translateX(-50%);
        }

        .timeline-item {
            display: flex;
            gap: 1.5rem;
            align-items: flex-start;
            margin-bottom: 2.5rem;
            position: relative;
        }

        .timeline-item:nth-child(odd) {
            flex-direction: row-reverse;
        }

        .timeline-item:nth-child(odd) .tl-card {
            text-align: right;
        }

        .tl-dot {
            position: absolute;
            left: 50%;
            top: 1.25rem;
            transform: translateX(-50%);
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: var(--color-primary);
            border: 3px solid var(--color-secondary);
            box-shadow: 0 0 0 2px var(--color-primary);
            flex-shrink: 0;
            z-index: 1;
        }

        .tl-card {
            width: calc(50% - 2rem);
            background: rgba(255, 255, 255, 0.95);
            border-radius: 0.5rem;
            padding: 1.25rem;
            color: var(--color-text);
        }

        .tl-date {
            font-size: 0.68rem;
            font-weight: 700;
            color: var(--color-accent);
            letter-spacing: 0.12em;
            text-transform: uppercase;
            margin-bottom: 0.35rem;
        }

        .tl-title {
            font-family: var(--font-heading);
            font-size: 1.1rem;
            color: var(--color-primary);
            margin-bottom: 0.5rem;
            line-height: 1.3;
        }

        .tl-desc {
            font-size: 0.85rem;
            line-height: 1.6;
            opacity: 0.8;
        }

        .tl-photo {
            width: 100%;
            height: 160px;
            object-fit: cover;
            border-radius: 0.375rem;
            margin-bottom: 0.75rem;
        }

        .tl-intro {
            font-size: 0.9rem;
            opacity: 0.8;
            line-height: 1.7;
            margin-bottom: 2rem;
            font-style: italic;
        }

        @media (max-width: 600px) {
            .timeline::before {
                left: 20px;
            }

            .timeline-item,
            .timeline-item:nth-child(odd) {
                flex-direction: column;
                padding-left: 3rem;
            }

            .timeline-item:nth-child(odd) .tl-card {
                text-align: left;
            }

            .tl-dot {
                left: 20px;
            }

            .tl-card {
                width: 100%;
            }
        }

        /* ─── Section 7 Wishes ─── */
        .wish-form {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
            text-align: left;            color: var(--color-text);
        }

        .wish-form label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--color-accent);
            margin-top: 0.85rem;
            margin-bottom: 0.3rem;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .wish-form input,
        .wish-form textarea,
        .wish-form select {
            width: 100%;
            padding: 0.65rem 0.9rem;
            border: 1.5px solid #e2e8f0;
            border-radius: 0.375rem;
            font-size: 0.9rem;
            font-family: inherit;
            color: var(--color-text);
            background: white;
        }

        .wish-form textarea {
            resize: vertical;
            min-height: 80px;
        }

        .wish-form button.send-btn {
            margin-top: 1.25rem;
            width: 100%;
            background: var(--color-primary);
            color: white;
            border: none;
            padding: 0.85rem;
            border-radius: 0.375rem;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
        }

        .wish-list {
            text-align: left;
        }

        .wish-item {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 0.5rem;
            padding: 1rem 1.2rem;
            margin-bottom: 0.65rem;            color: var(--color-text);
        }

        .wish-item-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.4rem;
        }

        .wish-item .who {
            font-weight: 700;
            color: var(--color-primary);
            font-size: 0.95rem;
        }

        .wish-item .meta {
            font-size: 0.7rem;
            opacity: 0.6;
        }

        .wish-item .att {
            display: inline-block;
            font-size: 0.65rem;
            padding: 0.15rem 0.55rem;
            border-radius: 999px;
            margin-left: 0.4rem;
            background: #d1fae5;
            color: #065f46;
        }

        .wish-item .att.no {
            background: #fee2e2;
            color: #991b1b;
        }

        .wish-item .msg {
            font-size: 0.88rem;
            opacity: 0.85;
            line-height: 1.55;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.4rem;
            margin-top: 1.25rem;
        }

        .pagination button {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid #e2e8f0;
            color: var(--color-text);
            cursor: pointer;
            width: 32px;
            height: 32px;
            border-radius: 0.375rem;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .pagination button.active {
            background: var(--color-primary);
            color: white;
            border-color: var(--color-primary);
        }

        .pagination button:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        .wishes-footer {
            margin-top: 2rem;
            font-style: italic;
            font-size: 0.9rem;
            color: var(--color-accent);
        }

        .has-bg .wishes-footer {
            color: white;
        }

        /* ─── Section 8 Closing ─── */
        .closing {
            padding: 5rem 1.5rem 4rem;
            background: var(--color-primary);
            color: white;
            text-align: center;
        }

        .closing h2 {
            color: white;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        .closing p.thanks {
            font-size: 1.05rem;
            opacity: 0.9;
            max-width: 480px;
            margin: 0 auto 2.5rem;
        }

        .closing .signature {
            font-family: var(--font-heading);
            font-size: 1.5rem;
            margin-bottom: 0.75rem;
        }

        .watermark {
            margin-top: 3rem;
            font-size: 0.7rem;
            opacity: 0.7;
            letter-spacing: 0.05em;
        }

        .watermark a {
            color: white;
            text-decoration: underline;
        }

        /* ─── Music Player addon ─── */
        .music-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 200;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
            border-top: 1px solid rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.6rem 1.25rem;
        }

        .music-btn {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: var(--color-primary);
            color: white;
            border: none;
            font-size: 1rem;
            cursor: pointer;
        }

        .music-info .song {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--color-text);
        }

        .music-info .artist {
            font-size: 0.68rem;
            color: var(--color-text);
            opacity: 0.6;
        }

        /* ─── Toast ─── */
        .toast {
            position: fixed;
            bottom: 5.5rem;
            left: 50%;
            transform: translateX(-50%) translateY(20px);
            background: rgba(0, 0, 0, 0.85);
            color: white;
            padding: 0.6rem 1.25rem;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 500;
            z-index: 1500;
            opacity: 0;
            transition: opacity 0.25s, transform 0.25s;
            pointer-events: none;
        }

        .toast.show {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }

        /* ─── Animation pack ─── */
        .anim {
            opacity: 1;
        }

        body.anim-free .anim {
            animation: fadeInUp 0.7s ease both;
        }

        body.anim-free .anim:nth-child(2) {
            animation-delay: 0.1s;
        }

        body.anim-free .anim:nth-child(3) {
            animation-delay: 0.2s;
        }

        body.anim-free .anim:nth-child(4) {
            animation-delay: 0.3s;
        }

        {{ '@' }}
        keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        body.anim-standard .anim,
        body.anim-premium .anim {
            opacity: 0;
            transform: translateY(40px);
        }

        #petals-canvas {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 9999;
        }

        /* ─── Animation: Free (fade-in) ─── */
        @if ($animationKey === AnimationPack::KEY_FREE || $animationKey === AnimationPack::KEY_STANDARD || $animationKey === AnimationPack::KEY_PREMIUM)
            .anim {
                opacity: 0;
                transform: translateY(24px);
                transition: opacity 0.7s ease, transform 0.7s ease;
            }

            .anim.is-visible {
                opacity: 1;
                transform: translateY(0);
            }

        @endif
    </style>
</head>

<body>

    {{-- ════════════════════ Section 1: Cover gate ════════════════════ --}}
    <div class="cover-gate" id="cover-gate" @if ($sectionBg('cover'))
        style="background-image:url('{{ $sectionBg('cover') }}'); background-size:cover; background-position:center;"
    @endif>
        <div class="cover-photos">
            @if (!empty($cover['photo_1']))
                <div class="cover-photo" style="background-image:url('{{ $cover['photo_1'] }}')"></div>
            @else
                <div class="cover-photo empty">📷</div>
            @endif
            @if (!empty($cover['photo_2']))
                <div class="cover-photo" style="background-image:url('{{ $cover['photo_2'] }}')"></div>
            @endif
        </div>

        <p class="cover-heading">{{ $cover['heading'] ?? 'The Wedding of' }}</p>

        @if ($groomShort || $brideShort)
            <h1 class="cover-names">
                {{ $groomShort ?: 'REKSA' }}<br>
                <span class="cover-amp">&amp;</span><br>
                {{ $brideShort ?: 'TUTI' }}
            </h1>
        @else
            <h1 class="cover-names">{{ $invitation->coupleNames() }}</h1>
        @endif

        @if (!empty($cover['date_text']))
            <p class="cover-date">{{ $cover['date_text'] }}</p>
        @elseif ($eventDate)
            <p class="cover-date">{{ \Carbon\Carbon::parse($eventDate)->isoFormat('dddd, D MMMM Y') }}</p>
        @endif

        <p class="cover-guest">{{ $cover['guest_label'] ?? "Kepada Yth.\nTamu Undangan" }}</p>

        <button class="cover-btn" id="open-invitation-btn">
            ✉️ {{ $cover['button_label'] ?? 'Buka Undangan' }}
        </button>
    </div>

    {{-- ════════════════════ Section 2: Doa & Surat ════════════════════ --}}
    @php $bg = $sectionBg('doa'); @endphp
    <section class="section anim {{ $bg ? 'has-bg' : '' }}"
        style="--overlay: {{ $doa['overlay'] ?? 0.45 }}; {{ $bg ? "background-image:url('$bg');" : '' }}">
        @if ($bg)
            <div class="overlay"></div>
        @endif
        <div class="section-inner">
            <p class="subtitle">Pembuka</p>
            <h2>{{ $doa['heading'] ?? 'Doa & Pengantar' }}</h2>
            <p class="doa-text">
                {{ $doa['description'] ?? 'Bismillahirrahmanirrahim. Segala puji bagi Allah, Tuhan semesta alam.' }}
            </p>
        </div>
    </section>

    {{-- ════════════════════ Section 3: Mempelai ════════════════════ --}}
    @php $bg = $sectionBg('couple'); @endphp
    <section class="section anim {{ $bg ? 'has-bg' : '' }}"
        style="--overlay: 0.5; {{ $bg ? "background-image:url('$bg');" : '' }}">
        @if ($bg)
            <div class="overlay"></div>
        @endif
        <div class="section-inner">
            <p class="subtitle">Mempelai</p>
            <h2>The Wedding Couple</h2>
            <p class="intro-text">
                {{ $couple['intro_text'] ?? 'Tanpa mengurangi rasa hormat, perkenankan kami mengundang Bapak/Ibu/Saudara/i untuk menghadiri acara pernikahan kami.' }}
            </p>

            <div class="couple-row">
                @foreach (['groom' => 'Mempelai Pria', 'bride' => 'Mempelai Wanita'] as $who => $label)
                    @php
                        $name = $couple[$who . '_fullname'] ?? '';
                        $parents = $couple[$who . '_parents'] ?? '';
                        $photo = $couple[$who . '_photo'] ?? null;
                        $socials = $couple[$who . '_social'] ?? [];
                    @endphp
                    <div class="couple-card">
                        <div class="couple-photo" @if ($photo) style="background-image:url('{{ $photo }}')" @endif>
                            <span class="couple-photo-emoji" @if ($photo) style="display:none"
                            @endif>{{ $who === 'groom' ? '🤵' : '👰' }}</span>
                        </div>
                        <p class="subtitle" style="color:var(--color-accent); margin-bottom:0.4rem;">
                            {{ $label }}
                        </p>
                        <h3 class="couple-name">
                            {{ $name ?: ($who === 'groom' ? 'Nama Mempelai Pria' : 'Nama Mempelai Wanita') }}
                        </h3>
                        @if ($parents)
                            <p class="couple-parents">{{ $parents }}</p>
                        @endif
                        @if (array_filter($socials))
                            <div class="couple-social">
                                @if (!empty($socials['instagram']))
                                    <a href="https://instagram.com/{{ ltrim($socials['instagram'], '@') }}" target="_blank"
                                        rel="noopener" title="Instagram">IG</a>
                                @endif
                                @if (!empty($socials['facebook']))
                                    <a href="{{ Str::startsWith($socials['facebook'], 'http') ? $socials['facebook'] : 'https://facebook.com/' . $socials['facebook'] }}"
                                        target="_blank" rel="noopener" title="Facebook">FB</a>
                                @endif
                                @if (!empty($socials['tiktok']))
                                    <a href="https://tiktok.com/@{{ ltrim($socials['tiktok'], '@') }}" target="_blank"
                                        rel="noopener" title="TikTok">TT</a>
                                @endif
                                @if (!empty($socials['twitter']))
                                    <a href="https://twitter.com/{{ ltrim($socials['twitter'], '@') }}" target="_blank"
                                        rel="noopener" title="Twitter / X">X</a>
                                @endif
                            </div>
                        @endif
                    </div>
                    @if ($who === 'groom')
                        <div class="couple-amp">&amp;</div>
                    @endif
                @endforeach
            </div>
        </div>
    </section>

    {{-- ════════════════════ Section 4: Main Event ════════════════════ --}}
    @php $bg = $sectionBg('event'); @endphp
    <section class="section anim {{ $bg ? 'has-bg' : '' }}"
        style="--overlay: 0.55; {{ $bg ? "background-image:url('$bg');" : '' }}">
        @if ($bg)
            <div class="overlay"></div>
        @endif
        <div class="section-inner">
            <p class="subtitle">Save the Date</p>
            <h2>Acara Pernikahan</h2>

            @if ($eventDate)
                <div class="countdown" data-target="{{ $eventDate }}">
                    <div class="cd-box"><span class="num" data-cd="d">00</span><small>Hari</small></div>
                    <div class="cd-box"><span class="num" data-cd="h">00</span><small>Jam</small></div>
                    <div class="cd-box"><span class="num" data-cd="m">00</span><small>Menit</small></div>
                    <div class="cd-box"><span class="num" data-cd="s">00</span><small>Detik</small></div>
                </div>
            @endif

            <div class="event-grid">
                @foreach (['akad' => ['Akad Nikah', '💍'], 'resepsi' => ['Resepsi', '🎉']] as $key => $meta)
                    @php $e = $event[$key] ?? []; @endphp
                    <div class="event-card">
                        <p class="label">{{ $meta[1] }} {{ $meta[0] }}</p>
                        @if (!empty($e['venue']))
                            <h3>{{ $e['venue'] }}</h3>
                        @endif
                        <p class="when">
                            {{ !empty($e['day']) ? $e['day'] . ', ' : '' }}{{ $e['date'] ?? '—' }}
                            @if (!empty($e['time']))
                                · {{ $e['time'] }} WIB
                            @endif
                        </p>
                        @if (!empty($e['address']))
                            <p class="addr">{{ $e['address'] }}</p>
                        @endif
                        <div class="event-actions">
                            @php
                                $maps = $e['maps_url'] ?? null;
                                if (!$maps && !empty($e['address'])) {
                                    $maps =
                                        'https://www.google.com/maps/search/?api=1&query=' . urlencode($e['address']);
                                }
                                $cal =
                                    $e['calendar_url'] ??
                                    $gcalUrl($e, ($invitation->coupleNames() ?: 'Wedding') . ' — ' . $meta[0]);
                            @endphp
                            @if ($maps)
                                <a href="{{ $maps }}" target="_blank" rel="noopener" class="btn-pill">📍
                                    Kunjungi Lokasi</a>
                            @endif
                            @if ($cal)
                                <a href="{{ $cal }}" target="_blank" rel="noopener" class="btn-pill ghost">📅
                                    Save Date</a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ════════════════════ Countdown Timer Addon ════════════════════ --}}
    @if ($countdownConfig && ($countdownConfig['enabled'] ?? true) && !empty($countdownConfig['target_date']))
        <section class="section anim" style="background: var(--color-primary); padding: 3rem 1.5rem;">
            <div class="section-inner">
                @if (!empty($countdownConfig['label']))
                    <p class="text-sm uppercase tracking-widest text-white/70 mb-4" style="letter-spacing: 0.15em;">
                        {{ $countdownConfig['label'] }}
                    </p>
                @endif
                <div class="countdown" data-target="{{ $countdownConfig['target_date'] }}"
                    data-label="{{ $countdownConfig['label'] ?? '' }}">
                    <div class="cd-box"><span class="num" data-cd="d">00</span><small>Hari</small></div>
                    <div class="cd-box"><span class="num" data-cd="h">00</span><small>Jam</small></div>
                    <div class="cd-box"><span class="num" data-cd="m">00</span><small>Menit</small></div>
                    <div class="cd-box"><span class="num" data-cd="s">00</span><small>Detik</small></div>
                </div>
            </div>
        </section>
    @endif

    {{-- ════════════════════ Section 4b: Maps ════════════════════ --}}
    @php
        $mapsEmbedUrl = null;
        if ($mapsConfig) {
            $mapsAddress = $mapsConfig['address'] ?? null;
            $mapsRawUrl = $mapsConfig['embed_url'] ?? null;
            if ($mapsAddress) {
                $mapsEmbedUrl =
                    'https://maps.google.com/maps?q=' .
                    urlencode($mapsAddress) .
                    '&t=&z=17&ie=UTF8&iwloc=&output=embed';
            } elseif ($mapsRawUrl) {
                if (
                    str_contains($mapsRawUrl, 'maps.google.com/maps?q=') ||
                    str_contains($mapsRawUrl, 'maps.google.com/embed?')
                ) {
                    $mapsEmbedUrl = $mapsRawUrl;
                } else {
                    $mapsEmbedUrl =
                        'https://maps.google.com/maps?q=' .
                        urlencode($mapsRawUrl) .
                        '&t=&z=17&ie=UTF8&iwloc=&output=embed';
                }
            }
        }
    @endphp
    @if ($mapsEmbedUrl)
        @php $bg = $sectionBg('maps'); @endphp
        <section class="section anim {{ $bg ? 'has-bg' : '' }}"
            style="--overlay: 0.5; {{ $bg ? "background-image:url('$bg');" : '' }}">
            @if ($bg)
                <div class="overlay"></div>
            @endif
            <div class="section-inner">
                <p class="subtitle">Location</p>
                <h2>Peta Lokasi</h2>
                @if (!empty($mapsConfig['address']))
                    <div style="margin: 1.5rem 0 2rem;">
                        <p class="text-sm" style="color: #163A51; font-style: italic; letter-spacing: 0.03em;">
                            {{ $mapsConfig['address'] }}
                        </p>
                    </div>
                @endif
                <div class="map-embed">
                    <iframe src="{{ $mapsEmbedUrl }}" width="100%" height="350"
                        style="border:0; border-radius: 0.375rem; display: block;" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </section>
    @endif

    {{-- ════════════════════ Section 5: Galeri ════════════════════ --}}
    @if (!empty($gallerySafe) || in_array('photo_gallery', $addonKeys))
        @php
            $bg = $sectionBg('gallery');
            $galleryColumns = $gallery['columns'] ?? 3;
            $galleryLightbox = $gallery['lightbox'] ?? true;
        @endphp
        <section class="section anim {{ $bg ? 'has-bg' : '' }}"
            style="--overlay: 0.5; {{ $bg ? "background-image:url('$bg');" : '' }}">
            @if ($bg)
                <div class="overlay"></div>
            @endif
            <div class="section-inner">
                <p class="subtitle">Our Moments</p>
                <h2>Galeri Foto</h2>
                <div class="gallery-grid" id="gallery-grid" @if ($galleryLightbox) data-lightbox="true" @endif
                    style="grid-template-columns: repeat({{ $galleryColumns }}, 1fr);">
                    @if (!empty($gallerySafe))
                        @foreach ($gallerySafe as $photo)
                            <div class="cell" data-src="{{ $photo }}">
                                <img src="{{ $photo }}" alt="Foto" loading="lazy">
                            </div>
                        @endforeach
                    @else
                        @for ($i = 0; $i < 6; $i++)
                            <div class="cell empty">🌸</div>
                        @endfor
                    @endif
                </div>
            </div>
        </section>
    @endif

    {{-- ════════════════════ Love Story ════════════════════ --}}
    @if (in_array('love_story', $addonKeys))
        @php
            $loveStory = $content['love_story'] ?? [];
            $lsHeading = $loveStory['heading'] ?? 'Kisah Cinta Kami';
            $lsIntro = $loveStory['intro'] ?? '';
            $lsItems = $loveStory['items'] ?? [];
            $lsBg = $sectionBg('love_story');
        @endphp
        <section class="section anim {{ $lsBg ? 'has-bg' : '' }}"
            style="--overlay: 0.45; {{ $lsBg ? "background-image:url('$lsBg');" : '' }}">
            @if ($lsBg)
                <div class="overlay"></div>
            @endif
            <div class="section-inner">
                <p class="subtitle">Our Journey</p>
                <h2>{{ $lsHeading }}</h2>

                @if ($lsIntro)
                    <p class="tl-intro">{{ $lsIntro }}</p>
                @endif

                @if (!empty($lsItems))
                    <div class="timeline">
                        @foreach ($lsItems as $lsItem)
                            <div class="timeline-item">
                                <div class="tl-dot"></div>
                                <div class="tl-card">
                                    @if (!empty($lsItem['photo']))
                                        <img src="{{ $lsItem['photo'] }}" alt="{{ $lsItem['title'] ?? '' }}" class="tl-photo">
                                    @endif
                                    @if (!empty($lsItem['date']))
                                        <p class="tl-date">{{ $lsItem['date'] }}</p>
                                    @endif
                                    @if (!empty($lsItem['title']))
                                        <p class="tl-title">{{ $lsItem['title'] }}</p>
                                    @endif
                                    @if (!empty($lsItem['description']))
                                        <p class="tl-desc">{{ $lsItem['description'] }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="timeline">
                        @foreach ([['Maret 2019', 'Pertama Bertemu', 'Takdir mempertemukan kami di sebuah acara yang tak terduga.'], ['Juni 2020', 'Mulai Berpacaran', 'Dengan penuh keberanian, ia mengungkapkan perasaannya.'], ['Desember 2023', 'Lamaran', 'Di hari yang penuh bunga, ia berlutut dan bertanya: maukah kamu?']] as $demo)
                            <div class="timeline-item">
                                <div class="tl-dot"></div>
                                <div class="tl-card">
                                    <p class="tl-date">{{ $demo[0] }}</p>
                                    <p class="tl-title">{{ $demo[1] }}</p>
                                    <p class="tl-desc">{{ $demo[2] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    @endif

    {{-- ════════════════════ Section 6: Wedding Gift ════════════════════ --}}
    @if (in_array('digital_gift', $addonKeys))
        @php $bg = $sectionBg('gift'); @endphp
        <section class="section anim {{ $bg ? 'has-bg' : '' }}"
            style="--overlay: 0.5; {{ $bg ? "background-image:url('$bg');" : '' }}">
            @if ($bg)
                <div class="overlay"></div>
            @endif
            <div class="section-inner">
                <p class="subtitle">Wedding Gift</p>
                <h2>Tanda Kasih</h2>

                @if (!empty($gift['receiver_name']) || !empty($gift['address']))
                    <div class="gift-receiver">
                        <p class="label">Penerima Kado</p>
                        <p class="name">{{ $gift['receiver_name'] ?? '—' }}</p>
                        <p class="addr">{{ $gift['address'] ?? '' }}</p>
                    </div>
                @endif

                @php
                    $banks = $gift['banks'] ?? [];
                    if (empty($banks)) {
                        $banks = [
                            [
                                'name' => 'BCA',
                                'account_no' => '1234567890',
                                'account_holder' => 'a.n. Nama Mempelai',
                                'logo' => null,
                            ],
                            [
                                'name' => 'Mandiri',
                                'account_no' => '0987654321',
                                'account_holder' => 'a.n. Nama Mempelai',
                                'logo' => null,
                            ],
                        ];
                    }
                @endphp

                @if (!empty($banks))
                    <p class="gift-section-label">Rekening Bank</p>
                @endif
                <div class="bank-list">
                    @foreach ($banks as $bank)
                        <div class="bank-card">
                            <div class="bank-logo">
                                @if (!empty($bank['logo']))
                                    <img src="{{ $bank['logo'] }}" alt="{{ $bank['name'] ?? '' }}">
                                @else
                                    {{ strtoupper(substr($bank['name'] ?? '?', 0, 4)) }}
                                @endif
                            </div>
                            <div class="bank-meta">
                                <p class="bname">{{ $bank['name'] ?? 'Bank' }}</p>
                                <p class="bno">{{ $bank['account_no'] ?? '—' }}</p>
                                <p class="bholder">{{ $bank['account_holder'] ?? '' }}</p>
                            </div>
                            @if (!empty($bank['account_no']))
                                <button type="button" class="copy-btn" data-copy="{{ $bank['account_no'] }}">Salin</button>
                            @endif
                        </div>
                    @endforeach
                </div>

                @php
                    $ewalletLabels = [
                        'gopay' => 'GoPay',
                        'dana' => 'DANA',
                        'ovo' => 'OVO',
                        'shopeepay' => 'ShopeePay',
                        'linkaja' => 'LinkAja',
                        'qris' => 'QRIS',
                    ];
                    $ewallets = $gift['ewallets'] ?? [];
                @endphp

                @if (!empty($ewallets))
                    <p class="gift-section-label">E-Wallet</p>
                    <div class="ewallet-list">
                        @foreach ($ewallets as $ew)
                            @php $providerKey = $ew['provider'] ?? 'qris'; @endphp
                            <div class="ewallet-card">
                                <div class="ewallet-icon {{ $providerKey }}">
                                    {{ strtoupper($providerKey === 'shopeepay' ? 'SPAY' : substr($providerKey, 0, 4)) }}
                                </div>
                                <div class="bank-meta">
                                    <p class="bname">{{ $ewalletLabels[$providerKey] ?? strtoupper($providerKey) }}
                                    </p>
                                    <p class="bno">{{ $ew['account_no'] ?? '—' }}</p>
                                    <p class="bholder">{{ $ew['account_holder'] ?? '' }}</p>
                                </div>
                                @if (!empty($ew['account_no']))
                                    <button type="button" class="copy-btn" data-copy="{{ $ew['account_no'] }}">Salin</button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    @endif

    {{-- ════════════════════ Section 7: Ucapan & Doa Restu ════════════════════ --}}
    @php $bg = $sectionBg('wishes'); @endphp
    <section class="section anim {{ $bg ? 'has-bg' : '' }}"
        style="--overlay: 0.55; {{ $bg ? "background-image:url('$bg');" : '' }}">
        @if ($bg)
            <div class="overlay"></div>
        @endif
        <div class="section-inner">
            <p class="subtitle">Send Your Wishes</p>
            <h2>{{ $wishesC['heading'] ?? 'Ucapan & Doa Restu' }}</h2>

            <form class="wish-form"
                onsubmit="event.preventDefault(); alert('Preview mode — kirim ucapan akan aktif setelah dipublikasikan.');">
                <label>Nama</label>
                <input type="text" placeholder="Nama Anda" required>
                <label>Konfirmasi Kehadiran</label>
                <select required>
                    <option value="hadir">Hadir</option>
                    <option value="tidak_hadir">Tidak Hadir</option>
                    <option value="ragu">Masih Ragu</option>
                </select>
                <label>Ucapan</label>
                <textarea placeholder="Tulis ucapan & doa untuk kedua mempelai..." required></textarea>
                <button class="send-btn" type="submit">Kirim Ucapan</button>
            </form>

            @php
                $wishesForJs = $wishes
                    ->map(function ($w) {
                        return [
                            'name' => $w['name'],
                            'message' => $w['message'],
                            'attending' => $w['attending'],
                            'when' => $w['created_at']->diffForHumans(),
                        ];
                    })
                    ->values();
            @endphp
            <div class="wish-list" id="wish-list"
                data-wishes='{!! $wishesForJs->toJson(JSON_HEX_APOS | JSON_HEX_QUOT) !!}'>
                {{-- Filled by JS for pagination support --}}
            </div>
            <div class="pagination" id="wish-pagination"></div>

            <p class="wishes-footer">{{ $wishesC['footer'] ?? 'Hope to see you soon, Stay safe and healthy!' }}</p>
        </div>
    </section>

    {{-- ════════════════════ Live Streaming ════════════════════ --}}
    @if (in_array('live_stream', $addonKeys))
        @php
            $ls = $content['live_stream'] ?? [];
            $lsHeading = $ls['heading'] ?? 'Saksikan Secara Online';
            $lsDesc = $ls['description'] ?? '';
            $lsProvider = $ls['provider'] ?? 'youtube';
            $lsUrl = $ls['url'] ?? '';
            $lsVideoId = $ls['video_id'] ?? null;
            $lsDate = $ls['start_date'] ?? '';
            $lsTime = $ls['start_time'] ?? '';
            $lsLabel = $ls['button_label'] ?? 'Tonton Live Streaming';
            $lsBg = $sectionBg('live_stream');

            // Extract YouTube ID from URL if not already stored
            if ($lsProvider === 'youtube' && $lsUrl && !$lsVideoId) {
                if (
                    preg_match('/(?:youtu\.be\/|[?&]v=|\/(?:embed|shorts|v|live)\/)([A-Za-z0-9_-]{11})/i', $lsUrl, $m)
                ) {
                    $lsVideoId = $m[1];
                }
            }

            $lsEmbedUrl = $lsVideoId ? "https://www.youtube.com/embed/{$lsVideoId}?autoplay=0&rel=0" : null;

            // Format schedule label
            $lsSchedule = null;
            if ($lsDate) {
                try {
                    $lsSchedule = \Carbon\Carbon::parse($lsDate)->locale('id')->isoFormat('dddd, D MMMM YYYY');
                    if ($lsTime) {
                        $lsSchedule .= ' · ' . substr($lsTime, 0, 5) . ' WIB';
                    }
                } catch (\Throwable $e) {
                    $lsSchedule = $lsDate . ($lsTime ? ' · ' . $lsTime : '');
                }
            }
        @endphp
        @php $lsBgStyle = $lsBg ? "background-image:url('$lsBg');" : ''; @endphp
        <section class="section anim {{ $lsBg ? 'has-bg' : '' }}" style="--overlay: 0.55; {{ $lsBgStyle }}">
            @if ($lsBg)
                <div class="overlay"></div>
            @endif
            <div class="section-inner">
                <p class="subtitle">Live Streaming</p>
                <h2>{{ $lsHeading }}</h2>

                <div class="livestream-card">
                    @if ($lsEmbedUrl)
                        <div class="livestream-embed">
                            <iframe src="{{ $lsEmbedUrl }}"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen loading="lazy">
                            </iframe>
                        </div>
                    @endif

                    <div class="livestream-body">
                        @if ($lsSchedule)
                            <div class="livestream-schedule">
                                📅 {{ $lsSchedule }}
                            </div>
                        @endif

                        @if ($lsDesc)
                            <p class="ls-desc">{{ $lsDesc }}</p>
                        @endif

                        @if ($lsUrl && $lsProvider === 'custom')
                            <a href="{{ $lsUrl }}" target="_blank" rel="noopener" class="ls-btn">
                                📺 {{ $lsLabel }}
                            </a>
                        @elseif ($lsUrl && $lsProvider === 'youtube')
                            <a href="{{ $lsUrl }}" target="_blank" rel="noopener" class="ls-btn">
                                ▶ Buka di YouTube
                            </a>
                        @else
                            <p class="text-sm opacity-50 italic">Link live streaming belum ditambahkan.</p>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- ════════════════════ Section 8: Closing ════════════════════ --}}
    @php $bgClose = $sectionBg('closing'); @endphp
    <footer class="closing anim" @if ($bgClose)
        style="background-image:linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('{{ $bgClose }}'); background-size:cover; background-position:center;"
    @endif>
        <h2>{{ $closing['heading'] ?? 'Terima Kasih' }}</h2>
        <p class="thanks">{{ $closing['thank_you'] ?? 'Atas Kehadiran & Doa Restunya' }}</p>
        <p class="signature">— {{ $invitation->coupleNames() }} —</p>

        @if (!empty($closing['watermark']))
            <p class="watermark">{{ $closing['watermark'] }}</p>
        @endif
    </footer>

    {{-- ════════════════════ Music player addon ════════════════════ --}}
    @if (in_array('music_player', $addonKeys) && !empty($music))
        @php
            $musicVideoId = $music['video_id'] ?? null;
            $musicTitle = $music['title'] ?: 'Lagu Latar';
            $musicArtist = $music['artist'] ?: '';
            $musicAutoplay = !empty($music['autoplay']);
            $musicLoop = !empty($music['loop']);
            $musicStartAt = (int) ($music['start_at'] ?? 0);
        @endphp

        @if ($musicVideoId)
            {{-- Hidden YouTube iframe host (positioned off-screen but still rendered
            so the IFrame API can control it). --}}
            <div id="yt-player-host"
                style="position:fixed; left:-9999px; top:-9999px; width:1px; height:1px; pointer-events:none;"
                data-video-id="{{ $musicVideoId }}" data-autoplay="{{ $musicAutoplay ? '1' : '0' }}"
                data-loop="{{ $musicLoop ? '1' : '0' }}" data-start-at="{{ $musicStartAt }}"></div>
        @endif

        <div class="music-bar" id="music-bar">
            <button class="music-btn" id="music-btn" type="button" aria-label="Putar/Jeda musik">▶</button>
            <div class="music-info">
                <p class="song">{{ $musicTitle }}</p>
                <p class="artist">{{ $musicArtist ?: 'Music Player' }}</p>
            </div>
        </div>
    @endif

    {{-- ════════════════════ Lightbox + Toast ════════════════════ --}}
    <div class="lightbox" id="lightbox">
        <button class="close-btn" id="lightbox-close">×</button>
        <img id="lightbox-img" src="" alt="">
    </div>
    <div class="toast" id="toast">Disalin!</div>

    {{-- ════════════════════ Animation pack scripts ════════════════════ --}}
    @if ($animationKey === 'standard' || $animationKey === 'premium')
        <script src="https://cdn.jsdelivr.net/npm/gsap@3/dist/gsap.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/gsap@3/dist/ScrollTrigger.min.js"></script>
    @endif

    <script>
        (function () {
            // ─── Cover gate ───
            var coverBtn = document.getElementById('open-invitation-btn');
            if (coverBtn) {
                coverBtn.addEventListener('click', function () {
                    document.body.classList.add('invitation-opened');
                    window.scrollTo({
                        top: 0,
                        behavior: 'instant'
                    });
                    // Start the music if autoplay is enabled and the player has booted.
                    // The click here counts as a user gesture so autoplay is allowed
                    // by the browser even without muting.
                    if (window.__musicAutoplayOnOpen) window.__musicAutoplayOnOpen();
                });
            }

            // ─── Countdown ───
            var cd = document.querySelector('.countdown');
            if (cd) {
                var target = new Date(cd.dataset.target).getTime();

                function tick() {
                    var diff = Math.max(0, target - Date.now());
                    var d = Math.floor(diff / 86400000),
                        h = Math.floor((diff % 86400000) / 3600000),
                        m = Math.floor((diff % 3600000) / 60000),
                        s = Math.floor((diff % 60000) / 1000);
                    cd.querySelector('[data-cd="d"]').textContent = String(d).padStart(2, '0');
                    cd.querySelector('[data-cd="h"]').textContent = String(h).padStart(2, '0');
                    cd.querySelector('[data-cd="m"]').textContent = String(m).padStart(2, '0');
                    cd.querySelector('[data-cd="s"]').textContent = String(s).padStart(2, '0');
                }
                tick();
                setInterval(tick, 1000);
            }

            // ─── Gallery lightbox ───
            var lb = document.getElementById('lightbox');
            var lbImg = document.getElementById('lightbox-img');
            document.querySelectorAll('#gallery-grid .cell[data-src]').forEach(function (cell) {
                cell.addEventListener('click', function () {
                    lbImg.src = cell.dataset.src;
                    lb.classList.add('open');
                });
            });
            if (lb) {
                lb.addEventListener('click', function (e) {
                    if (e.target === lb || e.target.id === 'lightbox-close') {
                        lb.classList.remove('open');
                        lbImg.src = '';
                    }
                });
                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape') {
                        lb.classList.remove('open');
                        lbImg.src = '';
                    }
                });
            }

            // ─── Copy bank ───
            var toast = document.getElementById('toast');

            function showToast(msg) {
                if (!toast) return;
                toast.textContent = msg;
                toast.classList.add('show');
                clearTimeout(showToast._t);
                showToast._t = setTimeout(function () {
                    toast.classList.remove('show');
                }, 1800);
            }
            document.querySelectorAll('.copy-btn').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var val = btn.dataset.copy || '';
                    var done = function () {
                        btn.classList.add('copied');
                        var prev = btn.textContent;
                        btn.textContent = '✓ Tersalin';
                        showToast('Nomor rekening disalin');
                        setTimeout(function () {
                            btn.classList.remove('copied');
                            btn.textContent = prev;
                        }, 1800);
                    };
                    if (navigator.clipboard && window.isSecureContext) {
                        navigator.clipboard.writeText(val).then(done).catch(function () {
                            var ta = document.createElement('textarea');
                            ta.value = val;
                            document.body.appendChild(ta);
                            ta.select();
                            document.execCommand('copy');
                            ta.remove();
                            done();
                        });
                    } else {
                        var ta = document.createElement('textarea');
                        ta.value = val;
                        document.body.appendChild(ta);
                        ta.select();
                        document.execCommand('copy');
                        ta.remove();
                        done();
                    }
                });
            });

            // ─── Wishes pagination ───
            var listEl = document.getElementById('wish-list');
            var pagEl = document.getElementById('wish-pagination');
            if (listEl && pagEl) {
                var wishes = [];
                try {
                    wishes = JSON.parse(listEl.dataset.wishes || '[]');
                } catch (e) {
                    wishes = [];
                }
                var perPage = 10,
                    page = 1;
                var pageCount = Math.max(1, Math.ceil(wishes.length / perPage));

                function escapeHtml(s) {
                    return String(s).replace(/[&<>"']/g, function (c) {
                        return ({
                            '&': '&amp;',
                            '<': '&lt;',
                            '>': '&gt;',
                            '"': '&quot;',
                            "'": '&#39;'
                        })[c];
                    });
                }

                function render() {
                    var start = (page - 1) * perPage;
                    var items = wishes.slice(start, start + perPage);
                    if (!items.length) {
                        listEl.innerHTML =
                            '<div class="wish-item" style="text-align:center;opacity:0.6"><p class="msg">Belum ada ucapan. Jadilah yang pertama!</p></div>';
                    } else {
                        listEl.innerHTML = items.map(function (w) {
                            var attClass = w.attending === 'tidak_hadir' ? 'att no' : 'att';
                            var attText = w.attending === 'tidak_hadir' ? 'Tidak Hadir' : (w.attending ===
                                'ragu' ? 'Masih Ragu' : 'Hadir');
                            return '' +
                                '<div class="wish-item">' +
                                '<div class="wish-item-head">' +
                                '<span class="who">' + escapeHtml(w.name) + '<span class="' + attClass + '">' +
                                attText + '</span></span>' +
                                '<span class="meta">' + escapeHtml(w.when || '') + '</span>' +
                                '</div>' +
                                '<p class="msg">' + escapeHtml(w.message) + '</p>' +
                                '</div>';
                        }).join('');
                    }
                    // Pagination buttons
                    var html = '<button data-act="prev" ' + (page === 1 ? 'disabled' : '') + '>‹</button>';
                    for (var i = 1; i <= pageCount; i++) {
                        html += '<button data-page="' + i + '" class="' + (i === page ? 'active' : '') + '">' + i +
                            '</button>';
                    }
                    html += '<button data-act="next" ' + (page === pageCount ? 'disabled' : '') + '>›</button>';
                    pagEl.innerHTML = html;
                    pagEl.querySelectorAll('button').forEach(function (b) {
                        b.addEventListener('click', function () {
                            if (b.dataset.act === 'prev' && page > 1) {
                                page--;
                                render();
                            } else if (b.dataset.act === 'next' && page < pageCount) {
                                page++;
                                render();
                            } else if (b.dataset.page) {
                                page = parseInt(b.dataset.page, 10);
                                render();
                            }
                        });
                    });
                }
                render();
            }

            // ─── Music player (YouTube IFrame API) ───
            var ytHost = document.getElementById('yt-player-host');
            var musicBtn = document.getElementById('music-btn');
            if (ytHost && musicBtn) {
                var videoId = ytHost.dataset.videoId;
                var wantAutoplay = ytHost.dataset.autoplay === '1';
                var wantLoop = ytHost.dataset.loop === '1';
                var startAt = parseInt(ytHost.dataset.startAt || '0', 10) || 0;
                var ytPlayer = null;
                var ytReady = false;
                var pendingPlay = false;

                // Load the IFrame API once, then construct a player.
                if (!window.YT || !window.YT.Player) {
                    var tag = document.createElement('script');
                    tag.src = 'https://www.youtube.com/iframe_api';
                    document.head.appendChild(tag);
                }

                function createPlayer() {
                    ytPlayer = new YT.Player('yt-player-host', {
                        videoId: videoId,
                        width: '1',
                        height: '1',
                        playerVars: {
                            controls: 0,
                            disablekb: 1,
                            modestbranding: 1,
                            rel: 0,
                            playsinline: 1,
                            fs: 0,
                            iv_load_policy: 3,
                            start: startAt,
                            // Loop a single video requires playlist=videoId.
                            loop: wantLoop ? 1 : 0,
                            playlist: wantLoop ? videoId : undefined,
                        },
                        events: {
                            onReady: function () {
                                ytReady = true;
                                if (pendingPlay) {
                                    ytPlayer.playVideo();
                                    pendingPlay = false;
                                }
                            },
                            onStateChange: function (e) {
                                if (e.data === YT.PlayerState.PLAYING) musicBtn.textContent = '⏸';
                                else if (e.data === YT.PlayerState.PAUSED || e.data === YT.PlayerState
                                    .ENDED) musicBtn.textContent = '▶';
                            },
                        },
                    });
                }

                window.onYouTubeIframeAPIReady = createPlayer;
                // If the API already loaded (e.g. cached), bootstrap immediately.
                if (window.YT && window.YT.Player) createPlayer();

                musicBtn.addEventListener('click', function () {
                    if (!ytReady || !ytPlayer) {
                        pendingPlay = true;
                        return;
                    }
                    var state = ytPlayer.getPlayerState();
                    if (state === YT.PlayerState.PLAYING) ytPlayer.pauseVideo();
                    else ytPlayer.playVideo();
                });

                window.__musicAutoplayOnOpen = function () {
                    if (!wantAutoplay) return;
                    if (ytReady && ytPlayer) ytPlayer.playVideo();
                    else pendingPlay = true;
                };
            }

            // ─── Animation pack ───
            var animKey = '{{ $animationKey }}';
            document.body.classList.add('anim-' + animKey);
            if (animKey === 'standard' || animKey === 'premium') {
                window.addEventListener('load', function () {
                    if (typeof gsap === 'undefined') return;
                    gsap.registerPlugin(ScrollTrigger);
                    gsap.utils.toArray('.anim').forEach(function (el) {
                        gsap.to(el, {
                            opacity: 1,
                            y: 0,
                            duration: animKey === 'premium' ? 1.1 : 0.8,
                            ease: animKey === 'premium' ? 'power3.out' : 'power2.out',
                            scrollTrigger: {
                                trigger: el,
                                start: 'top 88%',
                                toggleActions: 'play none none none'
                            },
                        });
                    });

                    @if ($animationKey === 'premium')
                        // Premium: floating petal particles
                        var canvas = document.createElement('canvas');
                        canvas.id = 'petals-canvas';
                        document.body.appendChild(canvas);
                        var ctx = canvas.getContext('2d');

                        function resize() {
                            canvas.width = innerWidth;
                            canvas.height = innerHeight;
                        }
                        resize();
                        window.addEventListener('resize', resize);
                        var pc = getComputedStyle(document.documentElement).getPropertyValue('--color-primary')
                            .trim() || '#c8756a';
                        var petals = Array.from({
                            length: 26
                        }, function () {
                            return {
                                x: Math.random() * innerWidth,
                                y: Math.random() * innerHeight - innerHeight,
                                r: 4 + Math.random() * 6,
                                speed: 0.6 + Math.random() * 1.1,
                                drift: (Math.random() - 0.5) * 0.8,
                                angle: Math.random() * Math.PI * 2,
                                spin: (Math.random() - 0.5) * 0.04,
                                opacity: 0.5 + Math.random() * 0.5,
                            };
                        });

                        function draw() {
                            ctx.clearRect(0, 0, canvas.width, canvas.height);
                            petals.forEach(function (p) {
                                p.y += p.speed;
                                p.x += p.drift;
                                p.angle += p.spin;
                                if (p.y > canvas.height + 20) {
                                    p.y = -20;
                                    p.x = Math.random() * canvas.width;
                                }
                                ctx.save();
                                ctx.translate(p.x, p.y);
                                ctx.rotate(p.angle);
                                ctx.globalAlpha = p.opacity;
                                ctx.fillStyle = pc;
                                ctx.beginPath();
                                ctx.ellipse(0, 0, p.r, p.r * 1.8, 0, 0, Math.PI * 2);
                                ctx.fill();
                                ctx.restore();
                            });
                            requestAnimationFrame(draw);
                        }
                        draw();
                    @endif
                });
            }
        })();
    </script>

    @if ($animationKey === AnimationPack::KEY_FREE || $animationKey === AnimationPack::KEY_STANDARD || $animationKey === AnimationPack::KEY_PREMIUM)
    <script>
        (function () {
            var observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.12 });

            function observeAll() {
                document.querySelectorAll('.anim').forEach(function (el) {
                    observer.observe(el);
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', observeAll);
            } else {
                observeAll();
            }
        })();
    </script>
    @endif

    <script>
        function loadGoogleFont(family) {
            if (!family) return;
            var id = 'gf-' + family.replace(/\s+/g, '-');
            if (document.getElementById(id)) return;
            var link = document.createElement('link');
            link.id = id;
            link.rel = 'stylesheet';
            link.href = 'https://fonts.googleapis.com/css2?family=' + encodeURIComponent(family) +
                ':wght@400;600;700&display=swap';
            document.head.appendChild(link);
        }

        window.addEventListener('message', function (event) {
            if (!event.data) return;
            var root = document.documentElement;

            if (event.data.type === 'preview:colors') {
                var c = event.data.colors || {};
                if (c.primary) root.style.setProperty('--color-primary', c.primary);
                if (c.secondary) root.style.setProperty('--color-secondary', c.secondary);
                if (c.accent) root.style.setProperty('--color-accent', c.accent);
                if (c.text) root.style.setProperty('--color-text', c.text);
            }

            if (event.data.type === 'preview:couplePhotos') {
                var cards = document.querySelectorAll('.couple-card');
                cards.forEach(function (card, i) {
                    var url = i === 0 ? event.data.groomPhoto : event.data.bridePhoto;
                    var photoEl = card.querySelector('.couple-photo');
                    var emoji = card.querySelector('.couple-photo-emoji');
                    if (!photoEl) return;
                    if (url) {
                        photoEl.style.backgroundImage = "url('" + url + "')";
                        if (emoji) emoji.style.display = 'none';
                    } else {
                        photoEl.style.backgroundImage = '';
                        if (emoji) emoji.style.display = '';
                    }
                });
            }

            if (event.data.type === 'preview:typography') {
                var t = event.data.typography || {};
                if (t.heading) {
                    loadGoogleFont(t.heading);
                    root.style.setProperty('--font-heading', "'" + t.heading + "', serif");
                }
                if (t.body) {
                    loadGoogleFont(t.body);
                    root.style.setProperty('--font-body', "'" + t.body + "', sans-serif");
                }
            }
        });
    </script>

</body>

</html>