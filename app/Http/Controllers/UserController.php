<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Referral;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function profile(Request $request) {
        $validator = Validator::make($request->all(), [
            "discord_id" => "required|integer"
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        $user = User::where('discord_id', $request->get('discord_id'))->first();

        $data = [
            "user" => $user,
        ];

        if ($user->helmet != null) {
            $data["helmet"]["inv"] = $user->helmet();
            $data["helmet"]["item"] = $user->helmet()->item();
        }

        if ($user->chestplate != null) {
            $data["chestplate"]["inv"] = $user->chestplate();
            $data["chestplate"]["item"] = $user->chestplate()->item();
        }

        if ($user->leggings != null) {
            $data["leggings"]["inv"] = $user->leggings();
            $data["leggings"]["item"] = $user->leggings()->item();
        }

        if ($user->boots != null) {
            $data["boots"]["inv"] = $user->boots();
            $data["boots"]["item"] = $user->boots()->item();
        }

        if ($user->weapon != null) {
            $data["weapon"]["inv"] = $user->weapon();
            $data["weapon"]["item"] = $user->weapon()->item();
        }

        return $this->successResponse($data, "Profile fetched successfully.", 200);
    }

    public function refer(Request $request) {
        $validator = Validator::make($request->all(), [
            "discord_id" => "required|integer"
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        $user = User::where('discord_id', $request->get('discord_id'))->first();

        $referrals_count = Referral::where('code', $user->referral_code)->get()->count();

        $data = [
          "code" => $user->referral_code,
          "referrals" => $referrals_count
        ];

        return $this->successResponse($data, "Referrals fetched successfully.", 200);
    }
}
