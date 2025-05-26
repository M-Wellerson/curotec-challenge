# Task Management Application

## Overview

Task management application built with Laravel 10 and Vue.js 3. This application allows users to create, filter, and
manage
tasks with features including task categories, priorities, and status tracking.

## Tech Stack

- **Backend**: Laravel (PHP framework)
- **Frontend**: Vue.js with Inertia.js
- **Styling**: Tailwind CSS
- **Database**: PostgreSQL
- **Package Managers**: Composer (PHP), NPM (JavaScript)

## Architecture

```
├── app/                # PHP application code
│   ├── Http/           # Controllers, Requests, Resources
│   └── Models/         # Database models
├── resources/          # Frontend assets
│   ├── js/             # Vue components and stores
│   └── views/          # Blade templates
└── routes/             # Application routes
```

## Setup & Run Commands

### Prerequisites

- PHP 8.3+
- Composer
- Node.js & NPM
- PostgreSQL

### Installation

```bash
# Start the application with Docker Compose
docker-compose up -d

# Run migrations inside the container
docker-compose exec app php artisan migrate

# Seed the database (if available)
docker-compose exec app php artisan db:seed
```

### Running the Application

```bash
# The application will be available at http://localhost:8000
docker-compose up -d

npm run dev
```

## Environment Variables

Key environment variables needed in your `.env` file:

| Variable        | Example   |
|-----------------|-----------|
| `DB_CONNECTION` | `pgsql`   |
| `DB_HOST`       | `db`      |
| `DB_PORT`       | `5432`    |
| `DB_DATABASE`   | `laravel` |
| `DB_USERNAME`   | `laravel` |
| `DB_PASSWORD`   | `secret`  |

## Database Schema (ERD)

### Core Tables

- **tasks**: Stores task information
    - id, title, owner_id, description, priority_id, status_id, due_date, created_at, updated_at
- **categories**: Stores task categories
    - id, name, user_id, created_at, updated_at
- **task_priorities**: Task priority levels
    - id, name, level, created_at, updated_at
- **task_statuses**: Possible task statuses
    - id, name, created_at, updated_at
- **category_task**: Pivot table for task-category relationship
    - category_id, task_id
- **task_user**: Pivot table for task assignees
    - task_id, user_id

## Core Modules/Services

### Task Management

- Task creation, filtering, and deletion
- Category management and filtering
- Priority and status assignment

## API Endpoints

### Tasks

- `GET /dashboard` - List all tasks with pagination
- `POST /tasks` - Create a new task
- `DELETE /tasks/{task}` - Delete a task

## Tests & Coverage

Run tests with:

```bash
php artisan test tests/Feature/Http/Controllers/TaskControllerTest.php
```

