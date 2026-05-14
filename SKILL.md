---
name: undangan-saas
description: Use this skill when building, extending, or debugging any part of the Undangan Digital SaaS platform. Covers the complete 12-week build: VPS infrastructure, Laravel 13 backend, pricing engine, Midtrans payment integration, invitation builder, RSVP system, WhatsApp notifications, and production deployment on Contabo Singapore. Trigger on any question about this specific project — architecture decisions, code patterns, service classes, database schema, deployment, or week-by-week task execution.
---

# Undangan Digital SaaS — Master Build Reference

## Project Identity

**Product**: Self-service digital wedding invitation platform (Indonesian market)  
**Stack**: Laravel 13 · PHP 8.4 · Vue 3 · MySQL 8 · Redis · Nginx · Contabo VPS Singapore  
**Payment**: Midtrans (primary) — Xendit/DOKU via gateway factory pattern  
**Notifications**: WhatsApp via Fonnte API · Email via Mailgun/Resend  
**Assets**: Cloudflare R2 (S3-compatible) · Cloudflare CDN (free tier)  
**Queue**: Redis + Laravel Horizon · WebSockets: Laravel Reverb  
**Admin**: Filament v3  
**Timeline**: 12 weeks · ~4 hours/day

## Business Model

Customers (couples) self-build their invitation in a drag-style builder:
1. Pick a **theme** (base price Rp 99K–119K)
2. Toggle **addons** (music player, gallery, maps, countdown, RSVP, digital gift, love story, livestream) — each has individual price
3. Select **animation pack** (free / standard Rp 29K / premium Rp 59K)
4. **Checkout** → price locked as snapshot → **Midtrans Snap payment**
5. On payment confirmation via webhook → **invitation auto-published** at unique slug URL
6. **RSVP** responses collected from guests → couple sees dashboard

No manual intervention required after customer pays.

---

## Environment Constants

```
VPS:        Contabo Cloud VPS 10, Asia (Singapore), Ubuntu 22.04 LTS
Web root:   /var/www/undangan/public
App dir:    /var/www/undangan
Deploy user: deploy (never root in production)
PHP:        8.4-fpm via ondrej/php PPA
DB:         MySQL 8 · database: undangan_db · user: undangan_user
Cache:      Redis on 127.0.0.1:6379
Queue:      redis driver · monitored by Horizon
Sockets:    Laravel Reverb on port 8080
Supervisor: manages horizon + reverb processes
Crontab:    * * * * * cd /var/www/undangan && php artisan schedule:run >> /dev/null 2>&1
Firewall:   UFW — allow 22, 80, 443, 8080 only
SSL:        Certbot (Let's Encrypt) + Cloudflare Full (strict) mode
```

---

## Directory Structure (Critical Paths)

```
app/
  Console/Commands/
    ExpireOrders.php          # orders:expire — runs hourly
  Events/
    PaymentSucceeded.php      # dispatched after payment confirmed
  Http/Controllers/
    Auth/
      LoginController.php
      RegisterController.php
    Api/
      PricingController.php   # GET /api/pricing
    OrderController.php       # store() + checkout()
    WebhookController.php     # POST /webhooks/midtrans
    DashboardController.php
    InvitationController.php  # public show() — /{slug}
    BuilderController.php     # builder UI + config API
    RsvpController.php
  Jobs/
    ProcessPaymentWebhook.php # ShouldQueue — async payment processing
  Listeners/
    InvitationPublisher.php   # listens PaymentSucceeded → publishes slug
  Models/
    User.php
    Invitation.php
    InvitationConfig.php      # JSON config blob (separate table)
    Theme.php
    Addon.php
    AnimationPack.php
    Order.php
    Payment.php
    RsvpResponse.php
  Policies/
    InvitationPolicy.php      # checkout, update, view abilities
  Providers/
    AppServiceProvider.php    # singletons + Midtrans boot config + event listeners
  Services/
    PricingService.php        # calculate(), calculateFromInvitation(), format()
    OrderService.php          # createOrder(), markAsPaid(), expireOldOrders()
    PaymentService.php        # gateway factory + initiate()
    Payment/
      PaymentGateway.php      # interface
      PaymentResult.php       # readonly DTO
      MidtransGateway.php     # implements PaymentGateway
  Filament/Resources/
    ThemeResource.php
    AddonResource.php
    AnimationPackResource.php
    UserResource.php
    OrderResource.php
resources/
  views/
    auth/login.blade.php
    auth/register.blade.php
    dashboard/index.blade.php
    orders/checkout.blade.php
    builder/edit.blade.php    # Vue 3 builder SPA entry point
    invitations/show.blade.php # public invitation — theme-specific partial
    themes/                   # one Blade partial per theme slug
      elegant-rose.blade.php
      modern-minimalist.blade.php
      floral-garden.blade.php
    rsvp/index.blade.php      # couple's RSVP response dashboard
  js/
    builder/                  # Vue 3 components
      App.vue
      store/index.js          # Pinia store
      components/
        ThemePicker.vue
        AddonPanel.vue
        AnimationPicker.vue
        SectionEditor.vue
        PriceBreakdown.vue
```

