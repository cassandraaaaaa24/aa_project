# Twitter-Like App

A Laravel web application built with Laravel 12, MySQL/SQLite, and Blade templating. Features user authentication, tweet management, and a like system.

## Features

- User authentication (registration and login)
- Create, read, update, and delete tweets
- Like/unlike tweets with like counter
- User profiles with their tweets
- Responsive design with Blade templates
- Database-backed session storage

## Requirements

- PHP 8.2 or higher
- Composer
- Node.js (for npm assets)
- SQLite or MySQL

## Installation & Setup

### 1. Clone the repository
```bash
git clone <repository-url>
cd twitter-like-app
```

### 2. Install dependencies
```bash
composer install
npm install
```

### 3. Environment setup
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure database (choose one)

**Option A: SQLite (Recommended for local development)**
- Already configured in `.env` by default
- Database file will be created automatically at `database/database.sqlite`

**Option B: MySQL (For production)**
- Update `.env` with your MySQL credentials:
  ```
  DB_CONNECTION=mysql
  DB_HOST=127.0.0.1
  DB_PORT=3306
  DB_DATABASE=twitter_app
  DB_USERNAME=root
  DB_PASSWORD=your_password
  ```

### 5. Run migrations
```bash
php artisan migrate
```

### 6. Build assets
```bash
npm run build
```

### 7. Start the development server
```bash
php artisan serve
```

The app will be available at `http://localhost:8000`

## Project Structure

- `/app/Models` - Eloquent models (User, Tweet)
- `/app/Http/Controllers` - Application controllers
- `/database/migrations` - Database schema migrations
- `/resources/views` - Blade templates
- `/routes/web.php` - Application routes
- `/config/database.php` - Database configuration

## Models & Relationships

### User
- `hasMany` tweets
- `belongsToMany` liked tweets (through tweet_user_likes pivot table)

### Tweet
- `belongsTo` user
- `belongsToMany` users who liked it (through tweet_user_likes pivot table)

## Database Tables

- `users` - User accounts
- `tweets` - Tweet posts with content and like count
- `tweet_user_likes` - Pivot table for tracking user likes

## Available Routes

### Public Routes
- `GET /` - Tweet feed
- `GET /landing` - Landing page
- `POST /register` - User registration
- `POST /login` - User login
- `POST /logout` - User logout
- `GET /tweets/{id}` - View single tweet
- `GET /users/{id}` - View user profile

### Authenticated Routes
- `GET /tweets/create` - Create tweet form
- `POST /tweets` - Store new tweet
- `GET /tweets/{id}/edit` - Edit tweet form
- `PUT /tweets/{id}/update` - Update tweet
- `DELETE /tweets/{id}/delete` - Delete tweet
- `POST /tweets/{id}/like` - Like/unlike tweet

## Troubleshooting

### Database connection error
- Ensure your database server is running
- Check DB credentials in `.env`
- Run `php artisan migrate` to create tables

### Assets not loading
- Run `npm run build` to compile CSS/JS
- Check `public/build` folder exists

## License

This project is open-source software licensed under the MIT license.

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
