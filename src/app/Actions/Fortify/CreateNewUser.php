<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use App\Http\Requests\RegisterRequest;

class CreateNewUser implements CreatesNewUsers
{
    public function create(array $input): User
    {
        $validator = Validator::make(
            $input,
            (new RegisterRequest())->rules(),
            (new RegisterRequest())->messages()
        );

        if ($validator->fails()) {
            throw \Illuminate\Validation\ValidationException::withMessages(
                $validator->errors()->toArray()
            );
        }

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);
    }
}