---

## Database Schema

### Core Rules
- All prices stored as **integers (Rupiah, no decimals)** — never floats
- All JSON columns cast to `array` in Eloquent models
- `invitation_configs` is a **separate table** from `invitations` — never load config in list queries
- `orders.snapshot` stores a **complete frozen copy** of the pricing breakdown at checkout time — never recalculate from snapshot
- `payments.external_id` is indexed — queried on every webhook

### Tables

```sql
users
  id, name, email, phone, whatsapp, role (admin|customer),
  email_verified_at, password, last_login_at, remember_token, timestamps

invitations
  id, user_id (FK), slug (unique, nullable — null until paid),
  groom_name, bride_name, event_date, event_venue,
  status (draft|pending|active|expired),
  published_at (nullable), expires_at (nullable), view_count,
  timestamps
  INDEX: [user_id, status], [slug]

invitation_configs
  id, invitation_id (FK, unique), theme_id (FK nullable),
  animation_pack_id (FK nullable),
  sections (json), colors (json), typography (json),
  content (json), addon_ids (json), music (json), maps (json),
  timestamps

themes
  id, name, slug (unique), description, preview_image,
  base_price (int), default_colors (json), default_fonts (json),
  is_active, sort_order, timestamps

addons
  id, name, key (unique), description, icon, price (int),
  category (media|interactive|social|utility),
  is_active, sort_order, timestamps

animation_packs
  id, name, key (unique — free|standard|premium),
  description, features (json), price (int), is_active, timestamps

orders
  id, order_number (unique — UND-YYYY-NNNNN), user_id (FK),
  invitation_id (FK), theme_price, addon_price, animation_price,
  total_amount (int), status (pending|paid|failed|refunded),
  snapshot (json — frozen pricing at checkout), expires_at,
  timestamps
  INDEX: [user_id, status]

payments
  id, order_id (FK), gateway (midtrans|xendit|doku),
  external_id (nullable), payment_method (nullable),
  status (pending|settlement|expire|cancel|deny|refund),
  amount (int), raw_payload (json), paid_at (nullable), timestamps
  INDEX: [external_id]

rsvp_responses
  id, invitation_id (FK cascade), guest_name, guest_phone,
  attendance (hadir|tidak_hadir|mungkin), pax (tinyint default 1),
  message (text nullable), timestamps
  INDEX: [invitation_id]
```

---

## Eloquent Models — Key Patterns

### InvitationConfig casts (ALL json fields)
```php
protected function casts(): array {
    return [
        'sections' => 'array', 'colors' => 'array',
        'typography' => 'array', 'content' => 'array',
        'addon_ids' => 'array', 'music' => 'array', 'maps' => 'array',
    ];
}
```

### Invitation scopes and helpers
```php
public function scopePublished($query) {
    return $query->whereNotNull('published_at')
                 ->where('status', 'active')
                 ->where('expires_at', '>', now());
}
public function isPublished(): bool {
    return $this->published_at !== null
        && $this->status === 'active'
        && $this->expires_at > now();
}
public function coupleNames(): string {
    return $this->groom_name . ' & ' . $this->bride_name;
}
```

### Order::generateNumber()
```php
public static function generateNumber(): string {
    $count = static::count() + 1;
    return 'UND-' . date('Y') . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
}
```

### User — FilamentUser interface
```php
// User must implement FilamentUser for Filament admin access
public function canAccessPanel(Panel $panel): bool {
    return $this->role === 'admin';
}
```

---

## Service Classes — Rules and Signatures

### PricingService

