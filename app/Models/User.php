<?php

namespace App\Models;

use App\Models\Item;
use App\Models\Inventory;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    protected $connection = "mysql";
    protected $table = "users";

    public $timestamps = false;

    protected $fillable = [
        'discord_id', 'gold', 'level', 'exp', 'health', 'energy', 'helmet', 'chestplate', 'boots', 'weapon', 'strength', 'dexterity', 'intelligence'
    ];

    protected $attributes = [
        'gold' => 0,
        'level' => 1,
        'exp' => 0,
        'health' => 100,
        'energy' => 100,
        'strength' => 1,
        'dexterity' => 1,
        'intelligence' => 1
    ];

    public function hasOngoingBattle(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne("App\Models\Battle", "user", "id");
    }

    public function hasEnoughEnergy($energy): bool {
        return $this->energy >= $energy;
    }

    public function addItem($item, $amount): string {
        $item = Item::find($item);

        if ($inv = Inventory::whereIn(['id' => $this->id, 'item' => $item->id])->exists()) {
            $inv->amount += $amount;
            $inv->save();

            return "x{$amount} {$item->name} added to your inventory";
        }

        $inv = Inventory::create([
            "user" => $this->id,
            "item" => $item->id,
            "amount" => $amount
        ]);

        return "x{$amount} {$item->name} added to your inventory";
    }

    public function removeItem($item, $amount): string {
        $item = Item::find($item);

        if ($inv = Inventory::whereIn(['id' => $this->id, 'item' => $item->id])->exists()) {
            if ($inv->amount < $amount) {
                return "You do not have x{$amount} {$item->name}";
            }

            if ($inv->amount === $amount) {
                $inv->delete();
                return "x{$amount} {$item->name} removed from your inventory";
            }

            $inv->amount -= $amount;
            $inv->save();

            return "x{$amount} {$item->name} removed from your inventory";
        } else {
            return "You do not have x{$amount} {$item->name}";
        }
    }

    public function canLevelUp(): bool {
        return $this->exp >= (25 * ($this->level + 1) * ($this->level + 1) - 25 * ($this->level + 1));
    }

    public function levelUp(): bool {
        if ($this->canLevelUp()) {
            $this->increment('level');
            $this->exp = 0;
            $this->save();

            return true;
        }

        return false;
    }
}
