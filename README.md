# Online House Plan Browsing and Inquiry Platform

> **Industry Project — 3rd Year, 2nd Semester**
> Lanka Nippon BizTech Institute (LNBTI) — Japanese IT University

---

## Project Overview

A professional web-based platform for **KD Planning & Design**, a house plan and construction services business based in Minuwangoda, Sri Lanka. The platform allows customers to browse available house plans online, view detailed specifications, and submit inquiries — replacing the current manual process of WhatsApp and phone-based communication.

---

## Student Details

| Field | Details |
|---|---|
| **Name** | Rajakaruna Mudiyanselage Isuri Nethmini |
| **Registration No** | UOG0723006 |
| **Email** | isuri.uog07@edu.lnbti.lk |
| **Institution** | LNBTI — Japanese IT University |

---

## Client Details

| Field | Details |
|---|---|
| **Client** | Dhanushka Chathuranga De Soysa |
| **Business** | KD Planning & Design |
| **Location** | Minuwangoda, Sri Lanka |
| **Email** | kdplanning@gmail.com |

---

## Technology Stack

| Layer | Technology |
|---|---|
| **Frontend** | Laravel Blade, Tailwind CSS, Alpine.js |
| **Backend** | Laravel 11 (PHP 8.2+) |
| **Database** | MySQL 8.0+ |
| **File Storage** | Laravel Local Storage (Storage Facade) |
| **Email** | Laravel Mail with SMTP |
| **Version Control** | Git / GitHub |
| **Hosting** | VPS / cPanel Shared Hosting |

---

## System Features (16 Functional Requirements)

| ID | Feature | Priority |
|---|---|---|
| FR-01 | Plan Catalogue & Listings | High |
| FR-02 | Plan Categories & Tags | High |
| FR-03 | Plan Detail Pages | High |
| FR-04 | Advanced Search & Filters | High |
| FR-05 | Featured / Popular Plans on Homepage | Medium |
| FR-06 | Guest Inquiry & Quote Request System | High |
| FR-07 | Email Notification System | High |
| FR-08 | Multi-Admin Dashboard | High |
| FR-09 | Plan Image Gallery Management | High |
| FR-10 | Blog / News Section | Medium |
| FR-11 | Testimonials Section | Medium |
| FR-12 | Completed Projects Gallery | Medium |
| FR-13 | WhatsApp Chat Button Integration | High |
| FR-14 | Plan View Counter | Medium |
| FR-15 | Analytics & Reports Dashboard | Medium |
| FR-16 | Responsive Design | High |

---

## Project Structure

```
kd-planning-platform/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── AdminDashboardController.php
│   │   │   │   ├── HousePlanController.php
│   │   │   │   ├── CategoryController.php
│   │   │   │   ├── InquiryController.php
│   │   │   │   ├── BlogController.php
│   │   │   │   ├── TestimonialController.php
│   │   │   │   ├── CompletedProjectController.php
│   │   │   │   └── AnalyticsController.php
│   │   │   └── Public/
│   │   │       ├── HomeController.php
│   │   │       ├── HousePlanController.php
│   │   │       └── InquiryController.php
│   ├── Models/
│   │   ├── HousePlan.php
│   │   ├── Category.php
│   │   ├── PlanImage.php
│   │   ├── Inquiry.php
│   │   ├── BlogPost.php
│   │   ├── Testimonial.php
│   │   ├── CompletedProject.php
│   │   └── AdminUser.php
│   └── Mail/
│       └── NewInquiryNotification.php
├── database/
│   └── migrations/
│       ├── create_house_plans_table.php
│       ├── create_categories_table.php
│       ├── create_plan_images_table.php
│       ├── create_plan_category_table.php
│       ├── create_inquiries_table.php
│       ├── create_blog_posts_table.php
│       ├── create_testimonials_table.php
│       ├── create_completed_projects_table.php
│       └── create_admin_users_table.php
├── resources/
│   └── views/
│       ├── public/
│       │   ├── home.blade.php
│       │   ├── plans/
│       │   │   ├── index.blade.php
│       │   │   └── show.blade.php
│       │   ├── blog/
│       │   ├── testimonials.blade.php
│       │   ├── completed-projects.blade.php
│       │   └── inquiry.blade.php
│       └── admin/
│           ├── dashboard.blade.php
│           ├── plans/
│           ├── inquiries/
│           ├── blog/
│           ├── testimonials/
│           └── analytics/
├── routes/
│   ├── web.php
│   └── admin.php
└── docs/
    ├── SRS_UOG0723006.docx
    ├── Project_Proposal_UOG0723006.pdf
    └── ER_Diagram/
```

---

## Database Entities

```
house_plans          → id, name, description, price, floors, bedrooms, bathrooms,
                       floor_area, style, view_count, is_featured, is_active
categories           → id, name, slug, description
plan_images          → id, house_plan_id, image_path, is_primary, sort_order
plan_category        → house_plan_id, category_id  (pivot)
inquiries            → id, name, email, phone, house_plan_id (nullable), message, status
blog_posts           → id, title, slug, cover_image, content, status, published_at
testimonials         → id, client_name, location, rating, content
completed_projects   → id, title, description, location, images
admin_users          → id, name, email, password, role, last_login_at
```

---

## Project Timeline

| Phase | Activity | Duration |
|---|---|---|
| Phase 1 | Proposal Approval & Requirement Gathering | Week 1–2 |
| Phase 2 | System Analysis & Database Design | Week 3–4 |
| Phase 3 | UI/UX Design & Prototyping | Week 5–6 |
| Phase 4 | Frontend Development (Blade + Tailwind) | Week 7–10 |
| Phase 5 | Backend Development (Laravel + MySQL) | Week 11–15 |
| Phase 6 | Integration, Testing & Bug Fixing | Week 16–17 |
| Phase 7 | Client Review & Revisions | Week 18–19 |
| Phase 8 | Deployment & Handover | Week 20 |

---

## Getting Started (Local Setup)

```bash
# 1. Clone the repository
git clone https://github.com/Isuri-Nethmini/kd-planning-platform.git
cd kd-planning-platform

# 2. Install PHP dependencies
composer install

# 3. Install Node dependencies
npm install

# 4. Copy environment file and configure
cp .env.example .env
php artisan key:generate

# 5. Configure your database in .env
DB_DATABASE=kd_planning
DB_USERNAME=root
DB_PASSWORD=

# 6. Run migrations
php artisan migrate

# 7. Link storage
php artisan storage:link

# 8. Run the development server
php artisan serve
npm run dev
```

---

## Documentation

All project documentation is available in the `/docs` folder:
- Software Requirements Specification (SRS)
- Project Proposal
- ER Diagram

---

## Current Status

> **Phase 2 — System Analysis & Database Design** (Week 3–4)

- [x] Project proposal submitted and approved
- [x] Client requirements gathered and finalized
- [x] SRS document completed (16 functional requirements)
- [x] Database schema designed
- [ ] UI/UX prototyping (upcoming — Week 5)
- [ ] Development (upcoming — Week 7+)

---

*Last updated: June 2026*
