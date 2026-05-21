<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TestSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_code',
        'title',
        'location',
        'session_date',
        'start_time',
        'end_time',
        'status',
        'description',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'session_date' => 'date',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(Participant::class)
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
}
