# Feature Planning Notes

## Confirmed Features (from client requirements — June 2026)

Status legend: ✅ done · 🟡 partial · ⬜ not started

### High Priority
- ✅ FR-01: Plan Catalogue & Listings (40 plans, 2 new per week)
- ✅ FR-02: Plan Categories & Tags (Single Storey, Double Storey, Modern, Colonial, Budget-Friendly)
- 🟡 FR-03: Plan Detail Pages (multiple images, specs) — 2D gallery done; **3D views not started**
- ✅ FR-04: Advanced Search & Filters (bedrooms, floors, category, sort; searches name/description/style)
- ✅ FR-06: Guest Inquiry System (no login required) — now backed by a five-stage
  sales pipeline (New → Read → Quoted → Converted / Closed) with a recorded
  construction estimate and private admin follow-up notes per lead
- 🟡 FR-07: Email Notifications — code complete, currently `MAIL_MAILER=log`; **needs Gmail SMTP credentials**
- ✅ FR-08: Multi-Admin Dashboard — session-based `AdminAuth` middleware, plus full
  admin account management (add/edit/remove admins, primary vs staff roles,
  `PrimaryAdmin` middleware gating). Safeguards: cannot delete own account,
  cannot delete or demote the last primary admin.
- ✅ FR-09: Plan Image Gallery Management (upload, primary image, delete-with-cleanup)
- ✅ FR-13: WhatsApp Chat Button (floating, all pages, official logo, number editable in Settings)
- ✅ FR-16: Responsive Design (mobile-first)

### Medium Priority
- ✅ FR-05: Featured Plans on Homepage (admin-selectable)
- ✅ FR-10: Blog / News Section (public index + article page, admin CRUD, draft/published)
- ✅ FR-11: Testimonials Section (homepage display + admin CRUD)
- ✅ FR-12: Completed Projects Gallery (public gallery + admin CRUD with multi-photo upload)
- ✅ FR-14: Plan View Counter
- ✅ FR-15: Analytics & Reports Dashboard — plan views, inquiries over time,
  view→inquiry and inquiry→sale conversion, total quoted vs converted value,
  pipeline stage breakdown, category breakdown

### Added beyond the original list
- ✅ Admin Settings page — WhatsApp number and notification email editable without touching code
- ✅ Admin Users page — create, edit and remove admin accounts with role-based permissions
- ✅ Hero background video slot — auto-detects `public/media/hero.mp4` when the client supplies it
- ✅ "How It Works" section — explains the offline purchase journey to buyers
  (browse → inquire → we call back → confirm and build), on the homepage and
  plan catalogue, with a "what happens next" panel on the inquiry success page

## Outstanding — blocked on client
1. Real house plan drawings and renders (placeholder renders until uploaded)
2. Hero background video (`public/media/hero.mp4`)
3. Business logo (`public/media/logo.png`)
4. Real plan names, descriptions and prices for the ~40 existing plans

## Sales process (no online payment — per client requirement)

The site is a catalogue and lead-capture system, not an e-commerce store. There
is deliberately no checkout and no customer login. The journey is:

1. Buyer browses and filters the catalogue (site)
2. Buyer submits an inquiry, or opens WhatsApp with the plan pre-filled (site)
3. Inquiry is stored, admin is emailed, lead appears as "New" (site)
4. Admin calls or WhatsApps back, discusses land and modifications (offline)
5. Admin records the construction estimate and notes against the lead (admin panel)
6. Payment and handover of drawings happen offline (bank transfer / office)
7. Admin marks the lead Converted or Closed (admin panel)

Steps 1–3 and the record-keeping in 5 and 7 are what the system provides. The
`quoted_amount` field lets the owner see total pipeline value versus won value.

**To confirm with the client:** whether an advance payment is taken, and whether
drawings are collected from the office or delivered. The "How It Works" copy in
`resources/views/components/how-it-works.blade.php` is deliberately general on
step 4 until this is known.

## Outstanding — development
1. FR-03: 3D plan visualisation (client request — scope and library still to be decided)
2. FR-07: Gmail SMTP credentials + switch `MAIL_MAILER` to `smtp`
3. Remove price from Featured Plans cards on homepage — **done**, cards now show specs only
4. Price vs. quote contradiction raised at the last panel review — **resolved**.
   The figure on a plan is now labelled **Plan Price** (the drawings), and the
   call to action is **Request a Construction Estimate**. An explainer panel on
   the plan detail page states that construction is quoted separately because it
   depends on land, materials and finishes. Copy aligned sitewide.

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
