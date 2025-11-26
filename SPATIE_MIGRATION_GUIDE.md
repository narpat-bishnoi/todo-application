# Spatie Laravel Permission Migration Guide

This document explains the migration from simple role enum to Spatie Laravel Permission package.

## Changes Made

### 1. Package Installation
- ✅ Installed `spatie/laravel-permission` package via Composer
- ✅ Package is auto-discovered by Laravel 12

### 2. Database Migrations
- ✅ Created `create_permission_tables.php` migration for Spatie's required tables
- ✅ Created `remove_role_column_from_users_table.php` migration to remove old role enum column

### 3. User Model Updates
- ✅ Added `HasRoles` trait from Spatie
- ✅ Removed `role` from `$fillable` array
- ✅ Updated `isAdmin()` method to use `hasRole('admin')`
- ✅ Added `getRoleName()` helper method for displaying role in views

### 4. Seeders
- ✅ Created `RolePermissionSeeder` to create roles (admin, employee) and permissions
- ✅ Updated `AdminSeeder` to assign admin role using Spatie
- ✅ Updated `DatabaseSeeder` to run `RolePermissionSeeder` before `AdminSeeder`

### 5. Controllers Updates
- ✅ Updated `InvitationController` to assign employee role using `$user->assignRole('employee')`
- ✅ Updated `TodoController` to use `User::role('employee')->get()` instead of `where('role', 'employee')`

### 6. Views Updates
- ✅ Updated `dashboard.blade.php` to use `getRoleName()` method
- ✅ Updated `partials/nav.blade.php` to use `getRoleName()` method

### 7. Middleware
- ✅ No changes needed - `EnsureUserIsAdmin` middleware still works as `isAdmin()` uses Spatie internally

## Setup Instructions

### Step 1: Run Migrations
```bash
php artisan migrate
```

This will:
- Create Spatie permission tables (roles, permissions, model_has_roles, etc.)
- Remove the old `role` enum column from users table

### Step 2: Run Seeders
```bash
php artisan db:seed
```

Or to run specific seeders:
```bash
php artisan db:seed --class=RolePermissionSeeder
php artisan db:seed --class=AdminSeeder
```

This will:
- Create roles: `admin` and `employee`
- Create permissions (if needed)
- Assign all permissions to admin role
- Assign limited permissions to employee role
- Create admin user with admin role assigned

### Step 3: Migrate Existing Users (if any)

If you have existing users in the database, you'll need to assign roles manually:

```php
// In tinker or a migration script
use App\Models\User;
use Spatie\Permission\Models\Role;

$adminRole = Role::where('name', 'admin')->first();
$employeeRole = Role::where('name', 'employee')->first();

// Assign roles to existing users based on their old role column
// Note: This assumes you run this BEFORE dropping the role column
// Or restore it temporarily if needed
```

## Roles and Permissions Structure

### Roles
- **admin**: Full access to all features
- **employee**: Limited access (can view todos assigned to them)

### Permissions Created
- `manage todos`
- `view todos`
- `create todos`
- `edit todos`
- `delete todos`
- `manage invitations`
- `invite employees`

### Permission Assignment
- **Admin role**: Has all permissions
- **Employee role**: Has `view todos` permission only

## API Changes

### Before (Old Enum System)
```php
$user->role; // Returns 'admin' or 'employee'
$user->isAdmin(); // Checks role === 'admin'
User::where('role', 'employee')->get();
```

### After (Spatie System)
```php
$user->roles; // Returns collection of Role models
$user->hasRole('admin'); // Check if user has admin role
$user->isAdmin(); // Still works - uses hasRole('admin') internally
$user->getRoleName(); // Returns first role name for display
$user->assignRole('admin'); // Assign role
User::role('employee')->get(); // Get users with employee role
```

## Testing

1. **Test Admin Access**:
   - Login as admin@example.com / password
   - Should see all admin features (invitations, todos management)
   - Should have admin role displayed

2. **Test Employee Access**:
   - Create an employee via invitation
   - Employee should only see assigned todos
   - Employee should NOT see admin features

3. **Test Role Assignment**:
   - Invitation acceptance should assign employee role
   - Admin user should have admin role

## Rollback (if needed)

If you need to rollback:

```bash
php artisan migrate:rollback --step=2
```

This will:
- Restore the old role column
- Drop Spatie permission tables

Then revert the code changes manually.

## Notes

- The `isAdmin()` method is maintained for backward compatibility
- All existing code using `isAdmin()` will continue to work
- Spatie provides more flexibility for future role/permission management
- You can now easily add more roles or permissions without code changes

