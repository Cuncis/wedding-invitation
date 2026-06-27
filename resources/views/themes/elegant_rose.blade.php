@php
    $content    = $config?->content ?? [];
    $eventAkad  = $content['event']['akad'] ?? [];
    $eventResep = $content['event']['resepsi'] ?? [];
@endphp

<style>
.er-cover {
    min-height: 100vh;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    text-align: center; padding: 3rem 2rem;
    background: var(--secondary);
    position: relative; overflow: hidden;
}
.er-cover::before {
    content: ''; position: absolute; inset: 0;
    background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23c8756a' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}
.er-cover h1 { font-size: clamp(2.5rem, 7vw, 5rem); color: var(--primary); line-height: 1.15; position: relative; }
.er-cover .amp { font-size: clamp(1.5rem, 4vw, 3rem); color: var(--accent); margin: 0 0.5rem; }
.er-divider { width: 80px; height: 2px; background: var(--primary); margin: 1.5rem auto; opacity: 0.4; }
.er-section { max-width: 640px; margin: 0 auto; padding: 4rem 2rem; text-align: center; }
.er-section h2 { font-size: 2rem; color: var(--primary); margin-bottom: 1.5rem; }
.er-card { background: white; border-radius: 0.5rem; padding: 2rem; margin: 1rem 0; border: 1px solid rgba(200,117,106,0.12); }
.er-card h3 { font-family: var(--font-h); font-size: 1.5rem; color: var(--primary); margin-bottom: 0.75rem; }
.er-card p { color: var(--text); line-height: 1.7; font-size: 0.95rem; }
.er-event { background: var(--primary); color: white; border-radius: 0.5rem; padding: 2rem; margin: 1rem 0; }
.er-event h3 { font-size: 1.4rem; margin-bottom: 0.5rem; }
.er-event .date { font-size: 1.1rem; margin: 0.5rem 0; opacity: 0.9; }
</style>

<section class="er-cover">
    @if (! empty($content['cover']['opening_text']))
        <p style="font-style: italic; color: var(--accent); margin-bottom: 1rem; position: relative;">
            {{ $content['cover']['opening_text'] }}
        </p>
    @endif
    <h1>
        {{ $invitation->groom_name }}
        <span class="amp">&</span>
        {{ $invitation->bride_name }}
    </h1>
    <div class="er-divider"></div>
    <p style="color: var(--accent); letter-spacing: 0.2em; text-transform: uppercase; font-size: 0.85rem; position: relative;">
        {{ $content['cover']['tagline'] ?? 'Save the Date' }}
    </p>
    @if ($invitation->event_date)
        <p style="margin-top: 1.5rem; font-size: 1.2rem; color: var(--accent); position: relative;">
            {{ $invitation->event_date->isoFormat('dddd, D MMMM Y') }}
        </p>
    @endif
</section>

@if (! empty($content['couple']['groom_fullname']) || ! empty($content['couple']['bride_fullname']))
<section class="er-section">
    <h2>Mempelai</h2>
    @if (! empty($content['couple']['groom_fullname']))
        <div class="er-card">
            <h3>{{ $content['couple']['groom_fullname'] }}</h3>
            @if (! empty($content['couple']['groom_parents']))
                <p>Putra dari {{ $content['couple']['groom_parents'] }}</p>
            @endif
        </div>
    @endif
    @if (! empty($content['couple']['bride_fullname']))
        <div class="er-card">
            <h3>{{ $content['couple']['bride_fullname'] }}</h3>
            @if (! empty($content['couple']['bride_parents']))
                <p>Putri dari {{ $content['couple']['bride_parents'] }}</p>
            @endif
        </div>
    @endif
</section>
@endif

@if (! empty($eventAkad['date']) || ! empty($eventResep['date']))
<section class="er-section" style="background: white;">
    <h2>Acara</h2>
    @if (! empty($eventAkad['date']))
        <div class="er-event">
            <h3>Akad Nikah</h3>
            <p class="date">{{ $eventAkad['date'] }} · {{ $eventAkad['time'] ?? '' }}</p>
            <p>{{ $eventAkad['venue'] ?? '' }}</p>
            <p style="opacity: 0.8; font-size: 0.9rem;">{{ $eventAkad['address'] ?? '' }}</p>
        </div>
    @endif
    @if (! empty($eventResep['date']))
        <div class="er-event" style="background: var(--accent);">
            <h3>Resepsi</h3>
            <p class="date">{{ $eventResep['date'] }} · {{ $eventResep['time'] ?? '' }}</p>
            <p>{{ $eventResep['venue'] ?? '' }}</p>
            <p style="opacity: 0.8; font-size: 0.9rem;">{{ $eventResep['address'] ?? '' }}</p>
        </div>
    @endif
</section>
@endif

<section class="er-section">
    <p style="font-style: italic; color: var(--accent); font-size: 1rem; max-width: 480px; margin: 0 auto;">
        {{ $content['closing']['thank_you'] ?? 'Merupakan suatu kehormatan apabila Bapak/Ibu/Saudara/i berkenan hadir.' }}
    </p>
    <div class="er-divider"></div>
    <p style="font-family: var(--font-h); font-size: 1.3rem; color: var(--primary);">
        {{ $invitation->coupleNames() }}
    </p>
</section>
