<?php

namespace App\Http\Controllers;

use App\Models\PasswordCreates;
use App\User;
use Illuminate\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * @package App\Http\Controllers
 */
class Authentication extends Controller
{
    public function check(): Http\JsonResponse
    {
        return response()->json(['auth' => Auth::guard('api')->check()]);
    }

    public function createPassword(): Http\JsonResponse
    {
        $email = Str::replaceFirst(' ', '+', request()->query('email'));
        $token = request()->query('token');

        $tokens = DB::table('password_creates')
            ->where('email', '=', $email)
            ->first();

        if ($tokens === null || Hash::check($token, $tokens->token) === false) {
            return response()->json(
                ['message'=>'Sorry, the email and token you supplied are invalid'],
                401
            );
        }

        $validator = Validator::make(
            request()->only(['password', 'password_confirmation']),
            [
                'password' => [
                    'required',
                    'min:10'
                ],
                'password_confirmation' => [
                    'required',
                    'same:password',
                ]
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

        try {

            $user = User::with([])
                ->where('email', '=', $email)
                ->first();

            if ($user !== null) {
                $user->password = Hash::make(request()->input('password'));
                $user->save();

                DB::table('password_creates')
                    ->where('email', '=', request()->input(['email']))
                    ->delete();

                return response()->json(['message' => 'Password created'], 201);
            }

            return response()->json(['message' => 'Unable to fetch your account to create password, please try again later'], 500);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Unable to create password, please try again later'], 500);
        }
    }

    public function login(): Http\JsonResponse
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

    public function register(): Http\JsonResponse
    {
        $validator = Validator::make(
            request()->all(),
            [
                'name' => 'required',
                'email' => 'required|email',
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

        try {
            $email = request()->input('email');

            $user = new User();
            $user->name = request()->input('name');
            $user->email = request()->input('email');
            $user->password = Hash::make(Str::random(20));
            $user->save();

            $create_token = Str::random(20);

            $password = new PasswordCreates();
            $password->email = $email;
            $password->token = Hash::make($create_token);
            $password->created_at = now()->toDateTimeString();
            $password->save();

        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to create the account, please try again later'], 500);
        }

        return response()->json(
            [
                'message' => "Your account has been created, please POST 'password' and 'confirm_password' to " .
                    'v2/auth/create-password?token=' . $create_token .
                    '&email=' . $email . ' to create your password'
            ],
            201
        );
    }

    public function user(): Http\JsonResponse
    {
        if (
            auth()->guard('api')->check() === true &&
            $user = auth()->guard('api')->user() !== null
        ) {
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
