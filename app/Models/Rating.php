<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rating extends Model
{
    protected $fillable = [
        'project_id',
        'client_id',
        'developer_id',
        'nilai',
        'komentar',
    ];

    protected function casts(): array
    {
        return [
            'nilai' => 'integer',
        ];
    }

    // Rating diberikan untuk satu proyek
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    // Klien yang memberikan rating
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    // Developer yang menerima rating
    public function developer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'developer_id');
    }
}
