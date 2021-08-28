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

    public function inventory(Request $request) {
        $validator = Validator::make($request->all(), [
            "discord_id" => "required|integer",
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        $user = User::where('discord_id', $request->get('discord_id'))->first();
        $inventory = Inventory::where("user", $user->id)->get();
        $data = [];

        $i = 0;
        foreach ($inventory as $item) {
            $data[$i]["inv"] = $item;
            $data[$i]["item"] = $item->item();
            $i++;
        }

        return $this->successResponse($data, "Inventory retrieved.", 200);
    }

    public function item(Request $request) {
        $validator = Validator::make($request->all(), [
            "inv" => "required|integer",
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        $inv = Inventory::find($request->get('inv'));

        if ($inv == null) {
            return $this->errorResponse("That item doesn't exist.", 200);
        }

        $data = [
            "inv" => $inv,
            "item" => $inv->item(),
            "stats" => json_decode($inv->stats, true),
            "user" => $inv->user()
        ];

        return $this->successResponse($data, "Item retrieved successfully", 200);
    }
}
