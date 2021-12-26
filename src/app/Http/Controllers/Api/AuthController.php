<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    //this function show current user
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * This method is used to log in user
     * @param Request     $request
     * @param AuthService $service
     * @return Response
     */
    public function register(Request $request, AuthService $service): Response
    {
        $validated = $request->validate([
            'name'     => ['required', 'string',],
            'mobile'   => ['required', 'regex:/^9\d{9}$/', 'unique:users,mobile',],
            'password' => ['required', 'confirmed', 'min:6',],
        ]);

        return response()->json($service->register($validated));
    }


    /**
     * This method is used to log in user
     * @param Request     $request
     * @param AuthService $service
     * @return Response
     * @throws AuthenticationException
     */
    public function login(Request $request, AuthService $service): Response
    {
        $attr = $request->validate([
            'mobile'   => ['required', 'string', 'regex:/^9\d{9}$/'],
            'password' => ['required', 'string', 'min:6']
        ]);

        return response()->json($service->login($attr['mobile'], $attr['password']));
    }

    /**
     * this method logs out users by removing token
     * @param AuthService $service
     * @return Response
     */
    public function logout(AuthService $service): Response
    {
        $service->logout();
        return response()->noContent();
    }
}
