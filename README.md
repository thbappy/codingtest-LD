# URL Shortener API

A professional Laravel API with Sanctum authentication and URL shortening functionality. This project demonstrates best practices for RESTful API development with proper validation, error handling, and secure token-based authentication.

## 🚀 Features

-   **Authentication**

    -   User registration with email validation
    -   User login with hashed password verification
    -   Token-based authentication using Laravel Sanctum
    -   Secure token generation and revocation
    -   Automatic token expiration on logout

-   **URL Shortening**

    -   Create shortened URLs from long URLs
    -   Automatic unique short code generation
    -   Duplicate URL prevention
    -   Redirect to original URL using short code
    -   User-specific URL management
    -   Retrieve all user's shortened URLs
    -   Delete shortened URLs

-   **Error Handling**
    -   Comprehensive validation error responses
    -   Proper HTTP status codes
    -   JSON formatted error messages
    -   Invalid token handling
    -   Not found error handling

## 📋 Installation

1. Clone the repository

```bash
git clone <repository-url>
cd CodingTest-LD-2025
```

2. Install dependencies

```bash
composer install
npm install
```

3. Setup environment

```bash
cp .env.example .env
php artisan key:generate
```

4. Configure database in `.env`

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=root
DB_PASSWORD=
```

5. Run migrations

```bash
php artisan migrate
```

6. Start development server

```bash
php artisan serve
```

## 🔐 Authentication API

### Register User

**Endpoint:** `POST /api/auth/register`

**Request:**

```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Success Response (201):**

```json
{
    "success": true,
    "message": "User registered successfully",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com"
        },
        "token": "1|eJ0eW1oDHWe8nWzz...",
        "token_type": "Bearer"
    }
}
```

**Validation Error (422):**

```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "email": ["The email has already been taken."],
        "password": ["The password must be at least 8 characters."]
    }
}
```

---

### Login User

**Endpoint:** `POST /api/auth/login`

**Request:**

```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

**Success Response (200):**

```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com"
        },
        "token": "2|nX9kL2mP8qR5sT1w...",
        "token_type": "Bearer"
    }
}
```

**Authentication Error (401):**

```json
{
    "success": false,
    "message": "Invalid email or password"
}
```

---

### Logout User

**Endpoint:** `POST /api/auth/logout`

**Headers:**

```
Authorization: Bearer {token}
```

**Response (200):**

```json
{
    "success": true,
    "message": "Logout successful"
}
```

---

## 🔗 URL Shortener API

### Shorten URL

**Endpoint:** `POST /api/urls/shorten`

**Headers:**

```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request:**

```json
{
    "original_url": "https://www.example.com/very-long-url-path"
}
```

**Success Response (201):**

```json
{
    "success": true,
    "message": "URL shortened successfully",
    "data": {
        "id": 1,
        "original_url": "https://www.example.com/very-long-url-path",
        "short_code": "aBcDeF",
        "short_url": "http://127.0.0.1:8000/aBcDeF",
        "created_at": "2025-12-31T10:30:00.000000Z"
    }
}
```

**Duplicate URL Error (409):**

```json
{
    "success": false,
    "message": "This URL has already been shortened",
    "data": {
        "short_code": "aBcDeF",
        "short_url": "http://127.0.0.1:8000/aBcDeF"
    }
}
```

**Validation Error (422):**

```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "original_url": [
            "The original_url field is required.",
            "The original_url must be a valid URL."
        ]
    }
}
```

---

### Get User's URLs

**Endpoint:** `GET /api/urls`

**Headers:**

```
Authorization: Bearer {token}
```

**Response (200):**

```json
{
    "success": true,
    "message": "URLs retrieved successfully",
    "data": [
        {
            "id": 1,
            "original_url": "https://www.example.com/very-long-url-path",
            "short_code": "aBcDeF",
            "short_url": "http://127.0.0.1:8000/aBcDeF",
            "created_at": "2025-12-31T10:30:00.000000Z"
        },
        {
            "id": 2,
            "original_url": "https://www.another-example.com/another-long-url",
            "short_code": "XyZaBc",
            "short_url": "http://127.0.0.1:8000/XyZaBc",
            "created_at": "2025-12-31T10:35:00.000000Z"
        }
    ],
    "total": 2
}
```

---

### Delete Shortened URL

**Endpoint:** `DELETE /api/urls/{id}`

**Headers:**

```
Authorization: Bearer {token}
```

**Success Response (200):**

```json
{
    "success": true,
    "message": "URL deleted successfully"
}
```

**Not Found Error (404):**

```json
{
    "success": false,
    "message": "URL not found"
}
```

---

### Access Shortened URL

**Endpoint:** `GET /{shortCode}`

**Response:** Redirects to original URL (HTTP 302)

**Example:**

```
GET http://127.0.0.1:8000/aBcDeF
→ Redirects to: https://www.example.com/very-long-url-path
```

**Not Found (404):**

```json
{
    "success": false,
    "message": "Short URL not found"
}
```

---

## 🔒 Authentication

All protected endpoints require the token in the Authorization header:

```
Authorization: Bearer {token}
```

Where `{token}` is the API token received from login or register response.

---

## ⚠️ Error Codes

| Code | Message               | Description                   |
| ---- | --------------------- | ----------------------------- |
| 201  | Created               | Resource created successfully |
| 200  | OK                    | Request successful            |
| 400  | Bad Request           | Malformed request             |
| 401  | Unauthorized          | Invalid or missing token      |
| 404  | Not Found             | Resource not found            |
| 409  | Conflict              | Duplicate URL                 |
| 422  | Unprocessable Entity  | Validation failed             |
| 500  | Internal Server Error | Server error                  |

---

## 📝 API Usage Example (cURL)

### Register

```bash
curl -X POST http://127.0.0.1:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

### Login

```bash
curl -X POST http://127.0.0.1:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

### Shorten URL

```bash
curl -X POST http://127.0.0.1:8000/api/urls/shorten \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "original_url": "https://www.example.com/very-long-url"
  }'
```

### Get All URLs

```bash
curl -X GET http://127.0.0.1:8000/api/urls \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Delete URL

```bash
curl -X DELETE http://127.0.0.1:8000/api/urls/1 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## 🛠 Tech Stack

-   **Framework:** Laravel 11
-   **Authentication:** Laravel Sanctum
-   **Database:** MySQL
-   **Server:** PHP 8.2+
-   **API Format:** RESTful JSON

---

## 📂 Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       ├── AuthController.php
│   │       └── ShortenUrlController.php
│   └── Requests/
├── Models/
│   ├── User.php
│   └── ShortenedUrl.php
│
routes/
├── api.php (API routes)
├── web.php (Web routes)
└── auth.php (Auth routes)

database/
├── migrations/
└── seeders/
```

---

## 📄 License

This project is open-sourced software licensed under the MIT license.

