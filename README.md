# SuriSync Backend

The robust backend API for the SuriSync workplace management portal, built on Laravel 11.

## Tech Stack
- **Framework:** Laravel 11
- **Language:** PHP 8.x
- **Database:** PostgreSQL (via Supabase)
- **Authentication:** Laravel Sanctum / JWT

## Features
- **Role-Based Access Control:** Strict role enforcement (`admin`, `manager`, `staff`, `user`).
- **Tasks API:** Kanban-style task tracking with auto-generated tracking IDs (`TSK-XXXX`).
- **Reservations API:** Workflow for booking and approving meeting spaces.
- **Documents API:** Hierarchical folder system with Base64 payload decoding to bypass PHP built-in server OS restrictions.
- **Dashboard Metrics:** Aggregated statistics for workplace activity.

## Local Development

### Requirements
- PHP 8.2+
- Composer
- PostgreSQL connection (update `.env`)

### Setup
1. Navigate to the backend directory: `cd SurSync_Backend`
2. Install dependencies: `composer install`
3. Copy environment variables: `cp .env.example .env`
4. Generate app key: `php artisan key:generate`
5. Run migrations: `php artisan migrate`
6. Link storage (required for Documents module): `php artisan storage:link`
7. Start the local server: `php artisan serve`

*Note on Windows file uploads: The built-in `php artisan serve` has known issues with `multipart/form-data`. The frontend handles this by sending Base64 encoded payloads to ensure stability.*
