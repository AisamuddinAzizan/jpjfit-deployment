<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HealthRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'participant_id',
        'test_session_id',
        'recorded_by',
        'height_cm',
        'weight_kg',
        'bmi',
        'bmi_status',
        'blood_pressure_systolic',
        'blood_pressure_diastolic',
        'glucose_mmol',
        'cholesterol_mmol',
        'cholesterol_status',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'height_cm' => 'float',
            'weight_kg' => 'float',
            'bmi' => 'float',
            'glucose_mmol' => 'float',
            'cholesterol_mmol' => 'float',
        ];
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    public function testSession(): BelongsTo
    {
        return $this->belongsTo(TestSession::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
