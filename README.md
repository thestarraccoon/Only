# Корпоративные авто для сотрудников - API

[![Tests](https://github.com/thestarraccoon/Only/actions/workflows/tests.yml/badge.svg)](https://github.com/thestarraccoon/Only/actions/workflows/tests.yml)
[![Coverage](https://codecov.io/github/thestarraccoon/Only/branch/main/graph/badge.svg?token=PMCVJZKELW)](https://codecov.io/github/thestarraccoon/Only)
[![PHP](https://img.shields.io/badge/PHP-8.1-blue.svg)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-10.x-red.svg)](https://laravel.com)

###### tags: `PHP` `Laravel`

## Стек разработки

- PHP Version：PHP 8.1.x
- Laravel Version：10
- MySQL Version：8.0.x

## Особенности

- Sanctum Auth
- Swagger UI

## Установка

- copy .env.example .env
- composer install
- php artisan key:generate
- php artisan storage:link
- php artisan migrate:fresh --seed

##Создание бронирований

- php artisan tinker
- App\Models\Booking::factory()->count(10)->create();

## API-документация

- В API реализована регистрация, вход/выход, получение профиля, и главный метод получения списка доступных машин.


- Для некоторых роутов используется обязательным X-Corporate-ID, который должен соответствовать пользователю (директорский токен и директорский заголовок).
Предположим, что фронтенд обеспечивает передачу этого заголовка между запросами. (e.g. corp-dir - директор, corp-mgr - менеджер, corp-scp - специалист)


- Присутствует небольшая мультиязычность, используется для перевода ответов API (401, 403, 404, 422, 500 коды). Доступные языки `ru/en`, по умолчанию `ru`.


- Часовые пояса не учитываются. Предположим, что софт писался для корпоративного клиента в часовом поясе UTC+0.


- Также предполагается, что 1 водитель может работать на 2 и более машинах.

1. `php artisan serve`
2. `http://127.0.0.1:8000/swagger`

## Запуск тестов

1. `php artisan test --coverage-clover coverage.xml`

