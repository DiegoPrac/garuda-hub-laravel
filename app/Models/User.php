<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nama',
        'email',
        'password',
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
        ];
    }

    // Satu klien dapat membuat banyak proyek
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'client_id');
    }

    // Satu developer dapat mengajukan banyak penawaran
    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class, 'developer_id');
    }
}
