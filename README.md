🌐 Translation Management System
A high-performance translation management system built with Laravel 11 and Vue 3. Designed to handle over 100,000 translation records with optimized export speeds.

📁 Project Structure
The project is split into two main directories:

/backend: Laravel 11 API (Sanctum Auth, MySQL/SQLite).

/frontend: Vue 3 SPA (Vite, Axios, CSS).

🚀 Getting Started
1. Backend Setup (Laravel)
Bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
Modify .env:
Ensure your database credentials are correct. For unit testing, the system is configured to use SQLite (In-Memory) automatically via phpunit.xml.

Code snippet
DB_CONNECTION=mysql
DB_DATABASE=digitaltolk_db
...
Run Migrations & Seeders:

Bash
php artisan migrate --seed


2. Frontend Setup (Vue)
Bash
cd frontend
npm install
cp .env.example .env
Modify .env:
Point the frontend to your backend API URL.

Code snippet
VITE_API_BASE_URL=http://localhost:8000


🛠 Features & Commands
Bulk Data Generation
To test the system's performance with 100k rows, use the custom artisan command. This utilizes background jobs to prevent memory exhaustion.

Bash
# Dispatch 100k rows (split into background jobs)
php artisan generate:translations-data 100000

# Process the jobs
php artisan queue:work --stop-when-empty


Running Unit Tests
The test suite covers Repository, Service, and Controller layers using PHPUnit.

Bash
cd backend
php artisan test --filter TranslationTest
Note: Tests require the php-sqlite3 driver as they run in-memory for speed.

📦 Content Delivery Network (CDN) Support
To optimize the delivery of translation files for worldwide users, the system supports Locale-Specific Exports.
Direct Download: Small JSON files per locale (e.g., en.json) can be cached on a CDN (like Cloudflare or AWS CloudFront).

Implementation:

Set the Cache-Control headers in the TranslationController@exportLocale.

Point your CDN to the /api/translations/export/{code} endpoint.

🖥 User Interface Overview
Key Components:

Dashboard View: Displays a paginated list of 100k records. Features a Debounced Search to ensure the API isn't overwhelmed while typing.

Quick Export Zone: Located at the top, allowing for single-click downloads of specific language JSON files.

Live Translation Test: A dedicated page to verify how keys appear in "Web" vs "Mobile" views in real-time.

📖 API Documentation (Postman)
The API is protected by Laravel Sanctum. To test via Postman:

Login: POST /api/login to receive a plainTextToken.

Auth: Add the token as a Bearer Token in the Authorization header for all other requests.

Endpoints:

GET /api/languages: List active languages.

GET /api/translations: List/Filter translations.

GET /api/translations/export/{code}: Export specific locale.

Inclusion in the TranslationTest suite.
