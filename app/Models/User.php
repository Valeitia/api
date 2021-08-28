<?php

namespace App\Models;

use App\Models\Inventory;
use App\Models\Item;

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
        'discord_id', 'gold', 'level', 'exp', 'health', 'energy', 'helmet', 'chestplate', 'boots', 'weapon', 'strength', 'dexterity', 'intelligence', 'gathering', 'luck'
    ];

    protected $attributes = [
        'gold' => 0,
        'level' => 1,
        'exp' => 0,
        'health' => 100,
        'energy' => 100,
        'strength' => 1,
        'dexterity' => 1,
        'intelligence' => 1,
        'gathering' => 1,
        'luck' => 1
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

    public function equip(Inventory $inv) {
        $stats = json_decode($inv->stats, true);
        $item = $inv->item();

        if ($this->{$item->type} != null) {
            if ($this->{$item->type} == $inv->id) return;

            $previous = Inventory::where(["user" => $this->id, "id" => $this->{$item->type}])->first();
            $previousStats = json_decode($previous->stats, true);

            $this->decrement($previous->item()->primary_attribute, $previousStats[$previous->item()->primary_attribute]);
            $this->{$item->type} = $inv->id;
            $this->increment($item->primary_attribute, $stats[$item->primary_attribute]);
            $this->save();
            return;
        }

        $this->{$item->type} = $inv->id;
        $this->increment($item->primary_attribute, $stats[$item->primary_attribute]);
        $this->save();
    }

    public function helmet() {
        return $this->hasOne('App\Models\Inventory', 'id', 'helmet')->first();
    }

    public function chestplate() {
        return $this->hasOne('App\Models\Inventory', 'id', 'chestplate')->first();
    }

    public function leggings() {
        return $this->hasOne('App\Models\Inventory', 'id', 'leggings')->first();
    }

    public function boots() {
        return $this->hasOne('App\Models\Inventory', 'id', 'boots')->first();
    }

    public function weapon() {
        return $this->hasOne('App\Models\Inventory', 'id', 'weapon')->first();
    }

    public function inventory() {
        return $this->hasMany('App\Models\Inventory', 'user', 'id')->get();
    }
}
