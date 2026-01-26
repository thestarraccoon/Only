<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'car_id',
        'user_id',
        'start_at',
        'end_at',
        'destination',
        'status',
    ];

    protected $dates = ['deleted_at'];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', '!=', BookingStatus::CANCELLED);
    }

    public function scopeOverlaps($query, $timeOverlap)
    {
        return $query->where($timeOverlap);
    }
}
