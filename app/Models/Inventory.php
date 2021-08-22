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
        'user', 'item', 'amount', 'stats'
    ];

    protected $attributes = [
        'amount' => 1
    ];

    public function item() {
        return $this->hasOne('App\Models\Item', 'id', 'item')->first();
    }

    public function user() {
        return $this->hasOne('App\Models\User', 'id', 'user')->first();
    }
}
