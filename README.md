Racket Arena 🏸

A Badminton Court Booking System built with Laravel (Backend) and React/Flutter (Frontend).

Features

[x] API Backend: Laravel 11

[x] Authentication: Laravel Sanctum (Register/Login)

[x] Court Management: Dynamic pricing and availability

[ ] Booking System: (In Progress)

[ ] Frontend: React/Flutter (Coming Soon)

Setup

Clone the repo

Run composer install

Copy .env.example to .env

Run php artisan migrate --seed

Run php artisan serve

API Endpoints

POST /api/register - Create account

POST /api/login - Get access token

GET /api/courts - View all courts
