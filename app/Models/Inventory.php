<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $connection = "mysql";
    protected $table = "inventory";

    public $timestamps = false;

    protected $fillable = [
        'user', 'item', 'amount'
    ];

    protected $attributes = [
        'amount' => 1
    ];
}
