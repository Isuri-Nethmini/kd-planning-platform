# KD Planning & Design — Setup & Handover

Online House Plan Browsing, Inquiry and Client Portal Platform
LNBTI Industry Project · Isuri Nethmini
Client: Dhanushka Chathuranga De Soysa, KD Planning & Design, Minuwangoda

---

## First-time setup

```bash
composer install
npm install
php artisan migrate:fresh --seed
php artisan storage:link
npm run build
php artisan serve          # http://127.0.0.1:8000
```

`migrate:fresh` rebuilds every table and loads demo data: 12 plans, 13
inquiries spread over six months across every pipeline stage, five real
completed projects, and a demo client (Ruwan Fernando) mid-way through a build
with real site photos, one verified payment and one receipt awaiting review.
Re-run it whenever a new version adds migrations, and before every demo —
the demo data is dated relative to the day you seed it.

## Test accounts (development only — delete before going live)

| Area | Email | Password |
|---|---|---|
| Admin — primary | `kdplanning@gmail.com` | `password123` |
| Admin — staff | `staff@kdplanning.test` | `password123` |
| Client portal | `client@kdplanning.test` | `password123` |

Admin sign-in: `/admin/login` · Client sign-in: `/portal/login`

## Email

Sent through the business Gmail account (`kdplanningdesign@gmail.com`) using an
app password. The settings live in `.env`, which git ignores, so the password is
never committed.

- **Keep a backup of your `.env` outside the project folder.** Extracting a new
  zip over the project replaces it.
- Accounts you create while testing receive real emails, so use addresses you
  can check.
- If a send fails, the action still completes and the error is written to
  `storage/logs/laravel.log`.

## Visual design

Blueprint/drafting theme in the client's brand colours. Depth is added with
pure CSS (no 3D library): extruded headings, cards that tilt toward the pointer
(`resources/js/tilt.js`), stacked drawing-sheet frames, raised buttons, and an
isometric floor-plan model in the homepage hero built from stacked SVG layers.
All motion switches off for visitors who request reduced motion.

## Automated tests

```bash
php artisan test
```

34 feature tests, run against an in-memory database (no build or network
needed), covering access control
(clients cannot see each other's projects or receipts, staff cannot verify
payments), the receipt workflow, admin security and the public site.

---

## What the system does

**Public website** — plan catalogue with search and filters, plan detail pages,
completed projects, blog, About page, guest inquiry form, WhatsApp links carrying
the plan details. Responsive, with a mobile menu.

**Admin panel** — plans, inquiries with a sales pipeline, completed projects,
blog, testimonials, analytics, downloadable CSV reports, settings, admin accounts.

**Client portal** — confirmed clients sign in to follow their build stage by
stage with site photos, and upload bank-transfer receipts for each instalment.

### Roles

| | Staff admin | Primary admin |
|---|---|---|
| Plans, inquiries, blog, testimonials, completed projects | ✓ | ✓ |
| Clients and construction progress | ✓ | ✓ |
| Payment schedules and receipt verification | — | ✓ |
| Analytics, view counts, plan and summary reports | — | ✓ |
| Admin accounts, Security Log | — | ✓ |

### The client journey

1. A visitor sends an inquiry (no account needed).
2. An admin works the lead through the sales pipeline.
3. When it reaches **Converted**, the inquiry page shows **Create client
   account**. The form is prefilled from the inquiry.
4. The client is emailed a temporary password and must set their own on first
   sign-in.
5. Staff update construction stages and add site photos. Starting or finishing
   a stage emails the client.
6. The primary admin adds the payment schedule. The client pays by bank
   transfer and uploads the receipt; the business is emailed.
7. The primary admin verifies or rejects it from **Payments**. The client is
   emailed either way, and a rejection shows them the reason.

---

## Security design

| Protection | How |
|---|---|
| Separate client and admin logins | Different tables, and clients use Laravel's separate `customer` guard — neither session satisfies the other's checks |
| Clients see only their own data | Ownership checked on the server on every request; others get a 404, which doesn't confirm the record exists |
| Private files | Receipts and site photos are on the private disk with no URL; served only by `ProtectedFileController` after a check |
| Brute-force protection | 5 failed sign-ins lock that email + IP for 15 minutes (admin and client) |
| Temporary passwords | System-generated, never seen by anyone; forced change on first sign-in, after which the emailed one stops working |
| Password policy | 10+ characters, mixed case, number, symbol, and checked against known breaches (`App\Support\PasswordPolicy`) |
| Session safety | New session ID on sign-in and password change; admins time out after 30 minutes idle; a deleted account is signed out on its next request |
| Audit trail | Security Log records sign-ins, lockouts, blocked pages and account changes, with a name snapshot so deleted accounts keep their history. Every receipt decision records who made it and when |

---

## Where things live

| What | Where |
|---|---|
| Public controllers | `app/Http/Controllers/Public/` |
| Admin controllers | `app/Http/Controllers/Admin/` |
| Client portal controllers | `app/Http/Controllers/Portal/` |
| Private file access | `app/Http/Controllers/ProtectedFileController.php` |
| Emails | `app/Support/Notifier.php` |
| Password rules | `app/Support/PasswordPolicy.php` |
| Uploaded plan images (public) | `storage/app/public/plans/` |
| Receipts and site photos (private) | `storage/app/private/` |
| Client-supplied media | `public/media/` |
| Tests | `tests/Feature/` |

---

## Before going live

1. Delete the three test accounts above and create real ones through the admin panel.
2. Replace the 12 placeholder plans with the client's real plans and drawings.
3. Replace the five placeholder testimonials with genuine ones.
4. Set `APP_ENV=production` and `APP_DEBUG=false`, and serve over HTTPS.
5. Generate a fresh Gmail app password and update `.env`.
6. Schedule database and `storage/` backups.

## Scope changes during the project

- **3D plan visualisation** — originally requested, then withdrawn by the client:
  he works in AutoCAD, Revit and Lumion and cannot produce web-ready 3D output.
- **Customer accounts** — the original SRS specified none. Requested by the
  interim panel and agreed by the client, becoming the client portal.
- **Receipt upload** — requested at the post-interim evaluation, as there is no
  payment gateway by the client's choice.
