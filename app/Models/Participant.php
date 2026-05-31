<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class Participant extends Model
{
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'participant_no',
        'full_name',
        'ic_no',
        'date_of_birth',
        'gender',
        'email',
        'phone',
        'agency',
        'rank',
        'address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function sessions(): BelongsToMany
    {
        return $this->belongsToMany(TestSession::class)
            ->withPivot(['attendance_status', 'result_status'])
            ->withTimestamps();
    }

    public function healthRecords(): HasMany
    {
        return $this->hasMany(HealthRecord::class);
    }

    public function fitnessResults(): HasMany
    {
        return $this->hasMany(FitnessResult::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    protected static function booted()
    {
    static::creating(function ($participant) {

        $number = (self::max('id') ?? 0) + 1;

        $participant->participant_no =
            'JPJPRK-'.date('Y').'-'.str_pad($number, 4, '0', STR_PAD_LEFT);
    });
}
}
