<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * @package App\Http\Controllers
 */
class PassportView extends Controller
{
    /**
     * login to the API and create an access token
     *
     * @return Http\JsonResponse
     */
    public function login()
    {
        if (
            Auth::attempt(
                [
                    'email' => request('email'),
                    'password' => request('password')
                ]
            ) === true
        ) {
            $user = Auth::user();

            if ($user !== null) {

                $token = request()->user()->createToken('costs-to-expect-api');

                return response()->json(
                    [
                        'type' => 'Bearer',
                        'token' => $token->plainTextToken,
                    ],
                    201
                );
            }

            return response()->json(['message' => 'Unauthorised, credentials invalid'], 401);
        }

        return response()->json(['message' => 'Unauthorised, credentials invalid'], 401);
    }

    public function check()
    {
        return response()->json(['auth' => Auth::guard('api')->check()]);
    }

    /**
     * Register with the API will return the token
     *
     * @return Http\JsonResponse
     */
    public function register()
    {
        $validator = Validator::make(
            request()->all(),
            [
                'name' => 'required',
                'email' => 'required|email',
                'password' => 'required',
                'password_confirmation' => 'required|same:password',
            ]
        );

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => 'Validation error, please review the below',
                    'fields' => $validator->errors()
                ],
                422
            );
        }

        $input = request()->all();

        $input['password'] = Hash::make($input['password']);

        try {
            User::create($input);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to create user'], 500);
        }

        return response()->json([], 204);
    }

    /**
     * Return user details
     *
     * @return Http\JsonResponse
     */
    public function user()
    {
        if (auth()->guard('api')->check() === true && $user = auth()->guard('api')->user() !== null) {

            $user = auth()->guard('api')->user();

            if ($user !== null) {

                $user = [
                    'id' => $this->hash->user()->encode($user->id),
                    'name' => $user->name,
                    'email' => $user->email
                ];

                return response()->json($user);
            }

            return response()->json(['message' => 'Unauthorised, credentials invalid'], 403);

        }

        return response()->json(['message' => 'Unauthorised, credentials invalid'], 403);
    }
}
