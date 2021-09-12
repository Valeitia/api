<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    protected $connection = "mysql";
    protected $table = "users";

    public $timestamps = false;

    protected $fillable = [
        'user', 'code'
    ];

    protected $attributes = [];

}
