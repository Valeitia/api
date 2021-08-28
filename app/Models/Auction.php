<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auction extends Model
{
    protected $connection = "mysql";
    protected $table = "auction";

    public $timestamps = true;

    protected $fillable = [
        'user', 'inv', 'price', 'created_at', 'updated_at'
    ];

    protected $attributes = [

    ];

    public function inv() {
        return $this->hasOne('App\Models\Inventory', 'id', 'inv')->first();
    }

    public function user() {
        return $this->hasOne('App\Models\User', 'id', 'user')->first();
    }
}
