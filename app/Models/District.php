<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $fillable = ['name'];

    public function schools()
    {
        return $this->hasMany(School::class);
    }

    public function head()
    {
        return $this->hasOne(User::class)->where('role', 'district_head');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
