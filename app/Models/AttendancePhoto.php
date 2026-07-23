<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendancePhoto extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'type',
        'photo_path',
        'latitude',
        'longitude',
        'address',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