```php
// Single source of truth — ALL price calculations go through here only
// Never calculate prices in controllers, views, or jobs

calculate(?int $theme_id, array $addon_ids = [], ?int $animation_pack_id = null): array
// Returns: ['theme'=>[...], 'addons'=>[...], 'animation'=>[...],
//           'theme_price'=>int, 'addon_price'=>int, 'animation_price'=>int,
//           'total'=>int, 'total_formatted'=>'Rp 99.000']

calculateFromInvitation(Invitation $invitation): array
// Reads from invitation->config, calls calculate() internally

static format(int $amount): string
// Returns 'Rp 99.000' format — dot thousands separator, no decimals
```

**Critical constraints:**
- `is_active = false` addons must be filtered out — `Addon::whereIn('id', $ids)->where('is_active', true)`
- Default to free animation pack when `$animation_pack_id` is null
- All arithmetic on integers — never cast to float
- Registered as singleton in AppServiceProvider

### OrderService

```php
createOrder(Invitation $invitation): Order
// Throws \Exception if: already paid, or total === 0 (no theme selected)
// Returns existing pending order if not yet expired (prevents duplicate orders)
// Wraps in DB::transaction()
// Sets expires_at = now()->addHours(24)
// Stores full pricing breakdown in snapshot column

markAsPaid(Order $order): void
// DB::transaction() — updates order.status='paid', invitation.status='active'

expireOldOrders(): int
// Called by orders:expire command hourly
// Updates pending orders past expires_at → status='failed'
// Resets invitation.status back to 'draft'
// Returns count of expired orders
```

### PaymentService + Gateway Pattern

```php
// Factory — resolves gateway by name
gateway(string $name = 'midtrans'): PaymentGateway

// Creates payment, saves Payment record, returns PaymentResult DTO
initiate(Order $order, string $gatewayName = 'midtrans'): PaymentResult
```

**PaymentGateway interface methods:**
```php
createPayment(Order $order): PaymentResult    // generate token/link
verifyWebhook(Request $request): bool         // signature check
resolveStatus(Request $request): string       // 'paid'|'failed'|'pending'
resolveExternalId(Request $request): string   // order_id from gateway
```

**MidtransGateway signature verification:**
```php
$signature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
return hash_equals($signature, $request->input('signature_key'));
```

**Midtrans status mapping:**
```
settlement              → 'paid'
capture + accept        → 'paid'  (credit card)
cancel|deny|expire      → 'failed'
pending                 → 'pending'
```

### InvitationPublisher (Event Listener)

```php
// Listens to: PaymentSucceeded event
// Idempotent — checks published_at !== null before doing anything
// Generates unique slug: Str::slug(groom . '-' . bride), suffixes -2 -3 if taken
// Sets: slug, status='active', published_at=now(), expires_at=now()->addYear()
// Logs: logger()->info("Invitation published: {$slug}", [...])
// Week 7: sends WhatsApp notification via Fonnte after logging
```

---

## Route Map

```php
// routes/web.php

// Public
GET  /                              → landing/marketing page
GET  /{slug}                        → InvitationController@show (published only)
POST /rsvp/{slug}                   → RsvpController@store (no auth)

// Guest only
GET  /login                         → LoginController@show
POST /login                         → LoginController@store
GET  /register                      → RegisterController@show
POST /register                      → RegisterController@store

// Authenticated
POST /logout                        → LoginController@destroy
GET  /dashboard                     → DashboardController@index
POST /dashboard/create              → DashboardController@create (creates draft)
GET  /builder/{invitation}/edit     → BuilderController@edit
PUT  /builder/{invitation}/config   → BuilderController@updateConfig (API)
GET  /builder/{invitation}/preview  → BuilderController@preview (iframe src)
POST /invitations/{invitation}/checkout → OrderController@store
GET  /orders/{order}/checkout       → OrderController@checkout
GET  /rsvp/{invitation}             → RsvpController@index (couple's view)

// Webhooks (outside auth, CSRF excluded)
POST /webhooks/midtrans             → WebhookController@midtrans

// Admin (Filament)
/admin/*                            → Filament panel (admin role only)

// routes/api.php
GET  /api/pricing                   → PricingController@calculate (throttle:60,1)
```

---

## Invitation Config JSON Schema

