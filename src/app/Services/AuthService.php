<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;
use JetBrains\PhpStorm\ArrayShape;
use Laravel\Sanctum\PersonalAccessToken;

class AuthService
{
    const TOKEN_NAME = 'Api Token';

    /**
     * This method is used to log in user
     * @param string $mobile
     * @param string $password
     * @return array
     * @throws AuthenticationException
     */
    public function login(string $mobile, string $password): array
    {
        $user = User::query()->where('mobile', $mobile)->first();

        if ($user && Hash::check($password, $user->getAuthPassword())) {
            return [
                'token' => $user->createToken(self::TOKEN_NAME)->plainTextToken,
                'type'  => 'Bearer'
            ];
        }
        throw new AuthenticationException();
    }


    public function register(array $inputs): array
    {
        $inputs['password'] = Hash::make($inputs['password']);
        $user               = User::create($inputs);
        return [
            'token' => $user->createToken(self::TOKEN_NAME)->plainTextToken,
            'type'  => 'Bearer'
        ];
    }

    /**
     * this method logs out users by removing token
     * @return bool|null
     */
    public function logout(): ?bool
    {
        /** @var PersonalAccessToken $token */
        $token = auth()->guard()->user()->currentAccessToken();
        return $token->delete();
    }

    public function getUserFromMobile(string $mobile): User
    {
        return User::where('mobile', $mobile)->firstOrCreate([
            'mobile'   => $mobile,
            'password' => Hash::make($mobile),
        ]);
    }
}
