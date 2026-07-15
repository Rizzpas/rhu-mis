# Silang RHU Management Information System (MIS)

The Silang RHU Management Information System (MIS) is a web-based, cloud-hosted application developed to digitize and streamline the end-to-end patient care workflow of the Rural Health Unit of Silang, Cavite. 

The system replaces paper-based processes including manual logbook registration, manila folder retrieval, handwritten prescriptions, and verbal queue management with a fully integrated digital platform.

## Key Features

- **Role-Based Access Control (RBAC):** Supports 9 distinct user roles including Super Admin, Admin, Information Desk, Doctors (Regular/Pedia), Clinical Nurses, Vitals Nurses, and Lab/Radiology staff.
- **Intelligent Queueing Engine:** Automated, priority-aware queueing system that routes patients based on severity, priority lane (Senior/PWD), and doctor availability.
- **Online Appointment Portal:** Public-facing portal for booking Pedia and Adult Follow-up consultations with OTP verification and rate limiting.
- **Electronic Health Records (EHR):** Secure, immutable clinical histories, prescriptions, and vitals snapshots for every patient.
- **Content Management System (CMS):** Two-tier approval system for publishing announcements and health advisories to the public portal.
- **Analytics Dashboard:** Real-time operational metrics, peak hour heatmaps, and staff productivity tracking.

## Technology Stack

- **Backend:** PHP 8.2+ / Laravel 12.0
- **Frontend:** Laravel Blade, Tailwind CSS v4, Alpine.js, Vanilla JS
- **Database:** MySQL 8.x (InnoDB with pessimistic locking)
- **Asset Bundler:** Vite

## Installation & Setup

1. **Clone the repository:**
   ```bash
   git clone https://github.com/Rizzpas/rhu-mis.git
   cd rhu-mis
   ```

2. **Install dependencies:**
   ```bash
   composer install
   npm install
   ```

3. **Environment Setup:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Update your `.env` file with your local database credentials.*

4. **Database Migration & Seeding:**
   ```bash
   php artisan migrate --seed
   ```

5. **Start the Development Server:**
   This project uses a unified command to run the Laravel server, Queue worker, and Vite simultaneously:
   ```bash
   npm run dev
   ```

## Authors
- Conchas, Isuga, Ripas, Takeuchi

---
*For in-depth architecture, database schema, and module logic, please refer to the `system_documentation.md` file included in this repository.*
