@php
    $config = $invitation->config;
    $theme = $config?->theme;
    $colors = $config?->colors ?? [];
    $typography = $config?->typography ?? [];
    $content = $config?->content ?? [];
    $addonKeys = $addonKeys ?? [];
    $animationKey = $animationKey ?? 'free';

    $primary = $colors['primary'] ?? '#c8756a';
    $secondary = $colors['secondary'] ?? '#f5e6e0';
    $accent = $colors['accent'] ?? '#8b4a42';
    $text = $colors['text'] ?? '#3d2820';

    $headingFont = $typography['heading'] ?? 'Playfair Display';
    $bodyFont = $typography['body'] ?? 'Lato';
    $eventDate = $invitation->event_date ?? null;
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
        href="https://fonts.googleapis.com/css2?family={{ urlencode($headingFont) }}:wght@400;700&family={{ urlencode($bodyFont) }}:wght@400;700&display=swap"
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

        body {
            font-family: var(--font-body);
            color: var(--color-text);
            background: var(--color-secondary);
            min-height: 100vh;
        }

        h1,
        h2,
        h3 {
            font-family: var(--font-heading);
            font-weight: 700;
        }

        .cover {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 2rem;
            background: linear-gradient(180deg, var(--color-secondary), #fff);
        }

        .cover .opening {
            font-style: italic;
            color: var(--color-accent);
            margin-bottom: 1rem;
        }

        .cover h1 {
            font-size: clamp(2.5rem, 6vw, 4.5rem);
            color: var(--color-primary);
            margin: 1rem 0;
        }

        .cover .tagline {
            font-size: 1.125rem;
            color: var(--color-accent);
            letter-spacing: 0.2em;
            text-transform: uppercase;
        }

        .section {
            max-width: 600px;
            margin: 0 auto;
            padding: 4rem 2rem;
            text-align: center;
        }

        .section h2 {
            color: var(--color-primary);
            font-size: 2rem;
            margin-bottom: 2rem;
        }

        .couple-card {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            margin-bottom: 1rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .couple-card h3 {
            color: var(--color-primary);
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .couple-card p {
            color: var(--color-text);
            opacity: 0.7;
        }

        .event-card {
            background: var(--color-primary);
            color: white;
            border-radius: 1rem;
            padding: 2rem;
            margin: 1rem 0;
        }

        .event-card h3 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .event-card .date {
            font-size: 1.125rem;
            margin: 0.5rem 0;
        }

        .footer {
            background: var(--color-accent);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .addon-section {
            border-top: 1px solid rgba(0, 0, 0, 0.07);
        }

        .addon-section h2 {
            color: var(--color-primary);
            font-size: 2rem;
            margin-bottom: 2rem;
        }

        /* Countdown */
        .countdown-grid {
            display: flex;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .countdown-box {
            background: var(--color-primary);
            color: #fff;
            border-radius: 1rem;
            padding: 1rem 1.5rem;
            min-width: 80px;
            text-align: center;
        }

        .countdown-box .count {
            display: block;
            font-size: 2.5rem;
            font-weight: 700;
            font-family: var(--font-heading);
        }

        .countdown-box small {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            opacity: 0.85;
        }

        /* Love Story */
        .timeline {
            position: relative;
            padding-left: 2rem;
            text-align: left;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 0.45rem;
            top: 0;
            bottom: 0;
            width: 2px;
            background: var(--color-primary);
            opacity: 0.3;
        }

        .tl-item {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .tl-dot {
            position: absolute;
            left: -2rem;
            top: 0.35rem;
            width: 0.9rem;
            height: 0.9rem;
            border-radius: 50%;
            background: var(--color-primary);
            border: 2px solid white;
            box-shadow: 0 0 0 2px var(--color-primary);
        }

        .tl-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1rem 1.25rem;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        }

        .tl-card h4 {
            color: var(--color-primary);
            margin-bottom: 0.25rem;
            font-size: 1rem;
        }

        .tl-card p {
            color: var(--color-text);
            opacity: 0.7;
            font-size: 0.875rem;
        }

        /* Gallery */
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.5rem;
        }

        .gallery-cell {
            aspect-ratio: 1;
            border-radius: 0.5rem;
            background: linear-gradient(135deg, var(--color-secondary), var(--color-primary));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            opacity: 0.8;
        }

        /* RSVP */
        .rsvp-form {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            text-align: left;
        }

        .rsvp-form label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--color-accent);
            margin-bottom: 0.25rem;
            margin-top: 1rem;
        }

        .rsvp-form input,
        .rsvp-form select {
            width: 100%;
            padding: 0.6rem 0.9rem;
            border: 1.5px solid #e2e8f0;
            border-radius: 0.5rem;
            font-size: 0.9rem;
            color: var(--color-text);
        }

        .rsvp-btn {
            margin-top: 1.25rem;
            width: 100%;
            padding: 0.75rem;
            background: var(--color-primary);
            color: white;
            border: none;
            border-radius: 0.75rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
        }

        /* Digital Gift */
        .gift-card {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            margin-bottom: 1rem;
            text-align: left;
        }

        .gift-card .bank {
            font-size: 0.75rem;
            color: var(--color-accent);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .gift-card .account {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--color-text);
            margin: 0.25rem 0;
            font-family: var(--font-heading);
        }

        .gift-card .holder {
            font-size: 0.875rem;
            color: var(--color-text);
            opacity: 0.7;
        }

        /* Maps */
        .map-placeholder {
            background: linear-gradient(135deg, #e2e8f0, #cbd5e1);
            border-radius: 1rem;
            height: 220px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            color: #64748b;
        }

        .map-placeholder .pin {
            font-size: 2.5rem;
        }

        .map-placeholder p {
            font-size: 0.875rem;
            font-weight: 600;
        }

        .map-btn {
            display: inline-block;
            margin-top: 1rem;
            background: var(--color-primary);
            color: white;
            padding: 0.6rem 1.5rem;
            border-radius: 999px;
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
        }

        /* Live Stream */
        .stream-placeholder {
            background: #0f172a;
            border-radius: 1rem;
            padding: 3rem 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.75rem;
            color: white;
        }

        .stream-placeholder .icon {
            font-size: 3rem;
        }

        .stream-placeholder p {
            font-size: 0.875rem;
            opacity: 0.7;
        }

        /* Music Player */
        .music-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 100;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
            border-top: 1px solid rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem 1.5rem;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.08);
        }

        .music-btn {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            background: var(--color-primary);
            color: white;
            border: none;
            font-size: 1.1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .music-info {
            flex: 1;
            min-width: 0;
        }

        .music-info .song {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--color-text);
            truncate: ellipsis;
        }

        .music-info .artist {
            font-size: 0.7rem;
            color: var(--color-text);
            opacity: 0.6;
        }

        .music-wave {
            display: flex;
            align-items: flex-end;
            gap: 2px;
            height: 1.5rem;
        }

        .music-wave span {
            width: 3px;
            background: var(--color-primary);
            border-radius: 2px;
            animation: wave 0.8s ease-in-out infinite;
        }

        .music-wave span:nth-child(2) {
            animation-delay: 0.1s;
        }

        .music-wave span:nth-child(3) {
            animation-delay: 0.2s;
        }

        .music-wave span:nth-child(4) {
            animation-delay: 0.3s;
        }

        .music-wave span:nth-child(5) {
            animation-delay: 0.15s;
        }

        {{ '@' }}
        keyframes wave {

            0%,
            100% {
                height: 4px;
            }

            50% {
                height: 20px;
            }
        }

        /* ── Animation pack ── */
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

        body.anim-free .anim:nth-child(5) {
            animation-delay: 0.4s;
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
    </style>
</head>

<body>

    <section class="cover anim">
        @if (!empty($content['cover']['opening_text']))
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

    <section class="section anim">
        <h2>Mempelai</h2>
        @if (!empty($content['couple']['groom_fullname']))
            <div class="couple-card">
                <h3>{{ $content['couple']['groom_fullname'] }}</h3>
                <p>{{ $content['couple']['groom_parents'] ?? '' }}</p>
            </div>
        @endif
        @if (!empty($content['couple']['bride_fullname']))
            <div class="couple-card">
                <h3>{{ $content['couple']['bride_fullname'] }}</h3>
                <p>{{ $content['couple']['bride_parents'] ?? '' }}</p>
            </div>
        @endif
    </section>

    @if (!empty($content['event']))
        <section class="section anim">
            <h2>Acara</h2>
            @foreach (['akad' => 'Akad Nikah', 'resepsi' => 'Resepsi'] as $key => $label)
                @if (!empty($content['event'][$key]))
                    <div class="event-card">
                        <h3>{{ $label }}</h3>
                        <p class="date">{{ $content['event'][$key]['date'] ?? '' }} · {{ $content['event'][$key]['time'] ?? '' }}
                        </p>
                        <p>{{ $content['event'][$key]['venue'] ?? '' }}</p>
                        <p style="opacity: 0.85;">{{ $content['event'][$key]['address'] ?? '' }}</p>
                    </div>
                @endif
            @endforeach
        </section>
    @endif

    <footer class="footer anim">
        <p>{{ $content['closing']['thank_you'] ?? 'Merupakan suatu kehormatan apabila Bapak/Ibu/Saudara/i berkenan hadir.' }}
        </p>
        <p style="margin-top: 1rem; opacity: 0.7;">— {{ $invitation->coupleNames() }} —</p>
    </footer>

    @if (in_array('countdown', $addonKeys))
        <section class="section addon-section anim">
            <h2>⏰ Hitung Mundur</h2>
            <div class="countdown-grid">
                <div class="countdown-box"><span class="count" id="c-days">00</span><small>Hari</small></div>
                <div class="countdown-box"><span class="count" id="c-hours">00</span><small>Jam</small></div>
                <div class="countdown-box"><span class="count" id="c-minutes">00</span><small>Menit</small></div>
                <div class="countdown-box"><span class="count" id="c-seconds">00</span><small>Detik</small></div>
            </div>
            @if ($eventDate)
                <script>
                    (function () {
                        var target = new Date("{{ $eventDate }}").getTime();
                        function tick() {
                            var now = Date.now(), diff = target - now;
                            if (diff < 0) diff = 0;
                            var d = Math.floor(diff / 86400000), h = Math.floor((diff % 86400000) / 3600000),
                                m = Math.floor((diff % 3600000) / 60000), s = Math.floor((diff % 60000) / 1000);
                            document.getElementById('c-days').textContent = String(d).padStart(2, '0');
                            document.getElementById('c-hours').textContent = String(h).padStart(2, '0');
                            document.getElementById('c-minutes').textContent = String(m).padStart(2, '0');
                            document.getElementById('c-seconds').textContent = String(s).padStart(2, '0');
                        }
                        tick(); setInterval(tick, 1000);
                    })();
                </script>
            @endif
        </section>
    @endif

    @if (in_array('love_story', $addonKeys))
        <section class="section addon-section anim">
            <h2>💕 Love Story</h2>
            <div class="timeline">
                <div class="tl-item">
                    <div class="tl-dot"></div>
                    <div class="tl-card">
                        <h4>Pertemuan Pertama</h4>
                        <p>Kisah kami berawal dari sebuah pertemuan yang tak terduga...</p>
                    </div>
                </div>
                <div class="tl-item">
                    <div class="tl-dot"></div>
                    <div class="tl-card">
                        <h4>Jatuh Cinta</h4>
                        <p>Hari demi hari, perasaan ini semakin dalam dan nyata...</p>
                    </div>
                </div>
                <div class="tl-item">
                    <div class="tl-dot"></div>
                    <div class="tl-card">
                        <h4>Lamaran</h4>
                        <p>Dengan penuh keyakinan, kami memutuskan untuk bersatu selamanya.</p>
                    </div>
                </div>
            </div>
        </section>
    @endif

    @if (in_array('photo_gallery', $addonKeys))
        <section class="section addon-section anim">
            <h2>📸 Galeri Foto</h2>
            <div class="gallery-grid">
                @for ($i = 0; $i < 6; $i++)
                    <div class="gallery-cell">🌸</div>
                @endfor
            </div>
        </section>
    @endif

    @if (in_array('maps', $addonKeys))
        <section class="section addon-section anim">
            <h2>📍 Lokasi Acara</h2>
            <div class="map-placeholder">
                <span class="pin">📍</span>
                <p>{{ $invitation->event_venue ?: 'Venue Pernikahan' }}</p>
            </div>
            <a href="#" class="map-btn">Buka Google Maps</a>
        </section>
    @endif

    @if (in_array('rsvp_form', $addonKeys))
        <section class="section addon-section anim">
            <h2>✉️ Konfirmasi Kehadiran</h2>
            <div class="rsvp-form">
                <label>Nama Lengkap</label>
                <input type="text" placeholder="Nama Anda" disabled>
                <label>Konfirmasi Kehadiran</label>
                <select disabled>
                    <option>Hadir</option>
                    <option>Tidak Hadir</option>
                    <option>Masih Ragu</option>
                </select>
                <label>Jumlah Tamu</label>
                <input type="number" placeholder="1" disabled>
                <button class="rsvp-btn">Kirim Konfirmasi</button>
            </div>
        </section>
    @endif

    @if (in_array('digital_gift', $addonKeys))
        <section class="section addon-section anim">
            <h2>🎁 Amplop Digital</h2>
            <div class="gift-card">
                <p class="bank">Bank BCA</p>
                <p class="account">1234 5678 90</p>
                <p class="holder">a.n. Nama Mempelai</p>
            </div>
            <div class="gift-card">
                <p class="bank">GoPay / OVO</p>
                <p class="account">0812 3456 7890</p>
                <p class="holder">a.n. Nama Mempelai</p>
            </div>
        </section>
    @endif

    @if (in_array('live_stream', $addonKeys))
        <section class="section addon-section anim">
            <h2>📺 Live Streaming</h2>
            <div class="stream-placeholder">
                <span class="icon">▶</span>
                <p>Link live streaming akan tersedia saat acara berlangsung</p>
            </div>
        </section>
    @endif

    @if (in_array('music_player', $addonKeys))
        <div class="music-bar">
            <button class="music-btn" id="music-btn">▶</button>
            <div class="music-info">
                <p class="song">Lagu Latar Belakang</p>
                <p class="artist">Music Player Aktif</p>
            </div>
            <div class="music-wave" id="music-wave" style="display:none">
                <span style="height:6px"></span><span style="height:12px"></span>
                <span style="height:18px"></span><span style="height:10px"></span>
                <span style="height:14px"></span>
            </div>
        </div>
        <script>
            document.getElementById('music-btn').addEventListener('click', function () {
                var isPlaying = this.textContent === '⏸';
                this.textContent = isPlaying ? '▶' : '⏸';
                document.getElementById('music-wave').style.display = isPlaying ? 'none' : 'flex';
            });
        </script>
    @endif

    @if (!empty($addonKeys))
        <div style="height:{{ in_array('music_player', $addonKeys) ? '5rem' : '0' }}"></div>
    @endif

    {{-- ── Animation pack scripts ── --}}
    @if ($animationKey === 'standard' || $animationKey === 'premium')
        <script src="https://cdn.jsdelivr.net/npm/gsap@3/dist/gsap.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/gsap@3/dist/ScrollTrigger.min.js"></script>
    @endif

    <script>
        (function () {
            var key = '{{ $animationKey }}';
            document.body.classList.add('anim-' + key);

            if (key === 'free') return; // CSS handles it

            // standard & premium: GSAP scroll reveal
            window.addEventListener('load', function () {
                if (typeof gsap === 'undefined') return;
                gsap.registerPlugin(ScrollTrigger);

                gsap.utils.toArray('.anim').forEach(function (el, i) {
                    gsap.to(el, {
                        opacity: 1,
                        y: 0,
                        duration: key === 'premium' ? 1.1 : 0.8,
                        ease: key === 'premium' ? 'power3.out' : 'power2.out',
                        delay: i === 0 ? 0.1 : 0,
                        scrollTrigger: {
                            trigger: el,
                            start: 'top 88%',
                            toggleActions: 'play none none none',
                        },
                    });
                });

                @if ($animationKey === 'premium')
                    // Premium: floating petal particles
                    var canvas = document.createElement('canvas');
                    canvas.id = 'petals-canvas';
                    document.body.appendChild(canvas);
                    var ctx = canvas.getContext('2d');
                    function resize() { canvas.width = innerWidth; canvas.height = innerHeight; }
                    resize();
                    window.addEventListener('resize', resize);

                    var primaryColor = getComputedStyle(document.documentElement)
                        .getPropertyValue('--color-primary').trim() || '#c8756a';

                    var petals = Array.from({ length: 28 }, function () {
                        return {
                            x: Math.random() * innerWidth,
                            y: Math.random() * innerHeight - innerHeight,
                            r: 4 + Math.random() * 7,
                            speed: 0.6 + Math.random() * 1.2,
                            drift: (Math.random() - 0.5) * 0.8,
                            angle: Math.random() * Math.PI * 2,
                            spin: (Math.random() - 0.5) * 0.04,
                            opacity: 0.5 + Math.random() * 0.5,
                        };
                    });

                    function drawPetal(p) {
                        ctx.save();
                        ctx.translate(p.x, p.y);
                        ctx.rotate(p.angle);
                        ctx.globalAlpha = p.opacity;
                        ctx.fillStyle = primaryColor;
                        ctx.beginPath();
                        ctx.ellipse(0, 0, p.r, p.r * 1.8, 0, 0, Math.PI * 2);
                        ctx.fill();
                        ctx.restore();
                    }

                    function animate() {
                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                        petals.forEach(function (p) {
                            p.y += p.speed;
                            p.x += p.drift;
                            p.angle += p.spin;
                            if (p.y > canvas.height + 20) {
                                p.y = -20;
                                p.x = Math.random() * canvas.width;
                            }
                            drawPetal(p);
                        });
                        requestAnimationFrame(animate);
                    }
                    animate();
                @endif
            });
        })();
    </script>

</body>

</html>