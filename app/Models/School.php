<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    protected $fillable = ['district_id', 'name', 'school_id_number', 'address'];

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function head()
    {
        return $this->hasOne(User::class)->where('role', 'school_head');
    }
}
