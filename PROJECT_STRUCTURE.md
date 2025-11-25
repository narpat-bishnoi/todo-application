# TODO Application - Complete Project Structure

## Overview
A production-ready Laravel 12 TODO management application with role-based access control (Admin/Employee), invitation system, and todo assignment features.

## Technology Stack
- **Backend**: Laravel 12, PHP 8.2+
- **Database**: MySQL
- **Frontend**: Tailwind CSS 4, Alpine.js
- **Templating**: Blade
- **Asset Bundling**: Vite 7
- **Package Manager**: Composer (PHP), npm (Node.js)

---

## Project Directory Structure

### Root Level
```
todo-assignment/
├── app/                    # Application core code
├── bootstrap/              # Application bootstrap files
├── config/                 # Configuration files
├── database/               # Migrations, seeders, factories
├── public/                 # Web server entry point
├── resources/              # Views, CSS, JS assets
├── routes/                 # Route definitions
├── storage/                # Logs, cache, sessions
├── tests/                  # Test files
├── vendor/                 # Composer dependencies
├── node_modules/           # npm dependencies
├── artisan                 # Laravel CLI tool
├── composer.json           # PHP dependencies
├── package.json            # Node.js dependencies
├── vite.config.js          # Vite configuration
├── phpunit.xml             # PHPUnit configuration
└── README.md               # Project documentation
```

---

## Application Structure (`app/`)

### Controllers (`app/Http/Controllers/`)
1. **Auth/LoginController.php**
   - `showLoginForm()` - Display login form
   - `login()` - Process login request
   - `logout()` - Handle user logout

2. **DashboardController.php**
   - `index()` - Display dashboard with role-based options

3. **TodoController.php**
   - `index()` - List todos (filtered by role)
   - `create()` - Show create form (admin only)
   - `store()` - Create new todo (admin only)
   - `show()` - Display single todo
   - `edit()` - Show edit form (admin only)
   - `update()` - Update todo (admin/employee with restrictions)
   - `destroy()` - Delete todo (admin only)

4. **InvitationController.php**
   - `create()` - Show invitation form (admin only)
   - `store()` - Send invitation email (admin only)
   - `accept()` - Show acceptance form (public)
   - `processAccept()` - Create employee account (public)

### Models (`app/Models/`)

1. **User.php**
   - Fields: `name`, `email`, `password`, `role`
   - Methods:
     - `isAdmin()` - Check if user is administrator
     - `sentInvitations()` - Relationship to invitations sent
     - `createdTodos()` - Relationship to todos created
     - `assignedTodos()` - Relationship to todos assigned

2. **Todo.php**
   - Constants: `STATUS_OPEN`, `STATUS_IN_PROGRESS`, `STATUS_COMPLETED`
   - Fields: `title`, `description`, `status`, `created_by`, `assigned_to`
   - Methods:
     - `creator()` - Relationship to user who created
     - `assignee()` - Relationship to assigned user
     - `isAssignedTo()` - Check if assigned to specific user

3. **Invitation.php**
   - Fields: `email`, `token`, `invited_by`, `accepted_at`
   - Methods:
     - `generateToken()` - Generate unique 32-character token
     - `isAccepted()` - Check if invitation accepted
     - `inviter()` - Relationship to user who sent invitation

### Middleware (`app/Http/Middleware/`)

1. **EnsureUserIsAdmin.php**
   - Checks if authenticated user has admin role
   - Returns 403 if unauthorized
   - Registered as `admin` middleware alias in `bootstrap/app.php`

### Mail (`app/Mail/`)

1. **InvitationMail.php**
   - Mailable for sending invitation emails
   - Uses view: `emails.invitation`
   - Subject: "You're Invited to Join TODO Application"

### Notifications (`app/Notifications/`)

1. **TodoAssignedNotification.php**
   - Notification sent when todo is assigned/reassigned
   - Channels: `mail`, `database`
   - Contains: todo_id, title

### Providers (`app/Providers/`)

1. **AppServiceProvider.php**
   - Sets default string length to 191 for MySQL compatibility

---

## Database Structure (`database/`)

### Migrations

1. **0001_01_01_000000_create_users_table.php**
   - Creates `users` table with basic authentication fields
   - Creates `password_reset_tokens` table
   - Creates `sessions` table

2. **2025_11_25_173633_add_role_to_users_table.php**
   - Adds `role` enum column: `admin`, `employee`
   - Default: `employee`

3. **2025_11_25_173641_create_invitations_table.php**
   - Fields: `id`, `email` (unique), `token` (unique, 32 chars), `invited_by` (FK), `accepted_at` (nullable), `timestamps`
   - Foreign key to `users` table

4. **2025_11_25_173649_create_todos_table.php**
   - Fields: `id`, `title`, `description` (nullable), `status` (enum: open/in_progress/completed), `created_by` (FK), `assigned_to` (nullable FK), `timestamps`
   - Foreign keys to `users` table

5. **2025_11_25_173658_create_notifications_table.php**
   - Standard Laravel notifications table
   - Fields: `id` (uuid), `type`, `notifiable` (polymorphic), `data`, `read_at`, `timestamps`

### Seeders

1. **DatabaseSeeder.php**
   - Calls `AdminSeeder`

2. **AdminSeeder.php**
   - Creates default admin user:
     - Email: `admin@example.com`
     - Password: `password`
     - Name: `Administrator`
     - Role: `admin`

### Factories

1. **UserFactory.php** - User model factory for testing

---

## Routes (`routes/web.php`)

### Public Routes
- `GET /login` - Login form
- `POST /login` - Process login
- `POST /logout` - Logout user
- `GET /invitations/accept/{token}` - Accept invitation form
- `POST /invitations/accept/{token}` - Process invitation acceptance

