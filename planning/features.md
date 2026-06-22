# Feature Planning Notes

## Confirmed Features (from client requirements — June 2026)

### High Priority
- FR-01: Plan Catalogue & Listings (40 plans, 2 new per week)
- FR-02: Plan Categories & Tags (Single Storey, Double Storey, Modern, Colonial, Budget-Friendly)
- FR-03: Plan Detail Pages (multiple images, specs, 2D/3D views)
- FR-04: Advanced Search & Filters (price, bedrooms, floors, style, category)
- FR-06: Guest Inquiry System (no login required)
- FR-07: Email Notifications (Gmail SMTP to admin on new inquiry)
- FR-08: Multi-Admin Dashboard (2 admin users)
- FR-09: Plan Image Gallery Management
- FR-13: WhatsApp Chat Button (floating, all pages)
- FR-16: Responsive Design (mobile-first)

### Medium Priority
- FR-05: Featured Plans on Homepage (admin-selectable + most viewed)
- FR-10: Blog / News Section
- FR-11: Testimonials Section
- FR-12: Completed Projects Gallery
- FR-14: Plan View Counter
- FR-15: Analytics & Reports Dashboard

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
