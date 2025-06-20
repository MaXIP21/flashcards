# Flashcard Learning Platform

A Laravel-based web application for language learning through interactive flashcards. Users can create flashcard sets, practice them with an interactive UI, and track their progress. Teachers can assign sets to students and monitor their activity.

## Features

- **User Roles**: Admin, Teacher, and Student roles with distinct permissions.
- **Flashcard Set Management**: Create, edit, and delete flashcard sets with public or private visibility.
- **Flashcard Management**: Add, edit, and delete individual cards within a set.
- **Bulk Import**: Import flashcards from CSV or JSON files.
- **Interactive Practice**: An Alpine.js powered practice interface with card-flipping animations, progress tracking, and keyboard navigation.
- **Assignment System**: Teachers can assign flashcard sets to students.
- **Progress Tracking**: Teachers can view detailed progress reports for their students.
- **Public Sharing**: Share flashcard sets with anyone via a unique public URL.

## Technology Stack

- **Backend**: Laravel 12
- **Frontend**: Blade, Tailwind CSS, Alpine.js
- **Database**: MariaDB/MySQL
- **Testing**: Pest PHP
- **Development**: Laravel Sail (Docker)

## Getting Started

Follow these instructions to get the project up and running on your local machine.

### Prerequisites

- PHP 8.2+
- Composer
- Node.js & NPM
- A database server (like MariaDB or MySQL)

### Installation

1.  **Clone the repository:**
    ```bash
    git clone <repository-url>
    cd flashcards
    ```

2.  **Install dependencies:**
    ```bash
    composer install
    npm install
    ```

3.  **Set up the environment file:**
    ```bash
    cp .env.example .env
    ```
    Open the `.env` file and configure your database connection details (`DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).

4.  **Generate application key:**
    ```bash
    php artisan key:generate
    ```

5.  **Run database migrations:**
    This will create all the necessary tables in your database.
    ```bash
    php artisan migrate
    ```

6.  **Compile frontend assets:**
    ```bash
    npm run dev
    ```

7.  **Start the development server:**
    ```bash
    php artisan serve
    ```
    The application will be available at `http://127.0.0.1:8000`.

### Creating an Admin User

To create a user with administrative privileges, run the following command and follow the prompts:

```bash
php artisan app:create-admin
```

You will be asked to provide a name, email, and password for the new admin account. Once created, you can log in with these credentials.

## Available Commands

Here is a list of useful commands for development and testing:

- **Start Development Server:**
  ```bash
  php artisan serve
  ```

- **Run Frontend Watcher:**
  Compiles assets on change.
  ```bash
  npm run dev
  ```

- **Run Pest Tests:**
  Executes the entire test suite.
  ```bash
  php artisan test
  ```

- **Run Database Migrations:**
  ```bash
  php artisan migrate
  ```

- **Create Admin User:**
  ```bash
  php artisan app:create-admin
  ```

## Project Structure

- `app/Http/Controllers`: Contains controllers for different roles (Admin, Teacher, Student).
- `app/Models`: Eloquent models for Users, Flashcards, etc.
- `app/Policies`: Authorization policies for models.
- `database/migrations`: Database schema migrations.
- `resources/views`: Blade templates for the UI.
- `routes/web.php`: Application routes.
- `tests/`: Pest PHP test files (Unit and Feature). 