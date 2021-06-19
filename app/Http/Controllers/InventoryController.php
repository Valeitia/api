<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Inventory;
use App\Models\User;
use App\Models\Item;
use Illuminate\Support\Facades\Validator;

class InventoryController extends Controller
{
    public function add(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            "discord_id" => "required|integer",
            "item" => "required|integer",
            "amount" => "required|integer"
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        $user = User::where('discord_id', $request->get('discord_id'))->first();
        $item = Item::find($request->get('item'));

        if ($inv = Inventory::whereIn(['id' => $user->id, 'item' => $request->get('item')])->exists()) {
            $inv->amount += $request->get('amount');
            $inv->save();

            return $this->successResponse(null, "x{$request->get('amount')} {$item->name} added to your inventory", 200);
        }

        $inv = Inventory::create([
           "user" => $user->id,
           "item" => $item->id,
           "amount" => $request->get('amount')
        ]);

        return $this->successResponse(null, "x{$request->get('amount')} {$item->name} added to your inventory", 200);
    }

    public function remove(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            "discord_id" => "required|integer",
            "item" => "required|integer",
            "amount" => "required|integer"
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        $user = User::where('discord_id', $request->get('discord_id'))->first();
        $item = Item::find($request->get('item'));

        if ($inv = Inventory::whereIn(['id' => $user->id, 'item' => $request->get('item')])->exists()) {
            if ($inv->amount < $request->get('amount')) {
                return $this->errorResponse("You do not have x{$inv->amount} {$item->name}", 422);
            }

            $inv->amount -= $request->get('amount');
            $inv->save();

            return $this->successResponse(null, "x{$request->get('amount')} {$item->name} removed from your inventory", 200);
        } else {
            return $this->errorResponse("You do not have x{$inv->amount} {$item->name}", 422);
        }
    }
}
