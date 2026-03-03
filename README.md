# 📐 MathPlan — Full-Stack PHP Project

A complete full-stack web application: **30-day math study planner** with authentication, progress tracking, and admin dashboard.


## 🗂 Project Structure
```
mathplan/
├── index.php          ← Front controller (routes API + SPA)
├── app.php            ← Full SPA (single-page frontend)
├── .htaccess          ← Apache URL rewriting
│
├── config/
│   ├── database.php   ← PDO connection
│   ├── helpers.php    ← Utilities: json_out(), validate(), clean()
│   └── schema.sql     ← DB schema + seed data
│
├── middleware/
│   └── auth.php       ← Token auth: getUser(), mustAuth(), mustAdmin()
│
├── auth/
│   └── index.php      ← Register, Login, Logout, Me
│
├── api/
│   └── plan.php       ← Study plan days + progress tracking
│
└── admin/
    └── index.php      ← Admin dashboard (stats, users, day analytics)
```

---

## 🔑 Default Accounts

| Role    | Email                 | Password   |
|---------|-----------------------|------------|
| Admin   | admin@mathplan.dev    | Admin1234! |

Register new student accounts via the UI.

---

## 📡 API Reference

### Auth
| Method | URL | Description |
|--------|-----|-------------|
| POST | `/api/auth?action=register` | Create account |
| POST | `/api/auth?action=login`    | Login → token |
| POST | `/api/auth?action=logout`   | Revoke token |
| GET  | `/api/auth?action=me`       | Current user |

### Study Plan
| Method | URL | Auth | Description |
|--------|-----|------|-------------|
| GET  | `/api/plan?action=all`                    | Optional | All 30 days (+ progress) |
| GET  | `/api/plan?action=one&day=1`              | —        | Single day |
| GET  | `/api/plan?action=progress&day=1`         | ✅       | User progress for day |
| POST | `/api/plan?action=progress&day=1`         | ✅       | Save progress/notes |

### Admin
| Method | URL | Description |
|--------|-----|-------------|
| GET  | `/api/admin?action=stats`         | Dashboard overview |
| GET  | `/api/admin?action=users`         | All users (paginated) |
| GET  | `/api/admin?action=users&id=2`    | User + full progress |
| POST | `/api/admin?action=delete&id=2`   | Delete student |

---

## ✨ Features

**Frontend (Single-Page App)**
- 🏠 Landing page with stats
- 🔐 Login & Register with validation
- 📅 Interactive 30-day study plan
- ✅ Mark days complete / incomplete
- 📝 Add personal notes per day
- 📊 Live progress bar & stats
- 🔍 Filter by week or completion status
- 📱 Responsive design

**Backend (PHP REST API)**
- 🔒 bcrypt password hashing
- 🎟️ Server-side token sessions (revokable)
- 🛡️ Role-based access (student / admin)
- 💾 PDO prepared statements (SQL injection safe)
- 🧹 Input sanitization

**Admin Dashboard**
- 📈 Stats: total students, active sessions, completions
- 🏆 Top students leaderboard
- 📋 Recent activity feed
- 👥 User management + delete
- 📊 Per-day completion rates

---

## 🛠 Tech Stack
- **Backend:** PHP 8.1+ (no framework)
- **Database:** MySQL 8.0+ / MariaDB
- **Frontend:** Vanilla JS SPA (no framework, no build step)
- **Auth:** Bearer token (server-side sessions)

---

*Built with PHP 8.1 · PDO · MySQL · Vanilla JS — zero dependencies.*
By Radhwane Harres in 2026
