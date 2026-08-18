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

## Client assets (received 18 Aug 2026)

| Asset | Where it went |
|---|---|
| `logo.ai` / `logo.png` | Header, footer and all favicons — rasterised from the **vector**, so it stays sharp |
| `hero video.mp4` (100 MB) | Cut to an 18 s silent 720p loop, **3.0 MB**, at `public/media/hero.mp4` with a poster frame |
| `Previous work/` (5 images) | Seeded as five real completed projects with the client's own names, locations and costs |
| `About us section.doc` | The `/about` page — company profile, principles, "We Design, We Build" |
| `Contcas page.doc` | Director credentials and phone number on `/about`; contact details in the footer |
| `3d architec.jpg` | `public/media/projects/render-showcase.jpg` — available for use as plan artwork |

The service images (CCTV, electrical work, consulting, construction) were **not**
used. They suggest the business offers more than house plans, which is a scope
question for the client rather than something to guess at.

## Known gaps

1. **3D plan views (FR-03)** — client requested; not started. Approach not yet chosen.
2. **Email notifications (FR-07)** — the code is written and wired up, but
   `MAIL_MAILER=log` in `.env`, so notifications are written to
   `storage/logs/laravel.log` rather than sent. Switching to Gmail SMTP needs an
   app password from the client.
3. **Real plan content** — the 12 seeded plans are placeholder data. The client
   has around 40 real plans to supply, with drawings.
4. **Testimonials are placeholder.** The five seeded testimonials are invented
   names. Completed projects, by contrast, are real. Ask the client for genuine
   quotes before launch — and be ready to say so if asked during a review.
5. **`.obj` 3D files are not web-renderable.** Browsers cannot display `.obj`
   natively, and the format is geometry-only with separate `.mtl` and texture
   files, uncompressed. The web standard is **glTF 2.0 (`.glb`)** — one
   self-contained file, typically 5-20x smaller. Most CAD tools (SketchUp,
   Revit, 3ds Max, Blender, Lumion) can export or convert to it.

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
