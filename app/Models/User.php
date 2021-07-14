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
        'discord_id', 'gold', 'level', 'exp', 'health', 'energy', 'helmet', 'chestplate', 'boots', 'weapon', 'strength', 'dexterity', 'intelligence'
    ];

    protected $attributes = [
        'gold' => 0,
        'level' => 1,
        'exp' => 0,
        'health' => 100,
        'energy' => 100,
        'strength' => 1,
        'dexterity' => 1,
        'intelligence' => 1
    ];

    public function hasOngoingBattle(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne("App\Models\Battle", "user", "id");
    }

    public function hasEnoughEnergy($energy): bool {
        return $this->energy >= $energy;
    }

}
