<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Builder — {{ $invitation->coupleNames() ?: 'Undangan Baru' }}</title>
    @vite(['resources/css/app.css', 'resources/js/builder/main.js'])
</head>
<body class="antialiased bg-slate-50">

<div
    id="builder-app"
    data-invitation-id="{{ $invitation->id }}"
    data-preview-url="{{ route('builder.preview', $invitation) }}"
    data-checkout-url="{{ route('checkout.show', $invitation) }}"
    data-config="{{ json_encode($invitation->config?->only([
        'theme_id','animation_pack_id','addon_ids','sections','colors','typography','content','music','maps',
    ]) ?? new \stdClass()) }}"
    data-invitation="{{ json_encode($invitation->only(['id','groom_name','bride_name','event_date','event_venue','status'])) }}"
    data-themes="{{ json_encode($themes) }}"
    data-addons="{{ json_encode($addons) }}"
    data-animation-packs="{{ json_encode($animationPacks) }}"
></div>

</body>
</html>