```json
{
  "theme_id": 1,
  "addon_ids": [1, 3, 5],
  "animation_pack_id": 2,
  "sections": ["cover", "couple", "event", "love_story", "gallery", "maps", "countdown", "rsvp"],
  "colors": {
    "primary": "#c8756a",
    "secondary": "#f5e6e0",
    "accent": "#8b4a42",
    "text": "#3d2820"
  },
  "typography": {
    "heading": "Playfair Display",
    "body": "Lato"
  },
  "content": {
    "cover": { "opening_text": "Bismillahirrahmanirrahim", "tagline": "Kami Menikah" },
    "couple": {
      "groom_fullname": "Budi Santoso, S.T.",
      "groom_parents": "Putra dari Bpk. Ahmad & Ibu Siti",
      "bride_fullname": "Sari Dewi, S.Pd.",
      "bride_parents": "Putri dari Bpk. Hasan & Ibu Rina"
    },
    "event": {
      "akad": { "date": "2025-06-14", "time": "08:00", "venue": "Masjid Al-Ikhlas", "address": "Jl. Merdeka No. 1" },
      "resepsi": { "date": "2025-06-14", "time": "11:00", "venue": "Gedung Serbaguna", "address": "Jl. Sudirman No. 45" }
    },
    "love_story": [
      { "year": "2020", "title": "Pertama Bertemu", "story": "..." }
    ],
    "closing": { "thank_you": "Merupakan suatu kehormatan..." }
  },
  "music": { "url": "https://...", "autoplay": true, "title": "Perfect - Ed Sheeran" },
  "maps": { "lat": -6.2, "lng": 106.8, "label": "Gedung Serbaguna", "embed_url": "https://..." }
}
```

**Config rules:**
- Config is saved by the builder on every change — debounced 1500ms
- Config is loaded by builder on page load — never recalculated
- The Blade invitation template reads ONLY from saved config — never from live DB joins
- Addon components render conditionally based on `in_array($addonKey, $config['addon_ids'])`

---

## Blade Invitation Template Pattern

```php
// routes/web.php
Route::get('/{slug}', [InvitationController::class, 'show'])->name('invitation.show');

// InvitationController@show
public function show(string $slug)
{
    $invitation = Invitation::published()
        ->where('slug', $slug)
        ->with('config.theme')
        ->firstOrFail();

    // Increment view count (non-blocking)
    $invitation->increment('view_count');

    // Load config as array
    $config = $invitation->config;
    $theme  = $config->theme;

    // Render theme-specific Blade partial
    return view('invitations.show', compact('invitation', 'config', 'theme'));
}

// resources/views/invitations/show.blade.php
// Includes theme partial based on theme slug:
@include('themes.' . $theme->slug, ['config' => $config, 'invitation' => $invitation])
```

**Each theme Blade partial (`resources/views/themes/elegant-rose.blade.php`):**
- Is a complete self-contained HTML page (own `<head>`, Tailwind CDN or compiled CSS)
- Reads all content from `$config->content` array
- Conditionally renders addon sections: `@if(in_array('music_player', $config->addon_ids ?? []))`
- Loads animation JS based on `$config->animation_pack_id`
- Includes RSVP form if `in_array('rsvp_form', ...)`
- Sets OG meta tags using `$invitation->coupleNames()` and `$invitation->event_date`

---

## Builder UI Architecture (Vue 3)

```
resources/js/builder/
  App.vue              — root, 3-column layout
  store/index.js       — Pinia store (config state, dirty tracking, pricing)
  components/
    ThemePicker.vue    — theme grid, click selects, shows base price
    AddonPanel.vue     — toggle list, each shows price, running total
    AnimationPicker.vue — 3 tier cards with feature list + price
    SectionEditor.vue  — per-section content fields (names, dates, text)
    ColorPicker.vue    — color inputs for primary/secondary/accent/text
    TypographyPicker.vue — font selector (Google Fonts)
    PriceBreakdown.vue — live itemized total, calls GET /api/pricing
    PreviewFrame.vue   — iframe pointing to /builder/{id}/preview
```

**Pinia store core state:**
```js
{
  config: {}, // mirrors invitation_configs JSON schema
  isDirty: false,
  isSaving: false,
  pricing: null,   // result from /api/pricing
}
```

**Auto-save pattern (debounced):**
```js
watch(config, debounce(async () => {
  store.isSaving = true
  await axios.put(`/builder/${invitationId}/config`, store.config)
  store.isDirty = false
  store.isSaving = false
  // Also refresh iframe src to trigger preview reload
  previewFrame.src = previewFrame.src
}, 1500), { deep: true })
```

**Live pricing update:**
```js
watch([() => config.theme_id, () => config.addon_ids, () => config.animation_pack_id],
  debounce(async () => {
    const { data } = await axios.get('/api/pricing', { params: {
      theme_id: config.theme_id,
      addon_ids: config.addon_ids,
      animation_pack_id: config.animation_pack_id
    }})
    store.pricing = data
  }, 500)
)
```

---

## Payment Flow — Complete Sequence

