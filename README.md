# MVMarket — Laravel Rebuild (Foundation Phase)

Multi-vendor eCommerce marketplace, rebuilt on Laravel 11 per your request
to move off Core PHP. This is **Phase 1 of the phased rebuild**: full
database schema, authentication (with 2FA and account lockout), RBAC, and
the admin panel shell. Later phases (vendor onboarding, catalog, cart/
checkout, payments, marketing) follow the same module order as the
original Core PHP build.

## Important: what's verified vs. not

This was built in a sandboxed environment with **no access to Packagist**
(Composer's package registry), so I could write and carefully review every
file, but I could not run `composer install`, `php artisan migrate`, or
actually boot the application myself. Everything below has been verified
the ways that were actually possible:

- **All 64 migration files**: syntax-checked, and cross-diffed table-by-table
  against the original, already-tested Core PHP schema — full parity confirmed.
- **All ~30 PHP application files** (models, controllers, middleware,
  services, seeders): syntax-checked individually.
- **The TOTP 2FA service**: ported from the Core PHP version, then re-ran
  the exact same round-trip test (generate → verify → pass) to confirm the
  port didn't introduce errors.
- **The frontend build pipeline**: genuinely installed via `npm install`
  and built via `npx vite build` — Bootstrap 5, Bootstrap Icons, and the
  Inter/Sora fonts all bundle correctly into a working `manifest.json`.
- **All 19 Blade views**: `.blade.php` files can't be syntax-checked with
  `php -l` (they're compiled by Laravel's Blade engine, not run as raw
  PHP), so instead I wrote a set of static-analysis scripts that verified:
  every `route()` call matches an actual defined route name (accounting
  for route-group name prefixes), every `<x-layouts.*>` component
  reference resolves to a real file, every `@if`/`@foreach`/`@forelse`
  directive is balanced, and every state-changing form has `@csrf` and
  the correct `@method` spoofing.

**What none of this proves**: that the app actually boots, that a request
completes end-to-end, or that the pages render without error. That
requires `composer install` succeeding and `php artisan serve` actually
running — please treat your first real test run as the true first
verification, and let me know what breaks.

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Edit `.env` with your database credentials, then:

```bash
php artisan migrate
php artisan db:seed
php artisan mvmarket:create-admin
npm install
npm run build
php artisan serve
```

Visit `http://127.0.0.1:8000` — you'll be redirected to `/login`. Sign in
with the admin account you just created.

### XAMPP / Apache deployment

Unlike the Core PHP version, point your Apache vhost's `DocumentRoot` at
this project's `public/` folder specifically — not the project root:

```apache
<VirtualHost *:80>
    ServerName mvmarket.local
    DocumentRoot "C:/xampp/htdocs/mvmarket-laravel/public"
    <Directory "C:/xampp/htdocs/mvmarket-laravel/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

For day-to-day development, `php artisan serve` (shown above) is simpler
and what most Laravel developers use.

## What's built (Phase 1 — Foundation)

- **Database**: all 65 original tables via Laravel migrations, plus
  Laravel's own infrastructure tables (sessions, cache, queue jobs).
  `password_resets` → Laravel's conventional `password_reset_tokens`;
  `rate_limit_hits` → replaced by Laravel's built-in rate limiter.
- **Auth**: registration with email verification (logged, not yet
  emailed — see Known Limitations), login with account lockout (5 failed
  attempts / 15 min, matching SRS 9.4), password reset, logout.
- **2FA**: TOTP setup with QR code (rendered server-side via
  `bacon/bacon-qr-code` — no external API call, unlike services that
  generate the QR image via a third-party endpoint), recovery codes,
  login challenge.
- **RBAC**: 9 seeded roles, full permission matrix across 20 modules,
  role management UI, per-user role assignment.
- **Admin panel**: dashboard, roles, permissions, users, audit log viewer
  (append-only — application code has no update/delete path into it),
  security settings.
- **Security**: CSP with per-request nonce, security headers, CSRF
  (Laravel's built-in), rate limiting on login/register/password-reset.

## Known limitations (Phase 1 scope)

- Email is logged (`storage/logs/laravel.log`), not actually sent — real
  Mailable classes are a fast follow once you confirm the environment
  works, matching the same pattern the Core PHP build used initially.
- No storefront yet — `/home` is a placeholder confirming auth/session
  works. Vendor onboarding, catalog, cart, checkout, and payments are
  Phase 2+.
- `npm audit` flags a moderate-severity advisory in `esbuild` (Vite's
  bundler) — it only affects the **local dev server**, not the production
  build output, and fixing it requires an untested major Vite version
  jump. Left as-is; safe to ignore for now.

## Next steps

Once you've confirmed `composer install` → `migrate` → `db:seed` →
`create-admin` → `npm run build` → `serve` all work and you can log in and
click around the admin panel, say the word and I'll continue with Phase 2
(vendor onboarding and store management) the same way.
