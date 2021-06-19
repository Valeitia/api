<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    protected $connection = "mysql";
    protected $table = "users";

    public $timestamps = false;

    protected $fillable = [
        'discord_id', 'gold'
    ];

    protected $attributes = [
        'gold' => 0
    ];
}
