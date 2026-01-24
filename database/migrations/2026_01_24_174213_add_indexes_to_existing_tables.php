<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comfort_categories', function (Blueprint $table) {
            $table->index('level', 'comfort_categories_level_index');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('position_id', 'users_position_id_index');
        });

        Schema::table('position_comfort_category', function (Blueprint $table) {
            $table->index('comfort_category_id', 'position_comfort_category_idx');
        });

        Schema::table('car_models', function (Blueprint $table) {
            $table->index('comfort_category_id', 'car_models_category_index');
        });

        Schema::table('cars', function (Blueprint $table) {
            $table->index(['is_active', 'car_model_id'], 'cars_active_model_index');
            $table->index(['driver_id', 'is_active'], 'cars_driver_active_index');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->index('status', 'bookings_status_index');
            $table->index(['user_id', 'status'], 'bookings_user_status_index');
            $table->index(['car_id', 'status', 'start_at', 'end_at'], 'bookings_availability_index');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('bookings_status_index');
            $table->dropIndex('bookings_user_status_index');
            $table->dropIndex('bookings_availability_index');
        });

        Schema::table('cars', function (Blueprint $table) {
            $table->dropIndex('cars_active_model_index');
            $table->dropIndex('cars_driver_active_index');
        });

        Schema::table('car_models', function (Blueprint $table) {
            $table->dropIndex('car_models_category_index');
        });

        Schema::table('position_comfort_category', function (Blueprint $table) {
            $table->dropIndex('position_comfort_category_idx');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_position_id_index');
        });

        Schema::table('comfort_categories', function (Blueprint $table) {
            $table->dropIndex('comfort_categories_level_index');
        });
    }
};
