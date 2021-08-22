<?php

namespace App\Http\Controllers;

use App\Models\User;

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

        return $this->successResponse(User::where('discord_id', $request->get('discord_id'))->first(), "Profile fetched successfully.", 200);
    }
}
