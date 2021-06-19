<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $connection = "mysql";
    protected $table = "items";

    public $timestamps = false;

    protected $fillable = [
        'name', 'type', 'rarity'
    ];
}
