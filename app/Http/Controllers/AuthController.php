<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Referral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function create(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            "discord_id" => "required|integer",
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        if (User::where('discord_id', $request->get('discord_id'))->exists()) {
            return $this->errorResponse("Account already exists, logging in.", 201);
        }

        $user_referral_code = null;

        while ($user_referral_code == null || User::where('referral_code', $user_referral_code)->exists()) {
            $user_referral_code = Str::random(6);
        }

        $user = User::create([
            'discord_id' => $request->get('discord_id'),
            'referral_code' => $user_referral_code
        ]);

        if ($request->get('referral') != null) {
            Referral::create([
                'user' => $user->id,
                'code' => $request->get('referral')
            ]);
        }

        return $this->successResponse($user, "Your account has been created!", 201);
    }
}
