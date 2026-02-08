<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'users';

    // Tabel kamu hanya punya created_at (tidak ada updated_at)
    public $timestamps = false;

    protected $fillable = [
        'name',
        'class',
    ];

    // Tidak ada password / remember_token di schema kamu
    protected $hidden = [];
}
