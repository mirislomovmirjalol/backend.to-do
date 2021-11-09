<?php

namespace App\Http\Controllers;

use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    /**
     * @throws GuzzleException
     */
    public function login(Request $request)
    {
        $http = new Client;

        try {
            $response = Http::asForm()->post(env('PASSPORT_LOGIN_ENDPOINT'), [
                'grant_type' => 'password',
                'client_id' => env('PASSPORT_CLIENT_ID'),
                'client_secret' => env('PASSPORT_CLIENT_SECRET'),
                'username' => $request->username,
                'password' => $request->password,
            ]);


            return $response->getBody();
        } catch (BadResponseException $e) {
            if ($e->getCode() == 400) {
                return Response()->json('Invalid Request. Please enter a username or a password.', $e->getCode());
            } else if ($e->getCode() == 401) {
                return Response()->json('Your credentials are incorrect. Please try again', $e->getCode());
            } else if ($e->getCode() == 404) {
                return Response()->json('Not found', $e->getCode());
            }

            return Response()->json('Something wrong on the server', $e->getCode());
        }
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        return User::query()->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
    }

    public function logout(): JsonResponse
    {
        auth()->user()->tokens->each(function ($token, $key) {
            $token->delete();
        });

        return Response()->json('Logged out successfully', 200);
    }
}
