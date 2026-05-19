# Laravel Brain AI Context
> Project: Laravel | Analyzed: 2026-05-19T10:24:45+07:00 | Focal: Full project summary | Budget: 6000 tokens

## Call Chain (depth ≤ 3)
invitations:expire → Invitation::where

## Complexity Hotspots
| Label | Cyclomatic | Lines |
|-------|-----------|-------|
| LoginController@store | 3 | 29 |
| VerifyEmailController@__invoke | 3 | 14 |
| HealthController@__invoke | 3 | 27 |
| WebhookController@__invoke | 3 | 17 |
| OrderService@createOrder | 3 | 42 |
| RegisterController@store | 2 | 21 |
| DashboardController@destroy | 2 | 14 |
| OrderController@checkout | 2 | 12 |
| OrderController@store | 2 | 12 |
| OrderController@storeCheckout | 2 | 12 |
| LoginController@destroy | 1 | 8 |
| LoginController@show | 1 | 4 |
| LogoutController@__invoke | 1 | 6 |
| RegisterController@show | 1 | 4 |
| BuilderController@edit | 1 | 17 |
| BuilderController@preview | 1 | 23 |
| BuilderController@updateConfig | 1 | 25 |
| DashboardController@create | 1 | 14 |
| DashboardController@index | 1 | 14 |
| InvitationConfigController@__invoke | 1 | 11 |
| InvitationController@destroy | 1 | 8 |
| InvitationController@index | 1 | 8 |
| InvitationController@show | 1 | 6 |
| InvitationController@store | 1 | 9 |
| InvitationController@update | 1 | 8 |
| InvitationPublishController@__invoke | 1 | 10 |
| InvitationShowController@__invoke | 1 | 30 |
| OrderController@destroy | 1 | 8 |
| OrderController@index | 1 | 6 |
| OrderController@show | 1 | 6 |
| OrderController@showCheckout | 1 | 8 |
| OrderController@showOrder | 1 | 8 |
| OrderController@update | 1 | 8 |
| PaymentController@create | 1 | 12 |
| PricingController@__invoke | 1 | 17 |
| PublicInvitationController@__invoke | 1 | 6 |
| RsvpController@export | 1 | 31 |
| RsvpController@index | 1 | 16 |
| RsvpController@store | 1 | 10 |
| ThemeSwitcherController@__invoke | 1 | 15 |
| MidtransGateway@resolveExternalId | 1 | 4 |
| MidtransGateway@verifyWebhook | 1 | 10 |
| PaymentService@gateway | 1 | 5 |
| PaymentService@initiate | 1 | 16 |
| PricingService@calculate | 1 | 49 |
| PricingService@calculateFromInvitation | 1 | 10 |
| invitations:expire | 1 | 10 |
| orders:expire | 1 | 8 |
| getHeaderActions | 1 | 6 |
| getHeaderActions | 1 | 6 |
| getHeaderActions | 1 | 6 |
| getHeaderActions | 1 | 6 |
| getHeaderActions | 1 | 6 |
| getHeaderActions | 1 | 6 |
| getHeaderActions | 1 | 6 |
| getHeaderActions | 1 | 6 |
| getHeaderActions | 1 | 6 |
| getHeaderActions | 1 | 6 |

## Database Operations
- eloquent query users (via LoginController@store)
- eloquent where payments (via WebhookController@__invoke)
- eloquent create users (via RegisterController@store)
- eloquent delete invitations (via DashboardController@destroy)
- eloquent view orders (via OrderController@checkout)
- eloquent isPaid orders (via OrderController@checkout)
- eloquent findOrFail invitations (via OrderController@store)
- eloquent calculateFromInvitation pricing_services (via OrderController@store)
- eloquent checkout invitations (via OrderController@storeCheckout)
- eloquent calculateFromInvitation pricing_services (via OrderController@storeCheckout)
- eloquent update invitations (via BuilderController@edit)
- eloquent loadMissing invitations (via BuilderController@edit)
- eloquent update invitations (via BuilderController@preview)
- eloquent loadMissing invitations (via BuilderController@preview)
- eloquent update invitations (via BuilderController@updateConfig)
- eloquent config invitations (via BuilderController@updateConfig)
- eloquent update invitations (via InvitationConfigController@__invoke)
- eloquent query invitation_configs (via InvitationConfigController@__invoke)
- eloquent delete invitations (via InvitationController@destroy)
- eloquent where invitations (via InvitationController@index)
- eloquent view invitations (via InvitationController@show)
- eloquent create invitations (via InvitationController@store)
- eloquent update invitations (via InvitationController@update)
- eloquent refresh invitations (via InvitationController@update)
- eloquent update invitations (via InvitationPublishController@__invoke)
- eloquent delete orders (via OrderController@destroy)
- eloquent where orders (via OrderController@index)
- eloquent view orders (via OrderController@show)
- eloquent checkout invitations (via OrderController@showCheckout)
- eloquent view orders (via OrderController@showOrder)
- eloquent load orders (via OrderController@showOrder)
- eloquent update orders (via OrderController@update)
- eloquent refresh orders (via OrderController@update)
- eloquent view orders (via PaymentController@create)
- eloquent create payments (via PaymentController@create)
- eloquent where invitations (via PublicInvitationController@__invoke)
- eloquent view invitations (via RsvpController@export)
- eloquent rsvpResponses invitations (via RsvpController@export)
- eloquent view invitations (via RsvpController@index)
- eloquent rsvpResponses invitations (via RsvpController@index)
- eloquent update invitations (via ThemeSwitcherController@__invoke)
- eloquent refresh invitations (via ThemeSwitcherController@__invoke)

## Backend Packages (composer.json)
| Package | Version | Dev |
|---------|---------|-----|
| fakerphp/faker | ^1.23 | yes |
| filament/filament | ^4.0 |  |
| laramint/laravel-brain | ^2.2 | yes |
| laravel/framework | ^13.7 |  |
| laravel/pail | ^1.2.5 | yes |
| laravel/pao | ^1.0.6 | yes |
| laravel/pint | ^1.27 | yes |
| laravel/sanctum | ^4.0 |  |
| laravel/tinker | ^3.0 |  |
| midtrans/midtrans-php | ^2.6 |  |
| mockery/mockery | ^1.6 | yes |
| nunomaduro/collision | ^8.6 | yes |
| phpunit/phpunit | ^12.5.12 | yes |

## Frontend Packages (package.json)
| Package | Version | Dev |
|---------|---------|-----|
| @tailwindcss/vite | ^4.0.0 | yes |
| @vitejs/plugin-vue | ^6.0.6 |  |
| axios | ^1.16.1 |  |
| concurrently | ^9.0.1 | yes |
| laravel-vite-plugin | ^3.1 | yes |
| pinia | ^3.0.4 |  |
| tailwindcss | ^4.0.0 | yes |
| vite | ^8.0.0 | yes |
| vue | ^3.5.34 |  |
## Source: invitations:expire
```php
class
```

## Source: orders:expire
```php
class
```

## Source: inspire
```php
route
```

