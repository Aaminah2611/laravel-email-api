# Email API with Keycloak Authentication

## Overview
This Email API uses Keycloak for authentication instead of Laravel Sanctum. All protected routes require a valid JWT token from Keycloak.

## Authentication Flow

### 1. Get Token from Keycloak
```bash
curl -X POST \
  http://localhost:8080/realms/your-realm/protocol/openid-connect/token \
  -H 'Content-Type: application/x-www-form-urlencoded' \
  -d 'grant_type=password&client_id=your-client&username=user@example.com&password=yourpassword'
```

### 2. Use Token in API Calls
```bash
curl -H "Authorization: Bearer YOUR_JWT_TOKEN" http://localhost:8000/api/emails
```

## API Endpoints

### Public Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/test` | API health check |
| GET | `/api/health` | Service status |
| GET | `/api/auth/info` | Keycloak configuration info |

### Protected Endpoints (Require Authentication)

#### Authentication
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/auth/me` | Get current user info |
| GET | `/api/auth/validate` | Validate current token |
| GET | `/api/user` | Legacy user endpoint |

#### Email Management
| Method | Endpoint | Description | Body |
|--------|----------|-------------|------|
| POST | `/api/send-email` | Send an email | `{"to": "user@example.com", "subject": "Test", "body": "Hello"}` |
| GET | `/api/emails` | List sent emails | - |
| GET | `/api/emails/{id}/status` | Check email status | - |
| DELETE | `/api/emails/{id}` | Delete email record | - |

#### Email Templates
| Method | Endpoint | Description | Body |
|--------|----------|-------------|------|
| GET | `/api/email-templates` | List all templates | - |
| POST | `/api/email-templates` | Create new template | `{"name": "Welcome", "subject": "Welcome!", "body": "Hello {{name}}"}` |
| GET | `/api/email-templates/{id}` | Get specific template | - |
| PUT | `/api/email-templates/{id}` | Update template | `{"name": "Updated Name"}` |
| DELETE | `/api/email-templates/{id}` | Delete template | - |

#### User Management
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/users` | List all users |
| DELETE | `/api/users/{id}` | Delete user |

## Example Usage

### Send a Simple Email
```bash
curl -X POST http://localhost:8000/api/send-email \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "to": "recipient@example.com",
    "subject": "Test Email",
    "body": "This is a test email sent via the API"
  }'
```

### Create Email Template
```bash
curl -X POST http://localhost:8000/api/email-templates \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Welcome Email",
    "subject": "Welcome to our platform!",
    "body": "Hello {{name}}, welcome to our platform!"
  }'
```

### List Sent Emails
```bash
curl -H "Authorization: Bearer YOUR_TOKEN" http://localhost:8000/api/emails
```

## Error Responses

### 401 Unauthorized
```json
{
  "message": "Unauthenticated."
}
```

### 403 Forbidden
```json
{
  "message": "This action is unauthorized."
}
```

### 422 Validation Error
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field is required."]
  }
}
```

## Development Setup

1. Start Docker containers: `docker-compose up -d`
2. Run migrations: `docker-compose exec app php artisan migrate`
3. Configure Keycloak realm and client
4. Update `.env` with Keycloak settings
5. Test with Postman or curl

## Notes

- All timestamps are in UTC
- Email sending is queued for better performance
- Templates support basic variable substitution with `{{variable}}` syntax
- Users are automatically created on first Keycloak login