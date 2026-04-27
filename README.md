# 🌐 Translation Management System

A translation management system built with Laravel 11 and Vue 3.  
It is designed to handle large datasets (100,000+ translation records) while maintaining reasonable performance for listing, searching, and exporting data.

The system uses a decoupled architecture with a REST API backend and a single-page frontend.

---

## 📁 Project Structure

The project is split into two main parts:

/backend  
Laravel 11 API application  
- Authentication via Laravel Sanctum  
- Database support: MySQL (primary), SQLite (testing)

/frontend  
Vue 3 single-page application  
- Built with Vite  
- Uses Axios for API communication  
- Basic CSS for UI styling

---

## 🚀 Getting Started

### 1. Backend Setup (Laravel)

Navigate to the backend directory and install dependencies:

cd backend  
composer install  
cp .env.example .env  
php artisan key:generate  

#### Configure `.env`

Update your database configuration:

DB_CONNECTION=mysql  
DB_HOST=127.0.0.1  
DB_PORT=3306  
DB_DATABASE=digitaltolk_db  
DB_USERNAME=root  
DB_PASSWORD=  

For testing, the application uses SQLite in-memory configuration defined in `phpunit.xml`.

#### Run Migrations & Seeders

php artisan migrate --seed  

This will:
- Create database tables
- Seed initial data (including a default user)

---

### 2. Frontend Setup (Vue 3)

Navigate to the frontend directory:

cd frontend  
npm install  
cp .env.example .env  

#### Configure `.env`

Set the API base URL to match your backend:

VITE_API_BASE_URL=http://localhost:8000  

#### Run Development Server

npm run dev  

The frontend will connect to the backend API using the configured base URL.

---

## 🛠 Features & Commands

### 🔐 Authentication (Default Credentials)

A default user is created during seeding:

Username: test@example.com  
Password: password  

This can be used to log in and access the system immediately.

---

### 📊 Bulk Data Generation

To simulate large datasets, a custom Artisan command is available.

#### Step 1: Dispatch Jobs

php artisan generate:translations-data  

This queues jobs to generate translation records.

#### Step 2: Process Jobs

php artisan queue:work --stop-when-empty  

The queue worker processes jobs in the background, allowing large data generation without exhausting memory.

---

### 🧪 Running Unit Tests

cd backend  
php artisan test --filter TranslationTest  

Notes:
- Tests run using SQLite in-memory for speed
- Ensure the `php-sqlite3` extension is installed

The test suite covers:
- Repository layer  
- Service layer  
- Controller layer  

---

## 📦 CDN & Export Optimization

The system supports exporting translations per locale.

### Key Idea

Instead of exporting all translations at once, data is split into smaller JSON files per language (e.g., `en.json`).  
This makes it easier to cache and distribute via CDNs.

### Implementation Details

- Export endpoint:

/api/translations/export/{code}

- Cache headers should be configured in:

TranslationController@exportLocale  

This allows responses to be cached by services like Cloudflare or CloudFront.

---

## 🖥 User Interface Overview

### Dashboard View

- Displays a paginated list of translation records  
- Designed to handle large datasets (100k+ rows)  
- Includes debounced search to reduce unnecessary API calls  

### Quick Export Zone

- Located in the header  
- Allows downloading JSON files for a selected language  
- Intended for quick access to locale exports  

### Live Translation Test

- Provides a sandbox environment  
- Lets you test how translation keys render  
- Supports switching between:
  - Web view  
  - Mobile view  

---

## 📖 API Documentation

The backend exposes a REST API secured with Laravel Sanctum.

---

### 🔐 Authentication

#### Login

POST /api/login  

**Request Body:**
{
  "email": "test@example.com",
  "password": "password"
}

**Response:**
{
  "token": "plainTextTokenHere"
}

#### Usage

Include the token in request headers:

Authorization: Bearer {token}  

---

### 📡 Available Endpoints

#### GET /api/languages

Returns a list of active languages.

**Response:**
[
  {
    "id": 1,
    "code": "en",
    "name": "English"
  }
]

---

#### GET /api/translations

Returns paginated translation records.

**Response:**
{
  "data": [
    {
      "id": 1,
      "key": "en",
      "content": "Welcome",
      "tags": "web, mobile"
    }
  ],
  "current_page": 1,
  "last_page": 10,
  "per_page": 10,
  "total": 100
}

---

#### POST /api/translations

Create a new translation record.

**Request Body:**
{
  "translation_language_id": 1,
  "key": "welcome_message",
  "content": "Welcome Message",
  "tags": "web"
}

**Response:**
{
  "id": 101,
  "translation_language_id": 1,
  "key": "welcome_message",
  "tags": "web",
  "content": "Welcome",
  "created_at": "2026-01-01T00:00:00.000000Z",
  "updated_at": "2026-01-01T00:00:00.000000Z"
}

---

#### PUT /api/translations/{id}

Update an existing translation.

**Request Body:**
{
  "value": "Welcome back"
}

**Response:**
{
  "id": 101,
  "translation_language_id": 1,
  "key": "welcome_message",
  "tags": "web",
  "content": "Welcome back",
  "updated_at": "2026-01-01T00:00:00.000000Z"
}

---

#### DELETE /api/translations/{id}

Delete a translation record.

**Response:**
{
  "message": "Translation deleted successfully"
}

---

#### GET /api/translations/export/{code}

Export translations for a specific locale.

**Example:**
/api/translations/export/en

---

## 💡 How to Use the App

### Testing Page (Homepage)

After logging in:

- Switch between available languages  
- Preview translations dynamically  
- Compare rendering between mobile and web layouts  

---

### Management Area

Provides basic administrative functionality:

- Create, update, and delete translation keys  
- Browse translation records  
- Trigger export operations  

---

## 📝 Notes

- The system is structured for handling large datasets but still depends on proper queue configuration for heavy operations  
- Export performance benefits from CDN caching rather than repeated API generation  
- SQLite is used only for unit testing; MySQL is recommended for development and production  

---
