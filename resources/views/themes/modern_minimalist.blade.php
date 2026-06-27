@php
    $content    = $config?->content ?? [];
    $eventAkad  = $content['event']['akad'] ?? [];
    $eventResep = $content['event']['resepsi'] ?? [];
@endphp

<style>
.mm-body { font-family: var(--font-b); background: #fff; }
.mm-hero {
    min-height: 100vh; display: flex; flex-direction: column;
    align-items: center; justify-content: center; text-align: center;
    padding: 3rem 2rem; background: #fff;
    border-bottom: 1px solid #e5e7eb;
}
.mm-hero .names { font-family: var(--font-h); font-size: clamp(2rem, 6vw, 4rem); color: #111; letter-spacing: -0.02em; }
.mm-hero .tagline { font-size: 0.75rem; letter-spacing: 0.35em; text-transform: uppercase; color: var(--accent); margin-top: 1rem; }
.mm-hero .line { width: 40px; height: 1px; background: var(--primary); margin: 1.5rem auto; }
.mm-row { display: flex; gap: 1rem; flex-wrap: wrap; justify-content: center; margin-top: 1.5rem; }
.mm-pill { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 999px; padding: 0.4rem 1.2rem; font-size: 0.85rem; color: #374151; }
.mm-section { max-width: 680px; margin: 0 auto; padding: 4rem 2rem; }
.mm-section h2 { font-family: var(--font-h); font-size: 0.7rem; letter-spacing: 0.3em; text-transform: uppercase; color: var(--accent); margin-bottom: 2rem; }
.mm-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
@media (max-width: 500px) { .mm-grid { grid-template-columns: 1fr; } }
.mm-card { border: 1px solid #e5e7eb; border-radius: 0.375rem; padding: 1.5rem; }
.mm-card .name { font-family: var(--font-h); font-size: 1.4rem; color: #111; margin-bottom: 0.25rem; }
.mm-card .sub { font-size: 0.85rem; color: #6b7280; }
.mm-event { padding: 1.5rem; border-left: 3px solid var(--primary); margin-bottom: 1rem; }
.mm-event .label { font-size: 0.7rem; letter-spacing: 0.2em; text-transform: uppercase; color: var(--primary); margin-bottom: 0.5rem; }
.mm-event h3 { font-family: var(--font-h); font-size: 1.3rem; color: #111; margin-bottom: 0.25rem; }
.mm-event p { font-size: 0.9rem; color: #4b5563; }
</style>

<div class="mm-body">
<section class="mm-hero">
    @if (! empty($content['cover']['opening_text']))
        <p style="font-size: 0.9rem; color: #9ca3af; margin-bottom: 1.5rem;">{{ $content['cover']['opening_text'] }}</p>
    @endif
    <h1 class="names">
        {{ $invitation->groom_name }}<br>
        <span style="font-size: 0.5em; color: var(--accent); font-weight: 400;">&amp;</span><br>
        {{ $invitation->bride_name }}
    </h1>
    <div class="line"></div>
    <p class="tagline">{{ $content['cover']['tagline'] ?? 'Save the Date' }}</p>
    @if ($invitation->event_date)
        <div class="mm-row">
            <span class="mm-pill">{{ $invitation->event_date->isoFormat('dddd') }}</span>
            <span class="mm-pill">{{ $invitation->event_date->isoFormat('D MMMM Y') }}</span>
            @if ($invitation->event_venue)
                <span class="mm-pill">{{ $invitation->event_venue }}</span>
            @endif
        </div>
    @endif
</section>

@if (! empty($content['couple']['groom_fullname']) || ! empty($content['couple']['bride_fullname']))
<section class="mm-section">
    <h2>Mempelai</h2>
    <div class="mm-grid">
        @if (! empty($content['couple']['groom_fullname']))
            <div class="mm-card">
                <div class="name">{{ $content['couple']['groom_fullname'] }}</div>
                <div class="sub">{{ $content['couple']['groom_parents'] ?? '' }}</div>
            </div>
        @endif
        @if (! empty($content['couple']['bride_fullname']))
            <div class="mm-card">
                <div class="name">{{ $content['couple']['bride_fullname'] }}</div>
                <div class="sub">{{ $content['couple']['bride_parents'] ?? '' }}</div>
            </div>
        @endif
    </div>
</section>
@endif

@if (! empty($eventAkad['date']) || ! empty($eventResep['date']))
<section class="mm-section" style="background: #f9fafb; max-width: 100%; padding: 4rem 2rem;">
    <div style="max-width: 680px; margin: 0 auto;">
        <h2 style="font-family: var(--font-h); font-size: 0.7rem; letter-spacing: 0.3em; text-transform: uppercase; color: var(--accent); margin-bottom: 2rem;">Acara</h2>
        @if (! empty($eventAkad['date']))
            <div class="mm-event">
                <div class="label">Akad Nikah</div>
                <h3>{{ $eventAkad['date'] }} · {{ $eventAkad['time'] ?? '' }}</h3>
                <p>{{ $eventAkad['venue'] ?? '' }}</p>
                <p style="color: #9ca3af; font-size: 0.85rem;">{{ $eventAkad['address'] ?? '' }}</p>
            </div>
        @endif
        @if (! empty($eventResep['date']))
            <div class="mm-event" style="border-left-color: var(--accent);">
                <div class="label" style="color: var(--accent);">Resepsi</div>
                <h3>{{ $eventResep['date'] }} · {{ $eventResep['time'] ?? '' }}</h3>
                <p>{{ $eventResep['venue'] ?? '' }}</p>
                <p style="color: #9ca3af; font-size: 0.85rem;">{{ $eventResep['address'] ?? '' }}</p>
            </div>
        @endif
    </div>
</section>
@endif

<section class="mm-section" style="text-align: center;">
    <p style="color: #6b7280; line-height: 1.8; max-width: 480px; margin: 0 auto;">
        {{ $content['closing']['thank_you'] ?? 'Merupakan suatu kehormatan apabila Bapak/Ibu/Saudara/i berkenan hadir.' }}
    </p>
    <p style="font-family: var(--font-h); font-size: 1.5rem; color: #111; margin-top: 2rem;">
        {{ $invitation->coupleNames() }}
    </p>
</section>
</div>