### Authenticated Routes (middleware: `auth`)
- `GET /` - Redirect to dashboard
- `GET /dashboard` - Dashboard
- `GET /todos` - List todos
- `GET /todos/create` - Create todo form (admin check in controller)
- `POST /todos` - Store todo (admin check in controller)
- `GET /todos/{id}` - Show todo
- `GET /todos/{id}/edit` - Edit todo form (admin check in controller)
- `PATCH /todos/{id}` - Update todo (role-based logic in controller)
- `DELETE /todos/{id}` - Delete todo (admin check in controller)

### Admin Only Routes (middleware: `auth`, `admin`)
- `GET /invitations/create` - Invite employee form
- `POST /invitations` - Send invitation

---

## Views (`resources/views/`)

### Layouts
- **layouts/app.blade.php** - Main application layout
  - Includes navigation
  - Includes flash messages
  - Loads Vite assets (CSS/JS)

### Partials
- **partials/nav.blade.php** - Navigation bar with role-based menu items
- **partials/flash.blade.php** - Flash message display

### Auth
- **auth/login.blade.php** - Login form

### Dashboard
- **dashboard.blade.php** - Dashboard with role-based action cards

### Todos
- **todos/index.blade.php** - Todo listing with role-based columns/actions
- **todos/create.blade.php** - Create todo form (admin only)
- **todos/edit.blade.php** - Edit todo form (admin only)
- **todos/show.blade.php** - View single todo

### Invitations
- **invitations/create.blade.php** - Invitation form (admin only)
- **invitations/accept.blade.php** - Invitation acceptance form (public)

### Emails
- **emails/invitation.blade.php** - Invitation email template

### Other
- **welcome.blade.php** - Welcome page

---

## Frontend Assets (`resources/`)

### CSS (`resources/css/`)
- **app.css** - Tailwind CSS imports with custom theme

### JavaScript (`resources/js/`)
- **app.js** - Alpine.js initialization
- **bootstrap.js** - Axios and CSRF token setup

### Vite Configuration (`vite.config.js`)
- Configures Laravel Vite plugin
- Configures Tailwind CSS Vite plugin
- Input files: `resources/css/app.css`, `resources/js/app.js`

---

## Configuration Files

### Application (`config/app.php`)
- Application name, environment, debugging
- Service providers

### Authentication (`config/auth.php`)
- Authentication guards
- Password reset configuration

### Database (`config/database.php`)
- Database connections (MySQL configured)

### Mail (`config/mail.php`)
- Mail driver configuration (for sending invitations)

### Session (`config/session.php`)
- Session driver and lifetime

---

## Key Features & Functionality

### Authentication & Authorization
- User authentication with email/password
- Role-based access control (Admin/Employee)
- Custom `admin` middleware for protecting admin routes
- Session-based authentication

### Invitation System
- Admins can invite employees via email
- Unique token-based invitation links
- Email validation (no duplicate users/invitations)
- Automatic account creation upon acceptance
- Auto-login after acceptance

### Todo Management
- **Admin Capabilities**:
  - Create todos with title, description, assignment
  - Edit todos (title, description, status, assignee)
  - Delete todos
  - View all todos
  - Assign/reassign todos to employees

- **Employee Capabilities**:
  - View only assigned todos
  - Update status (in_progress, completed)
  - Cannot change title, description, or reassign
  - Cannot set status back to open

### Notifications
- Email notifications when todos are assigned
- Database notifications stored for in-app display
- Notifications sent on assignment/reassignment

### UI/UX Features
- Modern Tailwind CSS styling
- Alpine.js for interactive components (delete modals)
- Responsive design
- Flash messages for user feedback
- Pagination for todo lists
- Role-based navigation menus

---

## Security Features

1. **CSRF Protection** - All forms include CSRF tokens
2. **Authorization Checks** - Role-based checks in controllers and middleware
3. **Input Validation** - Comprehensive validation in all controllers
4. **Password Hashing** - Laravel's bcrypt hashing
5. **SQL Injection Protection** - Eloquent ORM with parameterized queries
6. **XSS Protection** - Blade template escaping

---

## Environment Setup

### Required Configuration (`.env`)
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=todo_task
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
# ... mail configuration
```

### Default Admin Credentials
- Email: `admin@example.com`
- Password: `password`

---

## Development Workflow

1. **Install Dependencies**:
   ```bash
   composer install
   npm install
   ```

2. **Setup Environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Database Setup**:
   ```bash
   php artisan migrate --seed
   ```

4. **Build Assets**:
   ```bash
   npm run build        # Production
   npm run dev          # Development with hot-reload
   ```

5. **Start Server**:
   ```bash
   php artisan serve
   ```

---

## File Count Summary

- **Controllers**: 5 files
- **Models**: 3 files
- **Middleware**: 1 file
- **Mail**: 1 file
- **Notifications**: 1 file
- **Providers**: 1 file
- **Migrations**: 7 files
- **Seeders**: 2 files
- **Views**: ~12 files
- **Routes**: 1 file (web.php)

---

## Code Quality Standards

- PSR-12 coding style
- Proper validation in all controllers
- Clean separation of concerns (MVC pattern)
- Eloquent relationships properly defined
- Comprehensive error handling
- Type hints and return types where applicable

---

## Extension Points

The application is well-structured for future enhancements:
- Add more user roles
- Implement todo comments
- Add file attachments to todos
- Implement todo priorities
- Add due dates and reminders
- Implement todo categories/tags
- Add activity logging
- Implement search and filtering
- Add email notifications preferences
- Implement todo templates