```
1. Customer finishes builder
2. POST /invitations/{id}/checkout
   → OrderService::createOrder() — validates, locks price, creates Order (status: pending)
   → redirect to GET /orders/{id}/checkout

3. GET /orders/{id}/checkout
   → PaymentService::initiate() — calls MidtransGateway::createPayment()
   → Midtrans API returns snap_token
   → Blade renders checkout page with snap.pay(token) button

4. Customer clicks "Bayar Sekarang"
   → Midtrans Snap popup opens (all payment methods)
   → Customer completes payment

5. Midtrans POSTs to POST /webhooks/midtrans
   → WebhookController::midtrans() (must complete in < 5 seconds)
   → verifyWebhook() — reject if signature invalid
   → Save raw_payload to payments table
   → Dispatch ProcessPaymentWebhook job
   → Return 200 immediately

6. ProcessPaymentWebhook job (async via Redis queue)
   → Idempotency check: skip if payment.status already 'settlement'
   → resolveStatus() → 'paid'|'failed'|'pending'
   → Update payment.status, payment_method, paid_at
   → If paid: OrderService::markAsPaid() → order='paid', invitation='active'
   → Dispatch PaymentSucceeded event

7. InvitationPublisher listener (triggered by PaymentSucceeded)
   → Idempotency check: skip if invitation.published_at !== null
   → Generate unique slug
   → Update invitation: slug, status='active', published_at, expires_at=+1year
   → Send WhatsApp via Fonnte (Week 7)
   → Send email receipt (Week 7)

8. Customer sees invitation live at /{slug}
```

---

## Webhook Controller — Iron Rules

1. **Verify signature first** — return 401 and stop if invalid
2. **Dispatch job, never process inline** — controller must return 200 in < 5s
3. **Store raw_payload** before dispatching — audit trail and job has all data it needs
4. **Return 200 even if payment record not found** — prevents Midtrans retry storm
5. **CSRF excluded** — add `'webhooks/*'` to CSRF except list in `bootstrap/app.php`
6. **Route outside auth middleware** — webhook has no session

---

## Filament v3 Admin Resources

```
ThemeResource     — full CRUD: name, slug (auto from name), description,
                    base_price (Rp prefix), preview_image upload, is_active toggle, sort_order
AddonResource     — full CRUD: name, key, description, icon, price (Rp), category select, is_active
AnimationPackResource — full CRUD: name, key, features (json textarea), price (Rp), is_active
UserResource      — list only: name, email, whatsapp, role, last_login_at, invitation count
OrderResource     — list + view: order_number, customer name, couple names,
                    total (Rp formatted), BadgeColumn status (warning/success/danger), created_at
                    Filter by status
```

**Price formatting in Filament:**
```php
TextColumn::make('base_price')
    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
    ->sortable()
```

---

## Seeded Data (DatabaseSeeder order)

```
1. AnimationPackSeeder  — free (Rp 0), standard (Rp 29K), premium (Rp 59K)
2. ThemeSeeder          — elegant-rose (Rp 99K), modern-minimalist (Rp 119K), floral-garden (Rp 109K)
3. AddonSeeder          — 8 addons with keys: music_player, photo_gallery, maps, countdown,
                          rsvp_form (Rp 0), digital_gift, love_story, live_stream
4. AdminUserSeeder      — admin@undanganmu.com, role='admin', email_verified_at=now()
```

**All seeders use `firstOrCreate` by unique key** — safe to re-run on production.

---

## Scheduled Jobs

```php
// routes/console.php
Schedule::command('orders:expire')->hourly();
// Additional schedules added in Week 12:
Schedule::command('invitations:expire-check')->daily();  // sets status=expired for past expires_at
```

---

## Supervisor Configuration

```ini
; /etc/supervisor/conf.d/undangan.conf

[program:undangan-horizon]
command=php /var/www/undangan/artisan horizon
autostart=true
autorestart=true
user=deploy
stopwaitsecs=3600
stdout_logfile=/var/www/undangan/storage/logs/horizon.log

[program:undangan-reverb]
command=php /var/www/undangan/artisan reverb:start --host=0.0.0.0 --port=8080
autostart=true
autorestart=true
user=deploy
stdout_logfile=/var/www/undangan/storage/logs/reverb.log
```

---

## Nginx Virtual Host

