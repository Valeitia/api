<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Battle extends Model
{
    protected $connection = "mysql";
    protected $table = "battles";

    public $timestamps = true;

    protected $fillable = [
        'user', 'enemy', 'health'
    ];

    protected $attributes = [

    ];

    public function enemy(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne("App\Models\Enemy", "id", "enemy");
    }

    public function getStartingHealth() {
        return $this->enemy()->health;
    }


}
