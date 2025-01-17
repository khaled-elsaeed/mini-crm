# Mini CRM Documentation

## Overview
Mini CRM is a Customer Relationship Management system that helps manage customer-employee relationships, track interactions, and handle customer assignments.

## System Requirements
- PHP 8.0+
- MySQL 5.7+
- Laravel 10.x
- Composer


## Installation
```bash
# Clone the repository
git clone [repository-url]

# Install PHP dependencies
composer install

# Install NPM dependencies
npm install

# Configure environment
cp .env.example .env
php artisan key:generate

# Run migrations and seeders
php artisan migrate --seed

# Start the development server
php artisan serve
```

## Core Features

### User Management
- **Roles**: Admin, Employee, Customer
- **Permissions**: 
  - add-customer
  - edit-customer
  - delete-customer
  - assign-customer
  - view-customer

### Customer Management
- Add/Edit/Delete customers
- Search customers by name/email
- Assign customers to employees
- Track customer interactions

### Employee Features
- Manage assigned customers
- Log customer interactions (calls, visits, follow-ups)
- View interaction history

### Admin Features
- Full system access
- User role management
- Customer-employee assignment control

## Database Structure

### Users Table
- id (primary key)
- name
- email
- password
- created_at
- updated_at

### Customer_Employee Table (Pivot)
- customer_id
- employee_id
- status
- created_at
- updated_at

### Customer_Actions Table
- id
- customer_id
- user_id
- action_type
- description
- created_at
- updated_at


