<?php

namespace Database\Seeders;

use App\Enums\BookingStatus;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\ComfortCategory;
use App\Models\Driver;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->createComfortCategories();
        $this->createPositions();
        $this->createCarModels();
        $this->createDrivers();
        $this->createCars();
        $this->createUsers();
    }

    private function createComfortCategories(): void
    {
        $categories = [
            ['name' => 'Премиум', 'level' => 1, 'description' => 'Premium'],
            ['name' => 'Комфорт', 'level' => 2, 'description' => 'Comfort'],
            ['name' => 'Стандарт', 'level' => 3, 'description' => 'Standard'],
        ];

        foreach ($categories as $category) {
            ComfortCategory::create($category);
        }
    }

    private function createPositions(): void
    {
        $positions = [
            ['name' => 'Руководство', 'comfort_levels' => [1, 2, 3]],
            ['name' => 'Топ-менеджмент', 'comfort_levels' => [2, 3]],
            ['name' => 'Рядовой специалист', 'comfort_levels' => [3]],
        ];

        foreach ($positions as $positionData) {
            $position = Position::create(['name' => $positionData['name']]);
            $comfortCategories = ComfortCategory::whereIn('level', $positionData['comfort_levels'])->pluck('id');
            $position->comfortCategories()->attach($comfortCategories);
        }
    }

    private function createCarModels(): void
    {
        $models = [
            ['brand' => 'Honda', 'model' => 'Civic 4D', 'comfort_level' => 1],     // id=1
            ['brand' => 'Renault', 'model' => 'Logan', 'comfort_level' => 3],       // id=2
            ['brand' => 'Toyota', 'model' => 'Camry', 'comfort_level' => 2],        // id=3
            ['brand' => 'BMW', 'model' => 'M5', 'comfort_level' => 1],              // id=4
            ['brand' => 'Kia', 'model' => 'Sportage', 'comfort_level' => 2],        // id=5
            ['brand' => 'Lada', 'model' => 'Aura', 'comfort_level' => 1],           // id=6
            ['brand' => 'Lada', 'model' => 'Iskra', 'comfort_level' => 2],          // id=7
            ['brand' => 'Changan', 'model' => 'Uni-V', 'comfort_level' => 3],       // id=8
            ['brand' => 'Omoda', 'model' => 'C5', 'comfort_level' => 3],            // id=9
        ];

        foreach ($models as $model) {
            $comfortCategory = ComfortCategory::where('level', $model['comfort_level'])->first();
            CarModel::create([
                'brand' => $model['brand'],
                'model' => $model['model'],
                'comfort_category_id' => $comfortCategory->id,
            ]);
        }
    }

    private function createDrivers(): void
    {
        $drivers = [
            ['first_name' => 'Иван', 'last_name' => 'Петров', 'phone' => '+7 (999) 123-45-67', 'license_number' => '1234 567890'], // id=1
            ['first_name' => 'Сергей', 'last_name' => 'Сидоров', 'phone' => '+7 (999) 234-56-78', 'license_number' => '2345 678901'], // id=2
            ['first_name' => 'Дмитрий', 'last_name' => 'Иванов', 'phone' => '+7 (999) 345-67-89', 'license_number' => '3456 789012'], // id=3
            ['first_name' => 'Алексей', 'last_name' => 'Смирнов', 'phone' => '+7 (999) 456-78-90', 'license_number' => '4567 890123'], // id=4
            ['first_name' => 'Михаил', 'last_name' => 'Кузнецов', 'phone' => '+7 (999) 567-89-01', 'license_number' => '5678 901234'], // id=5
            ['first_name' => 'Николай', 'last_name' => 'Попов', 'phone' => '+7 (999) 678-90-12', 'license_number' => '6789 012345'],   // id=6
            ['first_name' => 'Владимир', 'last_name' => 'Соколов', 'phone' => '+7 (999) 789-01-23', 'license_number' => '7890 123456'], // id=7
            ['first_name' => 'Антон', 'last_name' => 'Морозов', 'phone' => '+7 (999) 890-12-34', 'license_number' => '8901 234567'],   // id=8
        ];

        foreach ($drivers as $driverData) {
            Driver::create($driverData);
        }
    }

    private function createCars(): void
    {
        // ✅ Предсказуемый порядок ID (1=Honda, 2=Renault, 3=Toyota, 4=BMW...)
        $modelIds = CarModel::orderBy('id')->pluck('id')->toArray();
        $driverIds = Driver::orderBy('id')->pluck('id')->toArray();

        // [modelIndex, driverIndex, year, color, isActive]
        $baseData = [
            // BMW M5 (model 3=id4) — 2 экземпляра
            [3, 0, 2024, 'Черный', true],      // BMW M5 #1 -> Иван Петров (driver 0=id1)
            [3, 1, 2023, 'Белый', false],      // BMW M5 #2 -> Сергей Сидоров (driver 1=id2, неактивна)

            // Honda Civic (model 0=id1) — 2 экземпляра
            [0, 0, 2023, 'Серый', true],       // Honda #1 -> Иван Петров (driver 0=id1)
            [0, 2, 2024, 'Синий', true],       // Honda #2 -> Дмитрий Иванов (driver 2=id3)

            // Toyota Camry (model 2=id3) — 3 экземпляра
            [2, 1, 2022, 'Белый', true],       // Camry #1 -> Сергей Сидоров (driver 1=id2)
            [2, 3, 2023, 'Темно-синий', false], // Camry #2 -> Алексей Смирнов (driver 3=id4, неактивна)
            [2, 4, 2021, 'Серебристый', true], // Camry #3 -> Михаил Кузнецов (driver 4=id5)

            // Renault Logan (model 1=id2) — 1 экземпляр
            [1, 3, 2021, 'Красный', true],     // Logan -> Алексей Смирнов (driver 3=id4)

            // Kia Sportage (model 4=id5) — 1 экземпляр
            [4, 2, 2023, 'Зеленый', false],    // Sportage -> Дмитрий Иванов (driver 2=id3, неактивна)

            // Lada Aura (model 5=id6) — 1 экземпляр
            [5, 4, 2024, 'Бежевый', true],     // Aura -> Михаил Кузнецов (driver 4=id5)
        ];

        $cars = [];
        foreach ($baseData as $index => [$modelIndex, $driverIndex, $year, $color, $isActive]) {
            $cars[] = [
                'car_model_id' => $modelIds[$modelIndex],
                'driver_id' => $driverIds[$driverIndex],
                'license_plate' => $this->generateLicensePlate($index),
                'year' => $year,
                'color' => $color,
                'is_active' => $isActive,
            ];
        }

        foreach ($cars as $car) {
            Car::create($car);
        }
    }

    private function generateLicensePlate(int $index): string
    {
        $letters = ['А', 'В', 'Е', 'К', 'М', 'Н', 'О', 'Р', 'С', 'Т', 'У', 'Х'];

        $firstNumber = sprintf('%03d', ($index + 1) * 111);
        $lastNumber = sprintf('%03d', rand(100, 999));

        $firstLetter = $letters[$index % count($letters)];
        $secondLetter = $letters[($index + 1) % count($letters)];
        $thirdLetter = $letters[($index + 2) % count($letters)];

        return "{$firstLetter}{$firstNumber}{$secondLetter}{$thirdLetter}{$lastNumber}";
    }

    private function createUsers(): void
    {
        $positions = Position::pluck('id', 'name')->toArray();
        $testPassword = Hash::make('123qweasd');

        $users = [
            [
                'name' => 'Директор Тестовый',
                'email' => 'director@example.com',
                'password' => $testPassword,
                'position_id' => $positions['Руководство']
            ],
            [
                'name' => 'Менеджер Тестовый',
                'email' => 'manager@example.com',
                'password' => $testPassword,
                'position_id' => $positions['Топ-менеджмент']
            ],
            [
                'name' => 'Специалист Тестовый',
                'email' => 'specialist@example.com',
                'password' => $testPassword,
                'position_id' => $positions['Рядовой специалист']
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
