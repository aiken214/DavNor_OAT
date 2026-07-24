<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'provider',
        'provider_id',
        'avatar',
        'bio_id',
        'tag',
        'role',
        'section_id',
        'district_id',
        'school_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'tag' => 'integer',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isSectionHead(): bool
    {
        return $this->role === 'section_head';
    }

    public function isDistrictHead(): bool
    {
        return $this->role === 'district_head';
    }

    public function isSchoolHead(): bool
    {
        return $this->role === 'school_head';
    }

    public function isEmployee(): bool
    {
        return $this->role === 'employee';
    }

    public function hasAdminAccess(): bool
    {
        return in_array($this->role, ['super_admin', 'section_head', 'district_head', 'school_head']);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function accomplishments()
    {
        return $this->hasMany(Accomplishment::class);
    }

    public function attendancePhotos()
    {
        return $this->hasMany(AttendancePhoto::class);
    }

    public function managedUserIds(): array
    {
        switch ($this->role) {
            case 'super_admin':
                return User::pluck('id')->toArray();
            case 'section_head':
                return User::where('section_id', $this->section_id)->pluck('id')->toArray();
            case 'district_head':
                $schoolIds = School::where('district_id', $this->district_id)->pluck('id');
                return User::where('district_id', $this->district_id)
                    ->orWhereIn('school_id', $schoolIds)
                    ->pluck('id')->toArray();
            case 'school_head':
                return User::where('school_id', $this->school_id)->pluck('id')->toArray();
            default:
                return [$this->id];
        }
    }

    public function canManageUser(User $target): bool
    {
        return in_array($target->id, $this->managedUserIds());
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'super_admin' => 'Super Admin',
            'section_head' => 'Section Head',
            'district_head' => 'District Head',
            'school_head' => 'School Head',
            default => 'Employee',
        };
    }
}
