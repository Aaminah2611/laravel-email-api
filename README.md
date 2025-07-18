

# Laravel Email System API

## Introduction

This project is an Email System API built with Laravel to provide a practical, real-world backend service for sending, managing, and tracking emails programmatically. It is designed as a learning project for interns and developers to gain hands-on experience with API development, email integration, queues, and Laravel best practices.

## Overview

The Email System API serves as a centralized backend to send various email types — transactional, marketing, notifications — through a simple API interface. This allows internal services or applications to dispatch emails without managing SMTP details directly.

Key functionalities include sending simple and templated emails, managing email templates, and tracking email delivery status. Email sending is handled asynchronously through Laravel’s queue system to improve API responsiveness.

## Technologies Used

* Backend Framework: Laravel (PHP)
* Database: MySQL (via Docker container)
* Email Driver: SMTP (configured with Mailtrap for development)
* Queue Driver: Database (Laravel queue system)
* API Authentication: Laravel Sanctum (token-based)
* Validation: Laravel built-in request validation
* Testing: PHPUnit
* Containerization: Docker and Docker Compose

## Docker Deployment

This API is fully containerized using Docker for easy local development and testing. The main containers are:

* **app**: Laravel PHP application
* **db**: MySQL 8.0 database
* **webserver**: Nginx serving the Laravel app on port 8000

### Setup and Running

1. Clone the repository.

2. Ensure Docker and Docker Compose are installed.

3. Run the following to build and start containers:

   ```bash
   docker-compose up -d --build
   ```

4. Run migrations and seed the database:

   ```bash
   docker-compose exec app php artisan migrate:fresh --seed
   ```

5. **Important:** Start the Laravel queue worker to process asynchronous jobs like sending emails:

   ```bash
   docker-compose exec app php artisan queue:work
   ```

   Keep this running during development and testing to allow queued emails to be sent.

6. Access the API at: `http://localhost:8000`

## Environment Configuration

The `.env` file is set up to connect the app container to the `db` MySQL container. Database credentials and ports are configured accordingly. Mailtrap SMTP is used for email testing with the relevant credentials.

## API Routes and Usage

Below are the main tested API endpoints. All routes are prefixed with `/api/`.

| HTTP Method | Endpoint                    | Description                     | Example Request Body / Notes                             |
| ----------- | --------------------------- | ------------------------------- | -------------------------------------------------------- |
| POST        | `/api/login`                | User login                      | JSON with email and password                             |
| POST        | `/api/logout`               | User logout                     | Token authentication required                            |
| POST        | `/api/send-email`           | Send an email                   | JSON: `{ "to": "...", "subject": "...", "body": "..." }` |
| GET         | `/api/email-templates`      | List all email templates        | Pagination supported                                     |
| POST        | `/api/email-templates`      | Create a new email template     | JSON with template fields                                |
| GET         | `/api/email-templates/{id}` | Retrieve a specific template    |                                                          |
| PUT/PATCH   | `/api/email-templates/{id}` | Update a specific template      | JSON with updated fields                                 |
| DELETE      | `/api/email-templates/{id}` | Delete a template               |                                                          |
| GET         | `/api/emails`               | List sent emails                | Pagination supported                                     |
| GET         | `/api/emails/{id}/status`   | Check status of a sent email    |                                                          |
| GET         | `/api/users`                | List users                      | Pagination supported                                     |
| GET         | `/api/test`                 | Test route for API connectivity | Simple test endpoint                                     |

## What This Application Does Successfully

* Fully containerized Laravel backend deployed via Docker Compose.
* Connects app, database, and webserver containers seamlessly.
* Supports user authentication via Laravel Sanctum.
* Allows CRUD operations on email templates.
* Sends emails asynchronously via Laravel queues and SMTP (Mailtrap).
* Tracks email sending status (pending, sent, failed).
* Provides RESTful API endpoints tested and verified with Postman.
* Utilizes database queue driver for reliable background processing.
* Implements robust validation for request payloads.

## Important Code Files and Structure

* `app/Http/Controllers/EmailController.php` — Email sending, status, and list handling
* `app/Http/Controllers/EmailTemplateController.php` — CRUD operations for email templates
* `routes/api.php` — All API routes definitions
* `config/queue.php` — Queue driver configuration (using database)
* `config/mail.php` — SMTP settings for Mailtrap integration
* `docker-compose.yml` — Docker container definitions for app, db, webserver
* `Dockerfile` — Defines the PHP/Laravel app container environment
* `.env` — Environment variables for DB, mail, and app configuration
* `database/migrations/` — Database schema migrations including users, emails, templates
* `database/seeders/` — Seed data for initial testing
* `app/Jobs/SendEmailJob.php` — Laravel queued job for sending emails asynchronously

## Pre-requisites

* Docker and Docker Compose installed on your local machine
* Basic knowledge of Laravel, Docker, and REST API usage
* Postman or similar API client for testing endpoints

## How to Test the API

* Use Postman to test all endpoints at `http://localhost:8000/api/`
* Ensure to pass required headers like `Authorization: Bearer {token}` where needed
* For sending email, provide JSON body with `"to"`, `"subject"`, and `"body"` fields
* Monitor the queue worker logs to verify email jobs are processed
* Check Mailtrap inbox for received emails during development

## Important Notes

* **Queue Worker:**
  To enable asynchronous email sending, the Laravel queue worker **must** be running:

  ```bash
  docker-compose exec app php artisan queue:work
  ```

  Without this, emails will not be dispatched and will remain in the pending state.

* **Environment Variables:**
  Ensure `.env` is properly configured with database and mail credentials. Mailtrap is set for development.

* **Ports:**
  The Laravel app is served on port 8000 via the `webserver` container. Use `http://localhost:8000` for API requests.

## Extending the Project

* Switch queue driver to Redis for improved performance
* Integrate production SMTP providers (SendGrid, Mailgun)
* Implement advanced email template engines (Blade/Markdown)
* Add detailed logging and error handling

---


