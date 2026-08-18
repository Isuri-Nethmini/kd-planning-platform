# Feature Planning Notes

## Confirmed Features (from client requirements — June 2026)

Status legend: ✅ done · 🟡 partial · ⬜ not started

### High Priority
- ✅ FR-01: Plan Catalogue & Listings (40 plans, 2 new per week)
- ✅ FR-02: Plan Categories & Tags (Single Storey, Double Storey, Modern, Colonial, Budget-Friendly)
- 🟡 FR-03: Plan Detail Pages (multiple images, specs) — 2D gallery done; **3D views not started**
- ✅ FR-04: Advanced Search & Filters (bedrooms, floors, category, sort; searches name/description/style)
- ✅ FR-06: Guest Inquiry System (no login required)
- 🟡 FR-07: Email Notifications — code complete, currently `MAIL_MAILER=log`; **needs Gmail SMTP credentials**
- ✅ FR-08: Multi-Admin Dashboard (2 admin users, session-based `AdminAuth` middleware)
- ✅ FR-09: Plan Image Gallery Management (upload, primary image, delete-with-cleanup)
- ✅ FR-13: WhatsApp Chat Button (floating, all pages, official logo, number editable in Settings)
- ✅ FR-16: Responsive Design (mobile-first)

### Medium Priority
- ✅ FR-05: Featured Plans on Homepage (admin-selectable)
- ✅ FR-10: Blog / News Section (public index + article page, admin CRUD, draft/published)
- ✅ FR-11: Testimonials Section (homepage display + admin CRUD)
- ✅ FR-12: Completed Projects Gallery (public gallery + admin CRUD with multi-photo upload)
- ✅ FR-14: Plan View Counter
- ✅ FR-15: Analytics & Reports Dashboard (views, inquiries over time, conversion, category breakdown)

### Added beyond the original list
- ✅ Admin Settings page — WhatsApp number and notification email editable without touching code
- ✅ Hero background video slot — auto-detects `public/media/hero.mp4` when the client supplies it

## Outstanding — blocked on client
1. Real house plan drawings and renders (placeholder renders until uploaded)
2. Hero background video (`public/media/hero.mp4`)
3. Business logo (`public/media/logo.png`)
4. Real plan names, descriptions and prices for the ~40 existing plans

## Outstanding — development
1. FR-03: 3D plan visualisation (client request — scope and library still to be decided)
2. FR-07: Gmail SMTP credentials + switch `MAIL_MAILER` to `smtp`
3. Remove price from Featured Plans cards on homepage — **done**, cards now show specs only

## Client Requirements Summary
- Total existing plans: ~40
- New plans added: ~2 per week
- Admin users: 2 (owner + 1 staff)
- Language: English only
- No payment gateway required
- No customer login required
- Budget: LKR 30,000

## Tech Stack Decision Notes
- Laravel chosen for built-in auth, Eloquent ORM, Storage Facade, and Mail
- Tailwind CSS for fast responsive UI without custom CSS overhead
- Alpine.js for lightweight dynamic interactions (no full React/Vue needed)
- MySQL standard with Laravel Eloquent
- Local file storage — Storage Facade abstraction allows future S3 migration in one config change
