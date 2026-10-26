<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'client_id',
        'judul',
        'deskripsi',
        'kategori',
        'budget_min',
        'budget_max',
        'batas_lelang',
    ];

    protected function casts(): array
    {
        return [
            'budget_min' => 'integer',
            'budget_max' => 'integer',
            'batas_lelang' => 'date',
        ];
    }

    // Setiap proyek dimiliki oleh satu klien
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    // Satu proyek bisa memiliki banyak penawaran
    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class, 'project_id');
    }
}
