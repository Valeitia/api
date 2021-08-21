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

    public function user(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne("App\Models\User", "id", "user");
    }

    public function enemy(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne("App\Models\Enemy", "id", "enemy");
    }

    public function getStartingHealth(): int {
        return $this->enemy()->health;
    }

    public function dropGear() {
        $rand = rand(0, 1000);
        $stats = [];

        $equipment_types = ["helmet", "chestplate", "boots", "weapon"];
        $possible_gear = Item::where('type', array_rand($equipment_types, 1))->get();
        $possible_gear_count = count($possible_gear);

        $item = $possible_gear[rand(0, $possible_gear_count - 1)];

        if ($rand <= 1) {
            $stats['rarity'] = "Mythic";
            $stats['strength'] = rand($this->user()->level * 2, round($this->user()->level * 4));
            $stats['dexterity'] = rand($this->user()->level * 2, round($this->user()->level * 4));
            $stats['intelligence'] = rand($this->user()->level * 2, round($this->user()->level * 4));
        } else if ($rand <= 25) {
            $stats['rarity'] = "Legendary";
            $stats[$item->primary_attribute] = rand($this->user()->level, round($this->user()->level * 2));
        } else if ($rand <= 50) {
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
            "item" => $item->id,
            "amount" => 1,
            "stats" => $stats
        ]);
    }
}
