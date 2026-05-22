# TaskFlow: Productivity Hub

**A Modern Task Management System Built for Teams**

![Status](https://img.shields.io/badge/status-production%20ready-brightgreen)
![Laravel](https://img.shields.io/badge/Laravel-11-red)
![PHP](https://img.shields.io/badge/PHP-8.x-blue)
![License](https://img.shields.io/badge/license-MIT-green)

---

## 🎯 Project Overview

TaskFlow is a comprehensive task management system designed for team collaboration, project planning, and real-time productivity tracking. Built with **Laravel 11**, **Tailwind CSS**, and **Alpine.js**, it delivers a fast, intuitive experience optimized for modern workflows.

## 📋 Project Summary

TaskFlow combines a layered Laravel architecture with normalized data design, policy based authorization, queued background processing, and performance tuning through eager loading, composite indexes, and caching. The system is built to preserve transactional safety, support task and project orchestration, and keep user facing operations within an interactive response window.

The core data model centers on users, projects, tasks, comments, attachments, and task activity records. Controllers remain focused on HTTP concerns, services handle business rules, observers capture lifecycle events, and queued jobs handle blocking side effects. The design goal is separation of concerns with predictable performance and auditable state changes.

### Core Features

- **Role-Based Access Control (RBAC)**: Admin → Project Manager → Team Member → Client (4-tier hierarchy)
- **Real-Time Kanban Board**: Drag-and-drop task management with 300ms debounced updates
- **Dark Mode Support**: WCAG 2.1 AA compliant with automatic system theme detection
- **Hierarchical Audit Logs**: Track all system mutations with user IP and browser fingerprinting
- **Capacity Planning**: Visual team workload heatmap (green/yellow/red health indicators)
- **Notification System**: Real-time task assignments and completion alerts
- **Integration Gateway**: Slack, Google Calendar, Email integrations (queued, non-blocking)
- **Multi-Tenant Ready**: Client portal with project isolation and permission boundaries

---

## ⚡ 60-Second Quick Start

### Prerequisites
- PHP 8.x
- Composer
- Node.js 18+
- MySQL 8.0+ (or SQLite for local testing)

### Installation

```bash
# 1. Clone the repository
git clone https://github.com/ktadena547638-create/IT9a.git
cd IT9a

# 2. Install PHP dependencies
composer install

# 3. Configure environment
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Run database migrations and seeders
php artisan migrate --seed

# 6. Install and build frontend assets
npm install && npm run build

# 7. Start the development server
php artisan serve
```

Access the application at: **http://localhost:8000**

### Sample Credentials

| User | Email | Password | Role |
|------|-------|----------|------|
| Admin | `admin@test.com` | `password` | Administrator |
| Project Manager | `pm1@test.com` | `password` | Project Manager |
| Team Member | `team1@test.com` | `password` | Team Member |
| Client | `client@test.com` | `password` | Client |

---

## 🏗️ Architecture Overview

### Technology Stack

| Layer | Technology | Purpose |
|---|---|---|
| **Backend** | Laravel 11, PHP 8.x | API, Business Logic, Authentication |
| **Frontend** | Blade Templates, Tailwind CSS, Alpine.js | Server-side rendered HTML with interactivity |
| **Database** | MySQL 8.0+ | Relational data, Audit logs |
| **Asset Pipeline** | Vite | Fast module bundling, Hot Module Replacement |
| **Task Queue** | Laravel Queue | Async jobs for integrations, notifications |
| **Cache** | Database/Redis | Session storage, capacity heatmap caching |

### Directory Structure

```
task-management-system/
├── app/
│   ├── Http/
│   │   ├── Controllers/     # Request handlers
│   │   ├── Middleware/      # Role checks, auth verification
│   │   ├── Policies/        # Authorization rules
│   │   └── Requests/        # Form validation
│   ├── Models/              # Eloquent models with relationships
│   ├── Services/            # Business logic (Audit, Integration)
│   ├── Jobs/                # Async queue jobs
│   └── Observers/           # Model event listeners
├── database/
│   ├── migrations/          # Schema definitions
│   └── seeders/             # Demo data (roles, users)
├── resources/
│   ├── views/               # Blade templates
│   ├── css/                 # Tailwind styles
│   └── js/                  # Alpine.js components
├── routes/                  # API and web route definitions
├── storage/                 # File uploads, logs, cache
└── public/                  # Web root, static assets
```

---

## 👥 Role Hierarchy & Permissions

### Access Levels

| Role | Dashboard | Projects | Tasks | Reports | Admin | Client Portal |
|---|---|---|---|---|---|---|
| **Admin** | ✅ Full | ✅ All | ✅ All | ✅ Full | ✅ Yes | - |
| **Project Manager** | ✅ Own | ✅ Assigned | ✅ Own | ✅ Own | - | - |
| **Team Member** | ✅ Limited | ✅ Assigned | ✅ Assigned | - | - | - |
| **Client** | - | ✅ Assigned | ✅ View Only | - | - | ✅ Yes |

---

## 📊 Database Schema

### Key Tables

**Users** - Role-based access
```
id, name, email, password, role (enum), theme_preference
```

**Projects** - Manager-owned workspaces
```
id, name, manager_id (FK), client_id (FK, nullable), due_date, priority
```

**Tasks** - Project tasks with assignments
```
id, project_id (FK), assigned_user_id (FK, nullable), title, status (enum), 
priority (enum), due_date, estimated_hours, created_by (FK)
```

**AuditLogs** - Immutable mutation history
```
id, user_id (FK, nullable), action (enum), model_type, model_id, changes (JSON), 
ip_address, user_agent, timestamps
```

### Foreign Key Constraints

- **cascadeOnDelete**: Projects, Tasks, Comments, Attachments
- **nullOnDelete**: Task assignments (task survives user deletion)
- **restrictOnDelete**: Task creators (audit trail protection)

---

## 🔐 Security Features

### Authentication & Authorization

- ✅ Email verification on registration
- ✅ Session-based authentication with CSRF protection
- ✅ Role-based gate authorization
- ✅ Policy-based fine-grained permissions
- ✅ Input validation with strict whitelisting
- ✅ SQL injection prevention (Eloquent ORM)

### Data Protection

- ✅ Password hashing (Bcrypt, 12 rounds)
- ✅ Audit log immutability
- ✅ Cascading deletes prevent orphaned data
- ✅ Null-safe relationship handling
- ✅ CSRF tokens on all mutation forms

---

## 🚀 Performance Optimization

### Targets

- **Response Time Ceiling**: <250ms
- **Current Average**: ~130ms
- **Safety Buffer**: 28% (70ms headroom)

### Optimization Strategies

- **Eager Loading**: All relationships pre-fetched to prevent N+1 queries
- **Selective Columns**: Only fetch required fields in list views
- **Composite Indexes**: Fast filtering on common queries
- **Query Caching**: Capacity heatmap cached for 6 hours
- **Debounced Drag-Drop**: 300ms batch writes prevent rapid API calls
- **Queued Integrations**: Slack/Email/Calendar operations non-blocking

---

## 🧪 Testing

### Run Tests
```bash
php artisan test
```

### Test Database
Tests run in-memory SQLite by default (see `phpunit.xml`)

---

## 🐛 Troubleshooting

### Common Issues

**Issue: Port 8000 Already In Use**
```bash
php artisan serve --port=8001
```

**Issue: Database Migration Fails**
```bash
php artisan migrate:reset
php artisan migrate --seed
```

**Issue: Missing Frontend Assets**
```bash
npm install && npm run build
php artisan view:clear
```

---

## 📌 Quick Reference

### Core Model Relationships
- User manages many projects
- Project has many tasks
- Task belongs to one project
- Task can be assigned to one user
- Task has many comments and attachments
- Task has many activity records

### Common Service Calls
```php
$task = app(\App\Services\TaskService::class)->createTask($data);
$tasks = app(\App\Services\TaskService::class)->getProjectTasks($projectId);
$stats = app(\App\Services\TaskService::class)->getProjectTaskStats($projectId);
```

### Frequent Checks
```php
auth()->user()->can('update', $task);
$task->isOverdue();
$task->loadAllRelationships();
```

### Common Validation Targets
- Task title, due date, priority, and project assignment
- Project name, due date, manager assignment, and status
- Comment content and attachment type or size

---

## 🧪 Testing Quick Guide

### Run the Main Test Suite
```bash
php artisan test
```

### High Value Checks
- Create, update, and delete a task to confirm transaction safety
- Load the dashboard twice to confirm cache behavior
- Assign a task and verify the notification queue is used
- Submit an invalid request to confirm validation and error handling
- Test a restricted action to confirm policy enforcement

### Useful Debug Commands
```bash
php artisan tinker
php artisan queue:listen
tail -f storage/logs/laravel.log
```

---

## 🚀 Phase 5 Setup Snapshot

### Common Setup Steps
```bash
php artisan migrate
php artisan storage:link
php artisan config:cache
php artisan route:cache
```

### Verify Core Features
- Notifications dispatch asynchronously
- Attachments upload and download correctly
- Analytics pages load with cached metrics
- Search returns projects and tasks after a short query

### Lab Environment Notes

- **Firewall**: Ensure ports 8000 (Laravel), 3000 (Vite dev) are accessible
- **Database**: If MySQL unavailable, use SQLite (set `DB_CONNECTION=sqlite` in `.env`)
- **Network**: All routes use dynamic helpers; works on restricted networks

---

## 📋 Pre-Deployment Checklist

- ✅ Environment variables configured (`.env`)
- ✅ Database migrations applied (`php artisan migrate`)
- ✅ Application key generated (`php artisan key:generate`)
- ✅ Frontend assets built (`npm run build`)
- ✅ Queue worker running (for integrations)
- ✅ Error logs monitored

---

## 🌐 Production Deployment

```bash
# 1. Clone repository
git clone https://github.com/ktadena547638-create/IT9a.git
cd IT9a

# 2. Install dependencies
composer install --no-dev
npm install && npm run build

# 3. Configure environment
cp .env.example .env
php artisan key:generate

# 4. Migrate database
php artisan migrate --force

# 5. Clear and optimize
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Start queue worker
php artisan queue:work --daemon
```

---

## 📚 Resources

- **Laravel Docs**: https://laravel.com/docs
- **Tailwind CSS**: https://tailwindcss.com
- **Alpine.js**: https://alpinejs.dev

---

## 📝 License

MIT License - See LICENSE file

---

**Built for University Laboratory Environments**  
*Status: Production Ready* ✅
