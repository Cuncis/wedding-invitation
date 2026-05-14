@php
    $content    = $config?->content ?? [];
    $eventAkad  = $content['event']['akad'] ?? [];
    $eventResep = $content['event']['resepsi'] ?? [];
@endphp

<style>
.fg-hero {
    min-height: 100vh; display: flex; flex-direction: column;
    align-items: center; justify-content: center; text-align: center; padding: 3rem 2rem;
    background: linear-gradient(180deg, #f0fdf4 0%, #dcfce7 40%, #f0fdf4 100%);
}
.fg-hero h1 { font-family: var(--font-h); font-size: clamp(2.5rem, 7vw, 5rem); color: #14532d; }
.fg-hero .amp { font-family: var(--font-h); font-size: clamp(1.5rem, 4vw, 3rem); color: #16a34a; }
.fg-leaf { display: inline-block; font-size: 2.5rem; margin: 1rem 0; }
.fg-section { max-width: 640px; margin: 0 auto; padding: 4rem 2rem; text-align: center; }
.fg-section h2 { font-family: var(--font-h); font-size: 2rem; color: #14532d; margin-bottom: 1.5rem; }
.fg-card { background: white; border-radius: 1rem; padding: 2rem; margin: 1rem 0; border: 1px solid #bbf7d0; box-shadow: 0 4px 12px rgba(21,128,61,0.06); }
.fg-card h3 { font-family: var(--font-h); font-size: 1.4rem; color: #166534; margin-bottom: 0.5rem; }
.fg-card p { color: #374151; font-size: 0.9rem; line-height: 1.7; }
.fg-event { background: #14532d; color: white; border-radius: 1rem; padding: 2rem; margin: 1rem 0; }
.fg-event h3 { font-size: 1.3rem; margin-bottom: 0.5rem; }
.fg-event p { opacity: 0.85; font-size: 0.9rem; line-height: 1.6; }
</style>

<section class="fg-hero">
    <div class="fg-leaf">🌿</div>
    @if (! empty($content['cover']['opening_text']))
        <p style="font-style: italic; color: #16a34a; margin-bottom: 1rem;">{{ $content['cover']['opening_text'] }}</p>
    @endif
    <h1>
        {{ $invitation->groom_name }}
        <span class="amp">&</span>
        {{ $invitation->bride_name }}
    </h1>
    <p style="color: #166534; letter-spacing: 0.2em; text-transform: uppercase; font-size: 0.8rem; margin-top: 1rem;">
        {{ $content['cover']['tagline'] ?? 'Save the Date' }}
    </p>
    @if ($invitation->event_date)
        <p style="margin-top: 1.5rem; font-size: 1.15rem; color: #15803d;">
            {{ $invitation->event_date->isoFormat('dddd, D MMMM Y') }}
        </p>
    @endif
    <div class="fg-leaf" style="margin-top: 1rem; transform: rotate(180deg);">🌿</div>
</section>

@if (! empty($content['couple']['groom_fullname']) || ! empty($content['couple']['bride_fullname']))
<section class="fg-section">
    <h2>Mempelai</h2>
    @if (! empty($content['couple']['groom_fullname']))
        <div class="fg-card">
            <h3>{{ $content['couple']['groom_fullname'] }}</h3>
            @if (! empty($content['couple']['groom_parents']))
                <p>Putra dari {{ $content['couple']['groom_parents'] }}</p>
            @endif
        </div>
    @endif
    @if (! empty($content['couple']['bride_fullname']))
        <div class="fg-card">
            <h3>{{ $content['couple']['bride_fullname'] }}</h3>
            @if (! empty($content['couple']['bride_parents']))
                <p>Putri dari {{ $content['couple']['bride_parents'] }}</p>
            @endif
        </div>
    @endif
</section>
@endif

@if (! empty($eventAkad['date']) || ! empty($eventResep['date']))
<section class="fg-section" style="background: #f0fdf4;">
    <h2>Rangkaian Acara</h2>
    @if (! empty($eventAkad['date']))
        <div class="fg-event">
            <h3>🌿 Akad Nikah</h3>
            <p>{{ $eventAkad['date'] }} · {{ $eventAkad['time'] ?? '' }}</p>
            <p>{{ $eventAkad['venue'] ?? '' }}</p>
            <p>{{ $eventAkad['address'] ?? '' }}</p>
        </div>
    @endif
    @if (! empty($eventResep['date']))
        <div class="fg-event" style="background: #166534;">
            <h3>🌸 Resepsi</h3>
            <p>{{ $eventResep['date'] }} · {{ $eventResep['time'] ?? '' }}</p>
            <p>{{ $eventResep['venue'] ?? '' }}</p>
            <p>{{ $eventResep['address'] ?? '' }}</p>
        </div>
    @endif
</section>
@endif

<section class="fg-section">
    <div class="fg-leaf">🌸</div>
    <p style="color: #374151; line-height: 1.8; max-width: 480px; margin: 0 auto; font-style: italic;">
        {{ $content['closing']['thank_you'] ?? 'Merupakan suatu kehormatan apabila Bapak/Ibu/Saudara/i berkenan hadir.' }}
    </p>
    <p style="font-family: var(--font-h); font-size: 1.5rem; color: #14532d; margin-top: 1.5rem;">
        {{ $invitation->coupleNames() }}
    </p>
</section>