```nginx
server {
    listen 80;
    server_name app.yourdomain.com;
    root /var/www/undangan/public;
    index index.php;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    gzip on;
    gzip_types text/plain text/css application/json application/javascript;

    location / { try_files $uri $uri/ /index.php?$query_string; }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # WebSocket proxy for Reverb
    location /app {
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "Upgrade";
        proxy_set_header Host $host;
        proxy_pass http://127.0.0.1:8080;
    }

    location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff2)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }

    location ~ /\.(?!well-known).* { deny all; }
}
```

---

## .env Production Values

```env
APP_NAME="Undangan Digital"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://app.yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=undangan_db
DB_USERNAME=undangan_user
DB_PASSWORD=<strong-password>

CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
BROADCAST_CONNECTION=reverb

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

REVERB_APP_ID=<id>
REVERB_APP_KEY=<key>
REVERB_APP_SECRET=<secret>
REVERB_HOST=app.yourdomain.com
REVERB_PORT=8080
REVERB_SCHEME=https

MIDTRANS_SERVER_KEY=Mid-server-...        # production key after Week 9 approval
MIDTRANS_CLIENT_KEY=Mid-client-...
MIDTRANS_IS_PRODUCTION=true

MAIL_MAILER=mailgun   # or resend
MAILGUN_DOMAIN=...
MAILGUN_SECRET=...

FONNTE_TOKEN=<fonnte-api-token>

FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=<cloudflare-r2-key>
AWS_SECRET_ACCESS_KEY=<cloudflare-r2-secret>
AWS_DEFAULT_REGION=auto
AWS_BUCKET=undangan-assets
AWS_ENDPOINT=https://<account>.r2.cloudflarestorage.com
AWS_USE_PATH_STYLE_ENDPOINT=true

SENTRY_LARAVEL_DSN=https://...@sentry.io/...
```

---

## deploy.sh — Production Deploy Script

```bash
#!/bin/bash
set -e
VPS_USER="deploy"
VPS_IP="YOUR_VPS_IP"

echo "🚀 Deploying to production..."

ssh $VPS_USER@$VPS_IP << 'ENDSSH'
  set -e
  cd /var/www/undangan

  git pull origin main

  composer install --no-dev --optimize-autoloader --no-interaction

  php artisan migrate --force

  php artisan config:cache
  php artisan route:cache
  php artisan view:cache
  php artisan event:cache

  php artisan horizon:terminate
  sudo supervisorctl restart undangan-horizon
  sudo supervisorctl restart undangan-reverb

  echo "✅ Deploy complete at $(date)"
ENDSSH
```

**Requires:** deploy user has `NOPASSWD: /usr/bin/supervisorctl` in sudoers.

---

## WhatsApp Notifications (Fonnte — Week 7)

```php
// app/Services/NotificationService.php

public function sendWhatsApp(string $to, string $message): bool
{
    $response = Http::withHeaders([
        'Authorization' => config('services.fonnte.token'),
    ])->post('https://api.fonnte.com/send', [
        'target'  => $to,          // whatsapp number with country code e.g. 628123456789
        'message' => $message,
        'countryCode' => '62',     // Indonesia
    ]);

    return $response->successful();
}

// Usage in InvitationPublisher after publishing:
$message = "🎉 Undangan *{$invitation->coupleNames()}* sudah aktif!\n\n" .
           "🔗 Link undangan:\n" . url('/' . $slug) . "\n\n" .
           "Link ini aktif selama 1 tahun. Selamat!";

app(NotificationService::class)->sendWhatsApp($order->user->whatsapp, $message);
```

```env
FONNTE_TOKEN=your-fonnte-api-token
```

```php
// config/services.php — add:
'fonnte' => ['token' => env('FONNTE_TOKEN')],
```

---

## RSVP System (Week 7)

```php
// Public form — no auth, no CSRF needed on API route
// POST /rsvp/{slug}
public function store(Request $request, string $slug)
{
    $invitation = Invitation::published()->where('slug', $slug)->firstOrFail();

    $data = $request->validate([
        'guest_name'  => ['required', 'string', 'max:100'],
        'guest_phone' => ['nullable', 'string', 'max:20'],
        'attendance'  => ['required', 'in:hadir,tidak_hadir,mungkin'],
        'pax'         => ['integer', 'min:1', 'max:10'],
        'message'     => ['nullable', 'string', 'max:500'],
    ]);

    $rsvp = $invitation->rsvpResponses()->create($data);

    // Notify couple via WhatsApp (queued)
    SendRsvpNotification::dispatch($rsvp);

    return back()->with('success', 'Terima kasih atas konfirmasi kehadirannya!');
}
```

