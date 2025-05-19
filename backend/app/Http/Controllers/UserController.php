<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController
{

    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $user = User::where('email', $data['email'])->exists();

        if ($user) {
            return response(['error' => 'User already exists'], 409);
        }

        $user = User::create($data);
        $accessToken = auth()->tokenById($user->id);

        return response(['user' => $user, 'access_token' => $accessToken]);
    }

    public function update(UpdateRequest $request)
    {
        $data = $request->validated();

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['new_password']);
            unset($data['new_password']);
            unset($data['new_password_confirmation']);
        }

        $result = $request->user()->update($data);

        return response(['data' => $result]);
    }

    public function me(Request $request)
    {
        return $request->user()->only(['name', 'role']);
    }
}
