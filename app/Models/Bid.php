<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bid extends Model
{
    protected $fillable = [
        'project_id',
        'developer_id',
        'harga',
        'durasi',
        'proposal',
    ];

    protected function casts(): array
    {
        return [
            'harga' => 'integer',
            'durasi' => 'integer',
            'is_winner' => 'boolean',
        ];
    }

    // Penawaran ditujukan untuk satu proyek
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    // Penawaran dibuat oleh satu developer
    public function developer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'developer_id');
    }
}
