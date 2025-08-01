Here's your updated `README.md` tailored for your current **Keycloak-integrated Laravel Email API**, including Keycloak authentication setup, token usage, Docker instructions, user syncing, and detailed examples.

---

# 📧 Laravel Email API with Keycloak Authentication

A secure Laravel-based API for sending and managing emails, now fully integrated with **Keycloak** for **authentication**, **authorization**, and **user management**.

---

## 🔐 Keycloak Integration

This app uses **Keycloak** instead of Laravel Sanctum. All protected routes now require a valid **JWT** issued by Keycloak. Users are authenticated and synchronized into Laravel automatically.

### 🔄 How Keycloak Works in This Project

* Users are **managed in Keycloak** and no longer created manually in Laravel.
* On each authenticated request, the **JWT is validated**, and the Laravel app:

  * Verifies the token via public keys (`/.well-known/openid-configuration`)
  * Auto-creates or updates the user in the local database
  * Uses Laravel middleware to gate access based on token presence and validity

---

## ⚙️ Local Development Setup

### Prerequisites

* Docker
* PHP 8.1+
* Composer
* Keycloak running locally (default: `http://localhost:8080`)

### 1. Clone the repo

```bash
git clone https://github.com/your-username/email-api.git
cd email-api
```

### 2. Start containers

```bash
docker-compose up -d
```

### 3. Install dependencies

```bash
docker-compose exec app composer install
```

### 4. Migrate & seed database

```bash
docker-compose exec app php artisan migrate
```

### 5. Run the queue (email jobs use Laravel queues)

```bash
docker-compose exec app php artisan queue:work
```

---

## 🛠️ Keycloak Setup for Local Dev

1. Start Keycloak container (use your own realm/client):

   ```bash
   docker run -p 8080:8080 \
     -e KEYCLOAK_ADMIN=admin \
     -e KEYCLOAK_ADMIN_PASSWORD=admin \
     quay.io/keycloak/keycloak:latest \
     start-dev
   ```

2. Create:

   * A **Realm** (e.g. `email-api-realm`)
   * A **Client** (e.g. `email-api-client`)

     * Enable `direct access grants`
     * Set `Access Type: confidential`
   * Add a **user** with email and password

3. Get `client_id`, `client_secret`, and realm name to set in `.env`:

   ```
   KEYCLOAK_REALM=email-api-realm
   KEYCLOAK_CLIENT_ID=email-api-client
   KEYCLOAK_CLIENT_SECRET=...
   KEYCLOAK_BASE_URL=http://localhost:8080
   ```

---

## 🔑 Getting an Access Token

### Using `curl`

```bash
curl -X POST http://localhost:8080/realms/email-api-realm/protocol/openid-connect/token \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "client_id=email-api-client" \
  -d "grant_type=password" \
  -d "username=user@example.com" \
  -d "password=yourpassword" \
  -d "client_secret=YOUR_SECRET"
```

### Using Postman

1. Method: `POST`
2. URL: `http://localhost:8080/realms/email-api-realm/protocol/openid-connect/token`
3. Body: `x-www-form-urlencoded`:

   * `grant_type: password`
   * `client_id: email-api-client`
   * `client_secret: <your-secret>`
   * `username: user@example.com`
   * `password: yourpassword`

---

## 🧬 How User Sync Works

* When a request with a valid Keycloak token hits a protected route:

  * Middleware verifies the token
  * Decoded token data (name, email, sub) is used to create/update the Laravel user
  * No need for separate user registration logic in Laravel

---

## 📡 API Endpoints

### 🔓 Public Endpoints

| Method | Endpoint         | Description             |
| ------ | ---------------- | ----------------------- |
| GET    | `/api/test`      | Health check            |
| GET    | `/api/health`    | Service status          |
| GET    | `/api/auth/info` | Keycloak config details |

### 🔐 Protected Endpoints (Require Token)

| Method | Endpoint             | Description                |
| ------ | -------------------- | -------------------------- |
| GET    | `/api/auth/me`       | Current authenticated user |
| GET    | `/api/auth/validate` | Validate current token     |
| GET    | `/api/user`          | Legacy user endpoint       |

#### 📧 Email

| Method | Endpoint                  | Description         | Body                                               |
| ------ | ------------------------- | ------------------- | -------------------------------------------------- |
| POST   | `/api/send-email`         | Send email          | `{ "to": "...", "subject": "...", "body": "..." }` |
| GET    | `/api/emails`             | List all emails     | -                                                  |
| GET    | `/api/emails/{id}/status` | Check email status  | -                                                  |
| DELETE | `/api/emails/{id}`        | Delete email record | -                                                  |

#### 📄 Email Templates

| Method | Endpoint                    | Description         |
| ------ | --------------------------- | ------------------- |
| GET    | `/api/email-templates`      | List all templates  |
| POST   | `/api/email-templates`      | Create new template |
| GET    | `/api/email-templates/{id}` | Get template by ID  |
| PUT    | `/api/email-templates/{id}` | Update template     |
| DELETE | `/api/email-templates/{id}` | Delete template     |

#### 👥 User Management

| Method | Endpoint          | Description    |
| ------ | ----------------- | -------------- |
| GET    | `/api/users`      | List all users |
| DELETE | `/api/users/{id}` | Delete user    |

---

## 🧪 Example: Send Email with Token

```bash
curl -X POST http://localhost:8000/api/send-email \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "to": "recipient@example.com",
    "subject": "Welcome",
    "body": "Hello from Keycloak-powered Email API!"
  }'
```

---

## ⚠️ Common Errors

### 401 Unauthorized

```json
{ "message": "Unauthenticated." }
```

### 403 Forbidden

```json
{ "message": "This action is unauthorized." }
```

### 422 Validation

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field is required."]
  }
}
```

---

## 🧾 Key Notes

* All timestamps in UTC
* Queue must be running for email delivery
* Tokens expire; refresh as needed
* Users sync automatically from Keycloak
* Supports `{{variable}}` placeholders in templates

---

## ✅ Summary of Changes from Sanctum to Keycloak

| Feature           | Sanctum                   | Keycloak (Now)              |
| ----------------- | ------------------------- | --------------------------- |
| Auth Mechanism    | Laravel Token             | OpenID Connect (JWT)        |
| User Registration | Handled by Laravel        | Managed via Keycloak UI     |
| Token Validation  | Laravel guards/middleware | Public key + JWT Middleware |
| Syncing           | Manual                    | Automatic on request        |
| Admin Panel       | Laravel                   | Keycloak Admin Console      |

