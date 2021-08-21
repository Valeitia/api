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
}
