<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

        $user = User::create([
            'discord_id' => $request->get('discord_id')
        ]);

        return $this->successResponse($user, "Your account has been created!", 201);
    }
}