**RSVP dashboard for couple (`GET /rsvp/{invitation}`):**
- Shows total responses, hadir count, total pax
- Filterable by attendance status
- CSV export: `$responses->toArray()` → `fputcsv()`
- Protected by InvitationPolicy: only owner can view

---

## Animation System (Week 8)

```
free tier    → No JS animation. CSS transition: opacity 0.6s ease only.
standard tier → GSAP ScrollTrigger: fade up on scroll, text slide in.
premium tier  → GSAP + Lottie: envelope open on load, particle flowers, cinematic page transitions.
```

**Loading pattern in theme Blade:**
```php
@if($config->animationPack->key === 'standard')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
    <script>@include('partials.animations.standard')</script>
@elseif($config->animationPack->key === 'premium')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.12.2/lottie.min.js"></script>
    <script>@include('partials.animations.premium')</script>
@endif
```

**WhatsApp in-app browser constraints (critical for Indonesian market):**
- Audio autoplay is blocked — always show a play button as fallback
- `prefers-reduced-motion` must be respected
- Test every theme in WhatsApp's built-in browser on Android before shipping

---

## Cloudflare Caching Strategy (Week 9)

```
Public invitation pages (/{slug}):
  Cache-Control: public, max-age=3600
  Cloudflare: Cache Everything rule for /{slug}/* pattern
  Purge on: InvitationPublisher runs → call Cloudflare Cache Purge API

Builder, dashboard, admin: Cache-Control: no-store (never cache authenticated pages)

Static assets (/build/*): Cache-Control: public, immutable, max-age=31536000
```

---

## Security Checklist (Week 9)

```
UFW firewall: ports 22, 80, 443, 8080 only
Fail2Ban: SSH brute force protection
root SSH: PermitRootLogin no
Password auth: PasswordAuthentication no
CSRF: enabled on all web routes except webhooks/*
Rate limiting: throttle:60,1 on /api/* routes
File uploads: max 5MB, jpg/png/webp only, no PHP execution in storage/
Webhook: signature verified before any DB operation
Admin panel: accessible only to role=admin users
Horizon: /horizon route protected — add auth middleware
Telescope: disabled in production (APP_ENV=production auto-disables)
Sentry: error monitoring — SENTRY_LARAVEL_DSN set in production
```

---

## Test Coverage Requirements (Week 9)

```
Unit tests (tests/Unit/):
  PricingServiceTest — 7 scenarios:
    - returns zero when nothing selected
    - calculates theme price only
    - sums multiple addons correctly
    - adds premium animation price
    - defaults to free animation when none selected
    - ignores inactive addons
    - formats total as Rupiah

Feature tests (tests/Feature/):
  OrderFlowTest — 4 scenarios:
    - guest cannot checkout
    - user cannot checkout other user's invitation
    - owner can create order and see checkout page
    - checkout page shows locked price not live price

  WebhookTest — 3 scenarios:
    - rejects webhook with invalid signature (returns 401)
    - dispatches ProcessPaymentWebhook job on valid webhook
    - returns 200 even when payment record not found

  InvitationPublisherTest — 2 scenarios:
    - publishes invitation with unique slug on PaymentSucceeded
    - does not re-publish already published invitation (idempotency)

Run: php artisan test
All must pass before deploying to production.
```

---

## Monitoring & Operations (Week 12)

```
Uptime:    UptimeRobot (free) — check https://app.yourdomain.com every 5 min
           Alert to WhatsApp + email on down

Logs:      storage/logs/laravel.log — check daily first 2 weeks
           storage/logs/horizon.log — check if queue falls behind
           /var/log/nginx/undangan_error.log — check for 5xx spikes

DB backup: Daily mysqldump to Cloudflare R2
           Crontab: 0 2 * * * /var/www/undangan/scripts/backup-db.sh
           Keep 30 days, test restore monthly

Horizon:   /horizon — monitor queue throughput, failed jobs
           Failed jobs: php artisan horizon:failed → investigate before retrying

Health endpoint: GET /health → returns 200 + JSON (DB check, Redis check, queue check)
```

**Daily backup script (`/var/www/undangan/scripts/backup-db.sh`):**
```bash
#!/bin/bash
DATE=$(date +%Y%m%d-%H%M)
FILENAME="undangan_db_${DATE}.sql.gz"
mysqldump -u undangan_user -p'PASSWORD' undangan_db | gzip > /tmp/$FILENAME
aws s3 cp /tmp/$FILENAME s3://undangan-backups/$FILENAME \
  --endpoint-url https://<account>.r2.cloudflarestorage.com
rm /tmp/$FILENAME
echo "Backup $FILENAME complete"
```

