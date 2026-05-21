<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'participant_id',
        'test_session_id',
        'certificate_no',
        'issued_at',
        'emailed_at',
        'issued_by',
        'pdf_path',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
            'emailed_at' => 'datetime',
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

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }
}
