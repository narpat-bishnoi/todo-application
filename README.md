# TODO Application

A complete, production-ready TODO application built with Laravel, featuring role-based access control, employee invitations, and todo management.

## Overview

This application provides a comprehensive TODO management system with two user roles:

- **Administrator**: Can create, edit, delete todos, assign them to employees, and invite new employees
- **Employee**: Can view their assigned todos and update their status

## Features

- User authentication (login/logout)
- Role-based access control using Spatie Laravel Permission (Admin/Employee)
- Employee invitation system via email
- Todo CRUD operations with permissions
- Todo assignment and status tracking
- Email notifications when todos are assigned (processed in background queue)
- Database notifications
- Modern UI with Tailwind CSS and Alpine.js
- Confirmation dialogs for status changes

## Tech Stack

- **Framework**: Laravel 12
- **Database**: MySQL
- **Role Management**: Spatie Laravel Permission
- **Frontend**: Tailwind CSS 4, Alpine.js
- **Templating**: Blade
- **Asset Bundling**: Vite

## Requirements

- PHP 8.2 or higher
- Composer
- Node.js and npm
- MySQL 5.7+ or MariaDB 10.3+
- MySQL server running on `127.0.0.1` with:
  - Database name: `todo_task`
  - Username: `root`
  - Password:

## Setup Instructions

1. **Clone the repository** (if applicable):
   ```bash
   git clone https://github.com/narpat-bishnoi/todo-application
   cd todo-assignment
   ```

2. **Install PHP dependencies**:
   ```bash
   composer install
   ```

3. **Configure environment**:
   ```bash
   cp .env.example .env
   ```
   
   Update the `.env` file with the following database configuration:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=todo_task
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Generate application key**:
   ```bash
   php artisan key:generate
   ```

5. **Create the database**:
   ```sql
   CREATE DATABASE todo_task;
   ```

6. **Run migrations and seeders**:
   ```bash
   php artisan migrate --seed
   ```
   
   This will:
   - Create all necessary tables (users, todos, invitations, notifications)
   - Create Spatie permission tables (roles, permissions, model_has_roles, etc.)
   - Create roles (admin, employee) and permissions
   - Create the default admin user with admin role assigned
   - Remove the old role enum column from users table

7. **Install Node dependencies**:
   ```bash
   npm install
   ```

8. **Build assets**:
   ```bash
   npm run build
   ```

9. **Start the development server**:
   ```bash
   php artisan serve
   ```

   The application will be available at `http://localhost:8000`

## Default Admin Credentials

- **Email**: `admin@example.com`
- **Password**: `password`

## Application Flow

### Authentication

- Administrators and employees log in using the login page
- Only administrators are seeded by default
- Employees join the system via invitation links sent by administrators

### Invitation Flow

1. Administrator navigates to "Invite Employee" from the dashboard
2. Administrator enters the employee's email address
3. System validates that the email is not already registered
4. An invitation email is sent with a unique token link
5. Employee clicks the link and is taken to the acceptance page
6. Employee enters their name and password
7. Account is created and assigned the `employee` role (via Spatie)
8. Employee is automatically logged in and redirected to dashboard

### Todo Management

#### Administrator Capabilities

- View all todos in the system
- Create new todos with title, description, and optional assignment
- Edit todos (title, description, status, assignee)
- Delete todos
- Assign or reassign todos to any employee
- When a todo is assigned or reassigned, the employee receives a notification

#### Employee Capabilities

- View only todos assigned to them
- Update status of their assigned todos to:
  - `in_progress`
  - `completed`
- Confirmation dialog appears before status change is submitted
- Cannot:
  - Change title or description
  - Reassign todos
  - Delete todos
  - Set status back to `open`

### Notifications

- When a todo is assigned or reassigned to an employee, they receive:
  - An email notification with the todo title and a link to view todos
  - A database notification stored in the `notifications` table
- Notifications are sent via both `mail` and `database` channels
- **Email notifications are processed in the background** using Laravel queues for better performance

## Database Structure

### Users Table
- `id`, `name`, `email`, `password`, `timestamps`
- **Note**: Roles are managed via Spatie Laravel Permission (see Roles & Permissions tables below)

### Invitations Table
- `id`, `email` (unique), `token` (unique, 32 chars), `invited_by` (FK), `accepted_at` (nullable), `timestamps`

### Todos Table
- `id`, `title`, `description` (nullable), `status` (enum: 'open', 'in_progress', 'completed'), `created_by` (FK), `assigned_to` (nullable FK), `timestamps`

### Notifications Table
- Standard Laravel notifications table structure

### Roles & Permissions Tables (Spatie Laravel Permission)
- **roles**: `id`, `name`, `guard_name`, `timestamps`
- **permissions**: `id`, `name`, `guard_name`, `timestamps`
- **model_has_roles**: Pivot table linking users to roles
- **model_has_permissions**: Pivot table linking users to permissions
- **role_has_permissions**: Pivot table linking roles to permissions

### Available Roles
- **admin**: Full access to all features
- **employee**: Limited access (can only view assigned todos)

### Available Permissions
- `manage todos` - Full todo management (admin only)
- `view todos` - View todos (both roles)
- `create todos` - Create new todos (admin only)
- `edit todos` - Edit todos (admin only)
- `delete todos` - Delete todos (admin only)
- `manage invitations` - Manage invitations (admin only)
- `invite employees` - Send invitations (admin only)

## Routes

### Public Routes
- `GET /login` - Login form
- `POST /login` - Process login
- `GET /invitations/accept/{token}` - Accept invitation form
- `POST /invitations/accept/{token}` - Process invitation acceptance

### Authenticated Routes
- `GET /dashboard` - Dashboard
- `GET /todos` - List todos
- `GET /todos/create` - Create todo form (admin only)
- `POST /todos` - Store todo (admin only)
- `GET /todos/{id}/edit` - Edit todo form (admin only)
- `PATCH /todos/{id}` - Update todo
- `DELETE /todos/{id}` - Delete todo (admin only)

### Admin Only Routes
- `GET /invitations` - View all invitations with status
- `GET /invitations/create` - Invite employee form
- `POST /invitations` - Send invitation

## Code Quality

- PSR-12 coding style
- Proper validation in controllers
- Middleware for role-based access control using Spatie
- Spatie Laravel Permission for flexible role/permission management
- Clean separation of concerns
- Comprehensive error handling
- Background job processing for emails

## Development

For development with hot-reloading:

```bash
npm run dev
php artisan serve
```

**Note**: The application uses queues for processing email notifications in the background. To process queued jobs:

```bash
php artisan queue:work
```

Or use the included development script which runs everything concurrently:

```bash
composer run dev
```

This will start:
- PHP development server
- Queue worker (for processing emails)
- Log viewer
- Vite dev server (for frontend assets)

### Queue Configuration

The application uses the `database` queue driver by default. Make sure your `.env` has:

```env
QUEUE_CONNECTION=database
```

The `jobs` table is created automatically by Laravel migrations.


## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