---

## Week-by-Week Milestone Reference

| Week | Focus | Done When |
|------|-------|-----------|
| 1 | VPS + Laravel deploy | `https://app.domain.com` loads on HTTPS, deploy.sh works |
| 2 | DB schema + Auth + Filament | Login/register work, admin panel shows seeded themes/addons |
| 3 | PricingService + OrderService + Checkout | GET /api/pricing returns JSON, order creates in DB, 11 tests pass |
| 4 | Midtrans payment + webhook + publisher | Sandbox payment → webhook → invitation auto-published |
| 5 | Builder data layer + Blade templates | Config saves/loads, preview route renders themed Blade |
| 6 | Builder Vue 3 UI | Auto-save works, live preview refreshes, price updates in real time |
| 7 | RSVP + notifications + public page | Full flow: pay → WA notification → guests can RSVP → couple sees responses |
| 8 | Themes polish + animations + addons | 3 themes render on mobile, GSAP animations per tier, all addons functional |
| 9 | Testing + security + Cloudflare | All tests pass, Sentry live, Cloudflare caching public pages |
| 10 | Soft launch — beta clients | First real paying client, real invitation live, first revenue |
| 11 | Landing page + marketing | Landing page live, demo video posted, referral outreach started |
| 12 | Ops automation + v2 planning | Auto backup, uptime monitoring, CI/CD, runbook written |

---

## Common Errors and Fixes

**Webhook returns 500:** Check CSRF exclusion in `bootstrap/app.php` — `webhooks/*` must be in except list.

**Snap popup doesn't open:** Client key not set correctly in Blade — confirm `config('midtrans.client_key')` is not null. Check browser console for `snap is not defined`.

**Queue job not processing:** Check Supervisor is running — `sudo supervisorctl status`. Check Redis is up — `redis-cli ping`. Check Horizon — `/horizon`.

**Invitation not publishing after payment:** Check `ProcessPaymentWebhook` job didn't fail — `php artisan horizon:failed`. Common cause: `PaymentSucceeded` event not registered in `AppServiceProvider`.

**Slug collision:** `InvitationPublisher::uniqueSlug()` must loop until slug is truly unique — `while (Invitation::where('slug', $slug)->exists())`.

**Config not saving in builder:** Check `PUT /builder/{id}/config` is in auth middleware group, Sanctum SPA auth is configured (`stateful` domains set in `config/sanctum.php`).

**Midtrans item_details total mismatch:** Sum of all item prices must exactly equal `gross_amount`. Any mismatch causes Midtrans API to reject the request. The `buildItemDetails()` method must sum to `$order->total_amount`.

**WhatsApp number format:** Fonnte expects numbers with country code, no leading zero: `628123456789` not `08123456789`. Strip leading `0` and prepend `62`.

---

## AppServiceProvider — Complete boot() and register()

```php
public function register(): void
{
    $this->app->singleton(PricingService::class);
    $this->app->singleton(OrderService::class);
    $this->app->singleton(PaymentService::class);
    $this->app->singleton(NotificationService::class);
}

public function boot(): void
{
    // Midtrans global config
    \Midtrans\Config::$serverKey    = config('midtrans.server_key');
    \Midtrans\Config::$clientKey    = config('midtrans.client_key');
    \Midtrans\Config::$isProduction = config('midtrans.is_production');
    \Midtrans\Config::$isSanitized  = config('midtrans.is_sanitized');
    \Midtrans\Config::$is3ds        = config('midtrans.is_3ds');

    // Event listeners
    Event::listen(PaymentSucceeded::class, InvitationPublisher::class);
}
```

---

## Key Architectural Decisions (Never Change These)

1. **PricingService is the ONLY place prices are calculated** — never inline in controllers or jobs
2. **Order snapshot is immutable** — checkout page reads snapshot, never recalculates
3. **WebhookController is thin** — verify → save raw payload → dispatch job → return 200. Nothing else.
4. **Jobs are idempotent** — check current state before acting. Safe to retry.
5. **InvitationConfig in separate table** — list queries never load the JSON blob
6. **Prices are integers** — no floats, no decimals, no casting to float anywhere
7. **Slug is null until paid** — invitation URL is inaccessible until `published_at` is set
8. **Deploy user, never root** — all app files owned by `deploy:www-data`
9. **All seeders use firstOrCreate** — safe to run multiple times on production
10. **Tests must pass locally before every push to main**
