<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Accomplishment extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'description',
        'photo_path',
        'latitude',
        'longitude',
        'address',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
