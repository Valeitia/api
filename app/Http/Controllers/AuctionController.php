<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Inventory;
use App\Models\Auction;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuctionController extends Controller
{
    public function add(Request $request) {
        $validator = Validator::make($request->all(), [
            "discord_id" => "required|integer",
            "inv" => "required|integer",
            "price" => "required|integer"
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        $user = User::where('discord_id', $request->get('discord_id'))->first();
        $item = Inventory::find($request->get('inv')) ?? null;

        if ($item == null) {
            return $this->errorResponse("This item does not exist.", 200);
        }

        if ($item->user != $user->id) {
            return $this->errorResponse("This item does not belong to you.", 200);
        }

        if ($request->get('price') == 0) {
            return $this->errorResponse("You cannot put up a listing for 0 gold.", 200);
        }

        $equippable = ["helmet", "chestplate", "leggings", "boots", "weapon"];
        foreach($equippable as $e) {
            if ($user->{$e} == $item->id) {
                return $this->errorResponse("Please unequip this item from the {$e} slot before creating a listing.", 200);
            }
        }

        $auction = Auction::create([
            "user" => $user->id,
            "inv" => $item->id,
            "price" => $request->get('price')
        ]);

        $item->user = 0;
        $item->save();

        return $this->successResponse($auction, "Successfully listed auction #{$auction->id} for item #{$item->id}", 200);
    }

    public function buy(Request $request) {
        $validator = Validator::make($request->all(), [
            "discord_id" => "required|integer",
            "auction" => "required|integer",
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        $user = User::where('discord_id', $request->get('discord_id'))->first();
        $auction = Auction::find($request->get('auction'));

        if ($auction == null) {
            return $this->errorResponse("This auction does not exist.", 200);
        }

        $item = Inventory::find($auction->inv);

        if ($item == null) {
            return $this->errorResponse("This item does not exist.", 200);
        }

        if ($user->gold < $auction->price) {
            return $this->errorResponse("You do not have enough gold to purchase this listing.", 200);
        }

        if ($item->user == $user->id) {
            return $this->errorResponse("Why...why are you trying to buy your own listing. :(", 200);
        }

        $user->gold -= $auction->price;
        $user->save();

        $item->user = $user->id;
        $item->save();

        $auction->user()->gold += $auction->price;
        $auction->user()->save();

        $auction->delete();
        return $this->successResponse(null, "You have purchased the listing for item #{$item->id}", 200);
    }

    public function listing() {
        $auctions = Auction::all();
        $data = [];

        $i = 0;
        foreach($auctions as $auction) {
            $data[$i]["auction"] = $auction;
            $data[$i]["inv"] = $auction->inv();
            $data[$i]["item"] = $auction->inv()->item();
            $i++;
        }
        return $this->successResponse($data, "Successfully retrieved all listings", 200);
    }

    public function remove(Request $request) {
        $validator = Validator::make($request->all(), [
            "discord_id" => "required|integer",
            "auction" => "required|integer",
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        $user = User::where('discord_id', $request->get('discord_id'))->first();
        $auction = Auction::find($request->get('auction'));

        if ($auction == null) {
            return $this->errorResponse("This auction does not exist.", 200);
        }

        $item = Inventory::where('id', $auction->inv)->first();

        if ($auction->user != $user->id) {
            return $this->errorResponse("This item does not belong to you.", 200);
        }

        $item->user = $user->id;
        $item->save();

        $auction->delete();

        return $this->successResponse(null, "The listing for item {$item->id} has been removed.", 200);
    }
}
