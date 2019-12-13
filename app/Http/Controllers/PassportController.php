<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * Register users and login
 *
 * @package App\Http\Controllers
 */
class PassportController extends Controller
{
    /**
     * login to the API and create a token
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
        ) === true) {
            $token = Auth::user()->createToken('costs-to-expect-api');

            return response()->json(
                [
                    'type' => 'Bearer',
                    'token' => $token->accessToken,
                    'created' => $token->token->created_at,
                    'updated' => $token->token->updated_at,
                    'expires' => $token->token->expires_at
                ],
                201
            );
        } else {
            return response()->json(['message' => 'Unauthorised, credentials invalid'], 401);
        }
    }

    /**
     * Register with the API will return the token
     *
     * @param Request $request
     *
     * @return Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
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

        $input = $request->all();

        $input['password'] = bcrypt($input['password']);
        User::create($input);

        $token = Auth::user()->createToken('costs-to-expect-api');

        $success = [
            'token' => [
                'type' => 'Bearer',
                'token' => $token->accessToken,
                'created' => $token->token->created_at,
                'updated' => $token->token->updated_at,
                'expires' => $token->token->expires_at
            ]
        ];

        return response()->json($success, 201);
    }

    /**
     * Return user details
     *
     * @return Http\JsonResponse
     */
    public function user()
    {
        $user = Auth::user();

        return response()->json($user, 200);
    }
}
