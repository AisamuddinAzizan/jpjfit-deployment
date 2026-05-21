<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FitnessResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'participant_id',
        'test_session_id',
        'recorded_by',
        'push_ups',
        'sit_ups',
        'sit_and_reach_cm',
        'shuttle_run_level',
        'run_2_4km_seconds',
        'total_score',
        'classification',
        'result_status',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'sit_and_reach_cm' => 'float',
            'shuttle_run_level' => 'float',
            'total_score' => 'float',
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
