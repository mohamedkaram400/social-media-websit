<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>



# Social Media Websit

## 🧠 Project Overview

This is a fully functional Laravel-based social media website that allows users to register, log in, create posts, like posts, follow other users, and view a personalized feed. It's built using Laravel 10 and follows MVC architecture principles.


## 🛠️ Tech Stack

- Laravel 10
- PHP 8.1
- MySQL
- Tailwind CSS
- Vue.js 3
- Inertiajs
- Laravel Breeze for authentication
- 
## Installation

1. Clone the repository:
   ```shell
   git clone https://github.com/mohamedkaram400/social-media-websit.git
   ```

2. Navigate to the project directory:
   ```shell
   cd social-media-websit
   ```

3. Install dependencies using Composer:
   ```shell
   composer install
   ```

4. Create a `.env` file by copying `.env.example`:
   ```shell
   cp .env.example .env
   ```

5. Generate a new Laravel application key:
   ```shell
   php artisan key:generate
   ```

6. Create a fresh database and update the DB name in `.env` file

7. Migrate the DB and seed the data
   ```shell
   php artisan migrate:fresh --seed
   ```

8. Install dependencies for frontend using npm
   ```shell
   npm install
   ```

8. Start the frontend development server
   ```shell
   npm run dev
   ```

8. Start the Laravel development server
   ```shell
   php artisan serve
   ```

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
