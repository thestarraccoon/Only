# 🚗 Laravel Car Rental API

[![Tests](https://github.com/thestarraccoon/Only/actions/workflows/tests.yml/badge.svg)](https://github.com/thestarraccoon/Only/actions/workflows/tests.yml)
[![Coverage](https://img.shields.io/endpoint?url=https://codecov.io/github/thestarraccoon/Only/coverage.svg?branch=main)](https://codecov.io/gh/thestarraccoon/Only)
[![PHP](https://img.shields.io/badge/PHP-8.1-blue.svg)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-10.x-red.svg)](https://laravel.com)

**Профессиональный API для аренды автомобилей с 95% покрытием тестами**

## ✨ Функционал
- 🔐 **Sanctum API Authentication**
- 🏢 **Корпоративная авторизация** (директор/специалист)
- 🚗 **Поиск доступных машин** по времени/модели
- 📅 **Пересечение броней** (исключение занятых)
- 🧪 **Feature Tests** - 95% покрытие
- 🌍 **Локализация ошибок**

## 🚀 Быстрый старт
```bash
git clone https://github.com/thestarraccoon/Only.git
cd Only
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan test  # 95% покрытие ✅
