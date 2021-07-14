<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enemy extends Model
{
    protected $connection = "mysql";
    protected $table = "enemy";

    public $timestamps = false;

    protected $fillable = [
        'name', 'level', 'health', 'strength', 'dexterity', 'intelligence', 'gold', 'exp'
    ];

    protected $attributes = [

    ];
}
