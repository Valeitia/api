<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $connection = "mysql";
    protected $table = "item";

    public $timestamps = false;

    protected $fillable = [
        'name', 'type', 'primary_attribute'
    ];
}
