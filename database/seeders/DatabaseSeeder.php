<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\ComfortCategory;
use App\Models\Driver;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $luxury = ComfortCategory::create([
            'name' => 'Премиум',
            'level' => 1,
            'description' => 'Автомобили премиум-класса',
        ]);

        $business = ComfortCategory::create([
            'name' => 'Бизнес',
            'level' => 2,
            'description' => 'Автомобили бизнес-класса',
        ]);

        $standard = ComfortCategory::create([
            'name' => 'Стандарт',
            'level' => 3,
            'description' => 'Автомобили стандарт-класса',
        ]);

        $director = Position::create(['name' => 'Генеральный директор']);
        $director->comfortCategories()->attach([$luxury->id, $business->id, $standard->id]);

        $manager = Position::create(['name' => 'Менеджер']);
        $manager->comfortCategories()->attach([$business->id, $standard->id]);

        $specialist = Position::create(['name' => 'Специалист']);
        $specialist->comfortCategories()->attach([$standard->id]);

        $mercedesS = CarModel::create([
            'brand' => 'Mercedes-Benz',
            'model' => 'S-Class',
            'comfort_category_id' => $luxury->id,
        ]);

        $bmw5 = CarModel::create([
            'brand' => 'BMW',
            'model' => '5 Series',
            'comfort_category_id' => $business->id,
        ]);

        $toyotaCamry = CarModel::create([
            'brand' => 'Toyota',
            'model' => 'Camry',
            'comfort_category_id' => $standard->id,
        ]);

        // Создаём водителей
        $driver1 = Driver::create([
            'first_name' => 'Иван',
            'last_name' => 'Петров',
            'phone' => '+7 (999) 123-45-67',
            'license_number' => '1234 567890',
        ]);

        $driver2 = Driver::create([
            'first_name' => 'Сергей',
            'last_name' => 'Сидоров',
            'phone' => '+7 (999) 234-56-78',
            'license_number' => '2345 678901',
        ]);

        $driver3 = Driver::create([
            'first_name' => 'Дмитрий',
            'last_name' => 'Иванов',
            'phone' => '+7 (999) 345-67-89',
            'license_number' => '3456 789012',
        ]);

        // Создаём автомобили
        Car::create([
            'car_model_id' => $mercedesS->id,
            'driver_id' => $driver1->id,
            'license_plate' => 'А123БВ777',
            'year' => 2023,
            'color' => 'Черный',
            'is_active' => true,
        ]);

        Car::create([
            'car_model_id' => $bmw5->id,
            'driver_id' => $driver2->id,
            'license_plate' => 'В456ГД777',
            'year' => 2022,
            'color' => 'Серый',
            'is_active' => true,
        ]);

        Car::create([
            'car_model_id' => $toyotaCamry->id,
            'driver_id' => $driver3->id,
            'license_plate' => 'Е789КЛ777',
            'year' => 2021,
            'color' => 'Белый',
            'is_active' => true,
        ]);

        // Создаём тестовых пользователей
        User::factory()->create([
            'name' => 'Директор Тестовый',
            'email' => 'director@example.com',
            'position_id' => $director->id,
        ]);

        User::factory()->create([
            'name' => 'Менеджер Тестовый',
            'email' => 'manager@example.com',
            'position_id' => $manager->id,
        ]);

        User::factory()->create([
            'name' => 'Специалист Тестовый',
            'email' => 'specialist@example.com',
            'position_id' => $specialist->id,
        ]);
    }
}
