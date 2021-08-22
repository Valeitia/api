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

        return $this->successResponse(null, $user->addItem($request->get('item'), $request->get('amount')), 200);
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

        return $this->successResponse(null, $user->removeItem($request->get('item'), $request->get('amount')), 200);
    }

    public function equip(Request $request) {
        $validator = Validator::make($request->all(), [
            "discord_id" => "required|integer",
            "inventory_id" => "required|integer"
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        if (!Inventory::where('id', $request->get('inventory_id'))->exists()) {
            return $this->errorResponse("That item doesn't exist.", 200);
        }

        $inv = Inventory::find($request->get('inventory_id'));

        if ($inv->user()->discord_id == $request->get('discord_id')) {
            $inv->user()->equip($inv);

            return $this->successResponse(null, "Successfully equipped {$inv->item()->name}.");
        }

        return $this->errorResponse("You do not own #{$inv->id}", 200);
    }
}
