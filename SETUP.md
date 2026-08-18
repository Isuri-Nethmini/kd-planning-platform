# KD Planning & Design — Setup & Handover

Online House Plan Browsing and Inquiry Platform
LNBTI Industry Project · UOG0723006 · Isuri Nethmini
Client: Dhanushka Chathuranga De Soysa, KD Planning & Design, Minuwangoda

---

## ⚠️ Run this first

The seed data used to contain stock photos from picsum.photos. Those have been
removed from the seeder files, **but they are still sitting in your database
until you re-seed**. Run this once before you demo:

```bash
php artisan migrate:fresh --seed
php artisan storage:link
npm run build
```

`migrate:fresh` drops and rebuilds every table, so any test inquiries you have
made will be cleared. That is what you want before a demo.

`storage:link` is required for uploaded images to be visible — without it,
uploads succeed but render as broken images.

---

## Running the project

```bash
php artisan serve      # http://127.0.0.1:8000
npm run dev            # in a second terminal, for live CSS rebuilds
```

For a demo, prefer `npm run build` once over `npm run dev` — fewer moving parts.

## Admin access

| | |
|---|---|
| URL | `/admin/login` |
| Email | `kdplanning@gmail.com` |
| Password | `password123` |

A second staff account exists: `staff@kdplanning.test` / `password123`.
**Both must be changed before the site goes live.**

---

## Where things live

| What | Where |
|---|---|
| Public pages | `app/Http/Controllers/Public/` |
| Admin pages | `app/Http/Controllers/Admin/` |
| Views | `resources/views/public/`, `resources/views/admin/` |
| Design tokens (colours, fonts) | `resources/css/app.css` (`@theme` block) |
| Uploaded plan images | `storage/app/public/plans/{plan_id}/` |
| Uploaded project photos | `storage/app/public/projects/{project_id}/` |
| Client media drop folder | `public/media/` |

## Adding the client's assets when they arrive

**Hero video** — save as `public/media/hero.mp4`. The homepage detects the file
automatically and switches from the blueprint-grid fallback to the video. No
code change needed.

**House plan images** — Admin → House Plans → Edit → Images. First image
uploaded becomes the card thumbnail. Until images exist, every plan renders the
`<x-image-frame>` blueprint placeholder.

**Logo** — save as `public/media/logo.png`, then swap the text wordmark in
`resources/views/layouts/app.blade.php` (around line 22) for an `<img>` tag.

---

## Known gaps

1. **3D plan views (FR-03)** — client requested; not started. Approach not yet chosen.
2. **Email notifications (FR-07)** — the code is written and wired up, but
   `MAIL_MAILER=log` in `.env`, so notifications are written to
   `storage/logs/laravel.log` rather than sent. Switching to Gmail SMTP needs an
   app password from the client.
3. **Real content** — plan names, descriptions and prices are placeholder data
   written for development. The client has ~40 real plans to supply.

---

## Bugs found and fixed during the 18 Aug cleanup

| Bug | Impact |
|---|---|
| Controller directory was `Controllers/public/` but namespace was `...\Public` | Every public page returned HTTP 500 on Linux/macOS. Invisible on Windows because its filesystem is case-insensitive — this would have broken the first real deployment. |
| `PlanImage::getUrlAttribute()` used `Storage::url()` (default disk = `local`, root `storage/app/private`) while uploads go to the `public` disk | Uploaded images produced URLs pointing where the file did not exist. Every real image would have rendered broken. |
| Catalogue search used an ungrouped `orWhere` | The `orWhere` escaped the `active()` scope, so searching surfaced plans the admin had deliberately hidden. |
| Analytics used `HAVING` without `GROUP BY` | Works on MySQL, crashes on SQLite. Replaced with `has()` for portability. |
| `.plan-placeholder` used the `background` shorthand | Wiped out the blueprint grid whenever the two classes were combined. |
| Deleting a plan left its uploaded files on disk | Orphaned files accumulated in storage forever. Deletion now cleans up files, image rows and category links. |
| Two views had a literal `...` inside a `class` attribute | Hover zoom silently did nothing on those cards. |
| `welcome.blade.php` (82 KB Laravel default) still present | Dead weight in the repo. Removed. |
