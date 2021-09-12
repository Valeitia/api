<?php

namespace App\Http\Controllers;

use App\Models\Battle;
use App\Models\Enemy;
use App\Models\Inventory;
use App\Models\User;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BattleController extends Controller
{
    public function init(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            "discord_id" => "required|integer"
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 200);
        }

        $user = User::where('discord_id', $request->get('discord_id'))->first();

        if (!$user->hasEnoughEnergy(3)) {
            return $this->errorResponse("You do not have enough energy to battle.", 200);
        }

        $battle = Battle::where('user', $user->id)->first() ?? null;

        if (!Battle::where('user', $user->id)->exists()) {
            $possible_enemies = null;
            $level_gap = 3;

            while ($possible_enemies === null) {
                if (Enemy::whereBetween('level', [$user->level - $level_gap, $user->level + $level_gap])->exists()) {
                    $possible_enemies = Enemy::whereBetween('level', [$user->level - $level_gap, $user->level + $level_gap])->get();
                } else {
                    $level_gap += 2;
                }
            }

            $possible_enemies_count = count($possible_enemies);
            $enemy = $possible_enemies[rand(0, $possible_enemies_count - 1)];

            $battle = Battle::create(['user' => $user->id, 'enemy' => $enemy->id, 'health' => $enemy->health]);
        }

        $enemy = Enemy::find($battle->enemy);

        if ($battle->health > 0) {
            $user_combined_damage = 0;
            $enemy_combined_damage = 0;
            $user_hit_chance = 100;
            $user_crit_chance = 0;

            $user_combined_damage += $user->strength;

            if ($user->level < $enemy->level) {
                $user_hit_chance -= ($enemy->level - $user->level) * 5;
            }

            if ($user->strength > $enemy->strength) {
                $user_combined_damage *= round($user->strength / $enemy->strength);
                $user_crit_chance += ($user->strength - $enemy->strength) * 5;
                $enemy_combined_damage += $enemy->strength * ($enemy->strength / $user->strength);
            } else if ($user->strength === $enemy->strength) {
                $user_combined_damage += $user->strength;
                $enemy_combined_damage += $enemy->strength;
            } else {
                $enemy_combined_damage += ($enemy->strength - $user->strength);
            }

            if ($user->dexterity < $enemy->dexterity) {
                $user_hit_chance -= ($enemy->level - $user->level) * 5;
            }

            $user_combined_damage = rand(round($user_combined_damage / 1.2), round($user_combined_damage * 1.2));
            $enemy_combined_damage = rand(round($enemy_combined_damage / 1.2), round($enemy_combined_damage * 1.2));

            if ($user_crit_chance <= rand(0, 100)) {
                $user_combined_damage = round($user_combined_damage * 2);
            }

            $data = [
                "battle" => $battle,
                "user" => $user,
                "enemy" => $enemy,
                "user_hit_chance" => $user_hit_chance,
                "user_damage" => $user_combined_damage,
                "enemy_damage" => $enemy_combined_damage,
                "drops" => []
            ];

            $user->health -= $enemy_combined_damage;
            $user->energy -= 3;
            $user->save();

            $battle->health -= $user_combined_damage;
            $battle->save();

            if ($battle->health <= 0) {
                $gold = rand(round($enemy->gold / 2), round($enemy->gold * 2));

                $user->gold += $gold;
                $user->exp += $enemy->exp;
                $user->save();

                $data["drops"] += ["gold" => $gold, "exp" => $enemy->exp];

                if ($gear = $battle->dropGear($enemy->level)) {
                    if ($gear != null) {
                        $data["items"] = $gear;
                        $data["items"]["stats"] = json_decode($data["items"]["stats"]);
                        $data["items"]["item"] = Item::find($data["items"]["item"]);
                    }
                }


                if ($user->levelUp()) $data["level_up"] = true;

                $battle->delete();
                return $this->successResponse($data, "Success", 200);
            } else {
                return $this->successResponse($data, "Success", 200);
            }
        } else {
            $battle->delete();
            return $this->errorResponse("Oops, something went wrong.", 200);
        }
    }
}
