<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Real-Time Chat API
A scalable, real-time chat system built with Laravel, featuring RESTful API endpoints, WebSocket-based messaging, and secure authentication.


##  System Features
- Real-Time Messaging: Instant message delivery using Laravel WebSockets and Pusher.
- Offline Support: Messages are stored in the database and delivered when users come online.
- Secure Authentication: JWT-based authentication for all API endpoints.
- Performance: Database indexing and rate limiting to ensure scalability and prevent spam.
- Event-Driven: Uses Laravel's broadcasting system for real-time notifications.
- API Documentation: Fully documented endpoints via Swagger.


## Installation

git clone https://github.com/badorhassan/realtime-chat-api.git

- cd realtime-chat-api
- cp .env.example .env

## .env Configuration
- DB_CONNECTION=mysql
- DB_HOST=127.0.0.1
- DB_PORT=3306
- DB_DATABASE=chat_system
- DB_USERNAME=your_username
- DB_PASSWORD=your_password

- PUSHER_APP_ID=your_pusher_app_id
- PUSHER_APP_KEY=your_pusher_key
- PUSHER_APP_SECRET=your_pusher_secret
- PUSHER_APP_CLUSTER=your_pusher_cluster

## generate key
- php artisan key:generate
- php artisan jwt:secret
- php artisan migrate

## Run
- php artisan serve

## Generate Swagger docs:
- php artisan l5-swagger:generate

## API Endpoints
-  POST /api/auth/login
-  POST /api/chat/send
-  GET /api/chat/messages/2
-  PATCH /api/chat/read/1

## Testing

- php artisan test

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
