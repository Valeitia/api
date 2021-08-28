<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Item;
use App\Models\Inventory;

class Battle extends Model
{
    protected $connection = "mysql";
    protected $table = "battles";

    public $timestamps = false;

    protected $fillable = [
        'user', 'enemy', 'health'
    ];

    protected $attributes = [

    ];

    public function user()
    {
        return $this->hasOne("App\Models\User", "id", "user")->first();
    }

    public function enemy()
    {
        return $this->hasOne("App\Models\Enemy", "id", "enemy")->first();
    }

    public function getStartingHealth(): int {
        return $this->enemy()->health;
    }

    public function dropGear($enemyLevel) {
        $rand = rand(0, 1000);
        $stats = [];

        $equipment_types = ["helmet", "chestplate", "leggings", "boots", "weapon"];
        $item = Item::whereIn('type', $equipment_types)->inRandomOrder()->first();

        if ($rand <= 25 && $enemyLevel >= 25) {
            $stats['rarity'] = "Legendary";
            $stats[$item->primary_attribute] = rand($this->user()->level, round($this->user()->level * 2));
        } else if ($rand <= 50 && $enemyLevel >= 10) {
            $stats['rarity'] = "Rare";
            $stats[$item->primary_attribute] = rand($this->user()->level, round($this->user()->level * 1.75));
        } else if ($rand <= 100) {
            $stats['rarity'] = "Uncommon";
            $stats[$item->primary_attribute] = rand($this->user()->level, round($this->user()->level * 1.50));
        } else if ($rand <= 200) {
            $stats['rarity'] = "Common";
            $stats[$item->primary_attribute] = rand($this->user()->level, round($this->user()->level * 1.10));
        } else {
            return null;
        }

        return Inventory::create([
            "user" => $this->user()->id,
            "item" => $item->id,
            "amount" => 1,
            "stats" => json_encode($stats)
        ]);
    }
}
