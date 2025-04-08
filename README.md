## TODO: Add a WalkMe-like User Guidance Feature to a WF

## Objective

Create a user guidance system within a Laravel Filament project to help users navigate and understand features through interactive, step-by-step tours or tooltips, similar to WalkMe.

---

## Step 1: Define Requirements

-   **Purpose**: Guide users through key features (e.g., creating a resource, navigating the dashboard, using forms).
-   **Features**:
    -   Step-by-step tours with popups/tooltips.
    -   Ability to highlight UI elements.
    -   Dismissible and skippable tours.
    -   Persistent state (e.g., mark tour as completed per user).
-   **Scope**: Focus on the Filament admin panel (e.g., `/admin` route).
-   **Target Audience**: New users or users exploring new features.

---

## Step 2: Technology Stack

-   **Backend**: Laravel (for managing tour data and user progress).
-   **Frontend**:
    -   Filament’s Livewire for reactivity.
    -   JavaScript library like [Intro.js](https://introjs.com/) or [Shepherd.js](https://shepherdjs.dev/) for the tour UI.
    -   Tailwind CSS (already used by Filament) for styling.
-   **Database**: Store tour steps and user completion status.

---

## Step 3: Setup and Installation

1. **Install Laravel and Filament (if not already done)**:

    - Ensure you have a working Laravel project with Filament installed (`composer require filament/filament:^3.2-stable -W`).
    - Run `php artisan filament:install --panels` to set up the admin panel.

2. **Install a JavaScript Tour Library**:

    - For this plan, we’ll use **Shepherd.js** due to its flexibility and modern design.
    - Install via npm:
        ```bash
        npm install shepherd.js
        ```
    - Include it in your Filament assets (e.g., `resources/js/app.js`).

3. **Configure Tailwind CSS**:
    - Filament already uses Tailwind, so ensure your `tailwind.config.js` includes Shepherd.js styles if needed:
        ```javascript
        module.exports = {
            content: [
                "./resources/**/*.blade.php",
                "./vendor/filament/**/*.blade.php",
                "./node_modules/shepherd.js/dist/**/*.js",
            ],
        };
        ```

---

## Step 4: Database Design

-   **Tours Table**: Store tour metadata.

    ```php
    Schema::create('tours', function (Blueprint $table) {
        $table->id();
        $table->string('name')->unique(); // e.g., "dashboard_tour"
        $table->json('steps'); // Store steps as JSON
        $table->timestamps();
    });
    ```

-   **User Tour Progress Table**: Track user progress.
    ```php
    Schema::create('user_tour_progress', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->foreignId('tour_id')->constrained()->cascadeOnDelete();
        $table->boolean('completed')->default(false);
        $table->timestamps();
    });
    ```

# Larament

[![Pint](https://github.com/codewithdennis/larament/actions/workflows/pint.yml/badge.svg)](https://packagist.org/packages/codewithdennis/larament)
[![PEST](https://github.com/codewithdennis/larament/actions/workflows/pest.yml/badge.svg)](https://packagist.org/packages/codewithdennis/larament)
[![PHPStan](https://github.com/CodeWithDennis/larament/actions/workflows/phpstan.yml/badge.svg)](https://github.com/CodeWithDennis/larament/actions/workflows/phpstan.yml)
[![Total Installs](https://img.shields.io/packagist/dt/codewithdennis/larament.svg?style=flat-square)](https://packagist.org/packages/codewithdennis/larament)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/codewithdennis/larament.svg?style=flat-square)](https://packagist.org/packages/codewithdennis/larament)

![Larament](https://raw.githubusercontent.com/CodeWithDennis/larament/main/resources/images/larament.png)

**Larament** is a time-saving starter kit to quickly launch Laravel projects. It includes **FilamentPHP** pre-installed and configured, along with additional tools and features to streamline your development workflow.

---

## Table of Contents

-   [Features](#features)
    -   [Security and Testing](#security-and-testing)
    -   [Quality of Life](#quality-of-life)
    -   [Design](#design)
-   [Default User](#default-user)
-   [Included Packages](#included-packages)
-   [Installation](#installation)
    -   [Development Environment Setup](#development-environment-setup)
    -   [Using the Template](#using-the-template)
    -   [CLI Installation](#cli-installation)

---

## Features

### Security and Testing

-   **PESTPHP**: Preconfigured with test cases for streamlined testing. ([Learn more](https://pestphp.com/docs/installation))
-   **Strict mode enabled** via [Should Be Strict](https://laravel-news.com/shouldbestrict):
    -   Prevents lazy loading (N+1 queries).
    -   Guards against discarding or accessing missing attributes.
-   **Production safeguards**: Prevents destructive commands in production. ([Learn more](https://laravel-news.com/prevent-destructive-commands-from-running-in-laravel-11))
-   **Architectural testing** with Archtest.
-   **Static analysis** using PHPStan.
-   **Debugging** with Laravel Debugbar.

### Quality of Life

-   Custom login page autofills email and password with seeded data for quicker testing.
-   Built-in password generator action on the user profile and user resource pages.
-   Enhanced global search includes email addresses for better discoverability.
-   Auto-translatable component labels.
-   `composer review`: A single command to run Pint, PHPStan, and PEST.
-   Helper functions available through a dedicated helper file.
-   Custom `php artisan make:filament-action` command for generating Filament actions.

### Design

![User Global Search](https://raw.githubusercontent.com/CodeWithDennis/larament/main/resources/images/user-global-search.jpg)

-   Filament Panel's primary color is preset to blue.
-   Single Page Application (SPA) mode enabled by default.
-   Global search keybinding set to `CTRL + K` or `CMD + K`.
-   A ready-to-use FilamentPHP custom theme, including a sidebar separator.
-   Enhanced profile page with a built-in password generator.

---

## API Authentication

The project includes Laravel Sanctum for API authentication. Here's how to use it:

### Login and Get Token

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email": "your@email.com", "password": "password", "device_name": "test"}'
```

### Using the Token

Add the token to your requests using the Bearer authentication scheme:

```bash
curl -X GET http://localhost:8000/api/user \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

### Logout

```bash
curl -X POST http://localhost:8000/api/logout \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

---

## Default User

A default user is seeded with the following credentials, pre-filled on the login page for quick access:

```dotenv
DEFAULT_USER_NAME="John Doe"
DEFAULT_USER_EMAIL="admin@example.com"
DEFAULT_USER_PASSWORD="password"
```

## Included Packages

The following packages are pre-installed:

-   [timokoerber/laravel-one-time-operations](https://github.com/TimoKoerber/laravel-one-time-operations)
-   [barryvdh/laravel-debugbar](https://github.com/barryvdh/laravel-debugbar)
-   [phpstan/phpstan](https://phpstan.org/user-guide/getting-started)
-   [pestphp/pest](https://pestphp.com/docs/installation)
    -   [pestphp/pest-plugin-faker](https://pestphp.com/docs/plugins#faker)
    -   [pestphp/pest-plugin-laravel](https://pestphp.com/docs/plugins#laravel)
    -   [pestphp/pest-plugin-livewire](https://pestphp.com/docs/plugins#livewire)

## Installation

### Development Environment Setup

1. Clone the repository to your local machine:

```bash
git clone <repository-url>
cd <project-directory>
```

2. Install PHP dependencies:

```bash
composer install
```

3. Install and build frontend assets:

```bash
npm install
npm run build
```

4. Set up the environment:

```bash
cp .env.example .env
php artisan key:generate
```

5. Set up the database:

```bash
php artisan migrate:refresh --seed
php artisan db:seed --class=MediaSeeder
```

6. Create a Filament admin user:

```bash
php artisan filament:user
```

This will prompt you to enter an email and password for the admin user.

7. Start the development server:

```bash
php artisan serve
```

You can now access the application at http://localhost:8000 and the admin panel at http://localhost:8000/admin.

### Using the Template

-   Create a repository using the Larament template.
-   Clone your repository to your local machine.
    Navigate to the project directory and run the following commands:

```bash
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
```

### CLI Installation

Alternatively, you can use the following command to create a new project with Larament:

```bash
composer create-project --prefer-dist CodeWithDennis/larament example-app
```

### Create a Terminal Alias

For easier usage in future projects, create an alias in your terminal:

```bash
alias larament="composer create-project --prefer-dist CodeWithDennis/larament"
```

Now, you can create a new project with a simple command:

```bash
larament my-cool-app

```

## Roadmap

### Upcoming Features

- **One-time Password (OTP) System**
  - Secure OTP generation and verification
  - OTP expiration and re-issuance
  - OTP history tracking
  - OTP usage statistics
  - https://x.com/i/grok?conversation=1908162706223378551

- **Help Documentation**
  - Comprehensive guide for new users
  - Step-by-step tutorials
  - FAQ section
  - Troubleshooting guide
  - "WalkMe"tm-like user guidance system
  https://x.com/i/grok?conversation=1908160916710940782

- **Email Tracking System**
  - Advanced analytics for email opens and clicks
  - Customizable tracking pixel implementation
  - Privacy-compliant tracking options
  - A/B testing capabilities

- **Enhanced Workflow Management**
  - Visual workflow builder
  - Conditional branching and parallel execution
  - Integration with external services
  - Real-time workflow monitoring
  - Advanced Workflow Triggers
  - n8n integration
  - https://x.com/i/grok?conversation=1908018461017608268

- **Contact Management Improvements**
  - Advanced segmentation tools
  - Automated contact scoring
  - Custom field builder
  - Bulk operations enhancements

- **Reporting & Analytics**
  - Custom report builder
  - Export capabilities in multiple formats
  - Scheduled report generation
  - Interactive dashboards

- **System Integration**
  - API expansion for third-party integrations
  - Webhook system for real-time updates
  - OAuth2 provider implementation
  - Batch import/export tools

### Long-term Goals

- Mobile application development
- Machine learning for contact scoring
- Advanced automation capabilities
- Multi-tenant architecture
- Localization support for multiple languages
