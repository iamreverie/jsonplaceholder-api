# JSONPlaceholder API

A **Laravel 13** REST API that fetches data from [JSONPlaceholder](https://jsonplaceholder.typicode.com), stores it in a MySQL database, and exposes it through a versioned, authenticated RESTful API secured with **Laravel Sanctum** token authentication.

---

## Stack

| Layer | Technology |
|---|---|
| Framework | Laravel 13 (PHP 8.4) |
| Database | MySQL 8.0 |
| Auth | Laravel Sanctum (Bearer Token) |
| Runtime | Docker (PHP-FPM + Nginx) |
| Environment | WSL Ubuntu |

---

## Requirements

- Docker & Docker Compose
- Git
- PHP 8.4 + Composer (host only — for initial scaffolding; runtime is fully inside Docker)

---

## Setup

### 1. Clone the repository

```bash
git clone <repository-url>
cd jsonplaceholder-api
```

### 2. Configure environment

```bash
cp .env.example .env
```

The default `.env` is pre-configured for the Docker setup. Edit if you need to change credentials or ports.

```dotenv
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=jsonplaceholder_db
DB_USERNAME=jp_user
DB_PASSWORD=secret
```

> MySQL is exposed on host port `3307` to avoid conflicts with any local MySQL instance.

### 3. Build and start containers

```bash
docker compose up -d --build
```

This starts three services:

| Container | Role | Port |
|---|---|---|
| `jp_app` | PHP 8.4-FPM (Laravel) | Internal |
| `jp_nginx` | Nginx reverse proxy | `8080` |
| `jp_db` | MySQL 8.0 | `3307` (host) |

> The `jp_app` container runs an entrypoint script that automatically fixes `storage/` and `bootstrap/cache/` directory permissions on every startup — no manual `chown` required after cloning.

### 4. Generate application key

```bash
docker compose exec app php artisan key:generate
```

### 5. Run migrations and seed the admin user

```bash
docker compose exec app php artisan migrate --seed
```

This creates all database tables with proper foreign key constraints and seeds the default API user.

### 6. Fetch and store JSONPlaceholder data

```bash
docker compose exec app php artisan app:fetch-jsonplaceholder
```

This concurrently fetches all six JSONPlaceholder resources and persists them to the database via bulk upsert operations.

The API is now available at: **http://localhost:8080**

---

## Authentication

All API endpoints (except login) require a Bearer token. Tokens expire after **24 hours** and must be renewed via login.

### Obtain a token

The login endpoint accepts an optional `device_name` field to identify the token source. If omitted, the `User-Agent` header is used.

```http
POST http://localhost:8080/api/v1/login
Content-Type: application/json

{
    "email": "your@email.com",
    "password": "your-password",
    "device_name": "Postman - Local"
}
```

**Response:**
```json
{
    "token": "1|your-token-here",
    "token_type": "Bearer"
}
```

> The login endpoint is rate limited to **5 attempts per minute** per email and IP address. Exceeding this returns `HTTP 429`.

### Use the token

Include it in the `Authorization` header on every protected request:

```
Authorization: Bearer 1|your-token-here
```

### Revoke the token

```http
POST http://localhost:8080/api/v1/logout
Authorization: Bearer 1|your-token-here
```

---

## API Endpoints

All endpoints are prefixed with `/api/v1`. Protected endpoints require `Authorization: Bearer {token}`. All responses are JSON — including errors.

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| `POST` | `/v1/login` | No | Obtain Bearer token |
| `POST` | `/v1/logout` | Yes | Revoke current token |
| `GET` | `/v1/users` | Yes | List users (paginated) |
| `GET` | `/v1/users/{id}` | Yes | Get single user |
| `GET` | `/v1/posts` | Yes | List posts (paginated) |
| `GET` | `/v1/posts/{id}` | Yes | Get single post |
| `GET` | `/v1/comments` | Yes | List comments (paginated) |
| `GET` | `/v1/comments/{id}` | Yes | Get single comment |
| `GET` | `/v1/albums` | Yes | List albums (paginated) |
| `GET` | `/v1/albums/{id}` | Yes | Get single album |
| `GET` | `/v1/photos` | Yes | List photos (paginated) |
| `GET` | `/v1/photos/{id}` | Yes | Get single photo |
| `GET` | `/v1/todos` | Yes | List todos (paginated) |
| `GET` | `/v1/todos/{id}` | Yes | Get single todo |

### Pagination

All list endpoints support pagination query parameters:

```
GET /api/v1/posts?page=2&per_page=25
```

| Parameter | Default | Max | Description |
|---|---|---|---|
| `page` | `1` | — | Page number |
| `per_page` | `15` | `100` | Records per page |

> Requests for more than 100 records per page are silently capped at 100.

### Error responses

All errors return JSON regardless of the `Accept` header:

| Status | Meaning |
|---|---|
| `401` | Missing or invalid token |
| `404` | Record not found |
| `422` | Validation failed |
| `429` | Rate limit exceeded |
| `500` | Server error |

---

## Database Schema

All JSONPlaceholder data is stored in `jp_`-prefixed tables to avoid collision with Laravel's own `users` table.

```
jp_users
├── id (PK, unsignedInteger)
├── name, username (unique), email (unique)
├── phone, website
├── address (JSON), company (JSON)
└── timestamps

jp_posts
├── id (PK)
├── user_id (FK → jp_users.id, cascade delete)
├── title, body
└── timestamps

jp_comments
├── id (PK)
├── post_id (FK → jp_posts.id, cascade delete)
├── name, email, body
└── timestamps

jp_albums
├── id (PK)
├── user_id (FK → jp_users.id, cascade delete)
├── title
└── timestamps

jp_photos
├── id (PK)
├── album_id (FK → jp_albums.id, cascade delete)
├── title, url, thumbnail_url
└── timestamps

jp_todos
├── id (PK)
├── user_id (FK → jp_users.id, cascade delete)
├── title, completed (boolean)
└── timestamps
```

---

## Artisan Commands

### Fetch JSONPlaceholder data

```bash
# Upsert all data (safe to re-run)
docker compose exec app php artisan app:fetch-jsonplaceholder

# Truncate all jp_* tables first, then re-fetch
docker compose exec app php artisan app:fetch-jsonplaceholder --fresh
```

The command fetches all 6 endpoints **concurrently** using `Http::pool()` and persists records using **bulk upsert** operations. Photos (5,000 records) are chunked in batches of 500 to manage memory.

---

## Environment Variables

Key variables to review when deploying:

| Variable | Default | Description |
|---|---|---|
| `APP_DEBUG` | `false` | Never set to `true` in production |
| `LOG_LEVEL` | `error` | Use `error` or `warning` in production |
| `SANCTUM_TOKEN_EXPIRATION` | `1440` | Token lifetime in minutes (24 hours) |
| `CORS_ALLOWED_ORIGINS` | `http://localhost:8080` | Comma-separated list of allowed origins |

---

## Default Credentials

Seeded by `database/seeders/ApiUserSeeder.php` when running `php artisan migrate --seed`.

| Field | Value |
|---|---|
| Email | `g.deleon@jsonplaceholder.test` |
| Password | `applicant@password` |

---

## Docker Reference

```bash
# Start all containers (with rebuild)
docker compose up -d --build

# Stop all containers
docker compose down

# Stop and remove all data volumes (wipes database)
docker compose down -v

# View container status
docker compose ps

# Tail application logs
docker compose logs -f app

# Open a shell in the app container
docker compose exec app bash

# Run any Artisan command
docker compose exec app php artisan <command>
```