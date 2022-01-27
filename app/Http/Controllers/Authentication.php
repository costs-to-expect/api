<?php

namespace App\Http\Controllers;

use App\Notifications\ForgotPassword;
use App\Notifications\Registered;
use App\Option\Auth\Check;
use App\Option\Auth\CreateNewPassword;
use App\Option\Auth\CreatePassword;
use App\Option\Auth\Login;
use App\Option\Auth\Register;
use App\Option\Auth\UpdatePassword;
use App\Option\Auth\UpdateProfile;
use App\User;
use Exception;
use Illuminate\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class Authentication extends \Illuminate\Routing\Controller
{
    protected \App\Request\Hash $hash;

    public function __construct()
    {
        $this->hash = new \App\Request\Hash();
    }

    public function check(): Http\JsonResponse
    {
        return response()->json(['auth' => Auth::guard('api')->check()]);
    }

    public function optionsCheck(): Http\JsonResponse
    {
        $response = new Check([]);

        return $response->create()->response();
    }

    public function createPassword(Request $request): Http\JsonResponse
    {
        $email = Str::replaceFirst(' ', '+', urldecode($request->query('email')));
        $token = $request->query('token');

        $tokens = DB::table('password_creates')
            ->where('email', '=', $email)
            ->first();

        if ($tokens === null || Hash::check($token, $tokens->token) === false) {
            return response()->json(
                [
                    'message'=>'Sorry, the email and or token you supplied are invalid'
                ],
                401
            );
        }

        $validator = Validator::make(
            $request->only(['password', 'password_confirmation']),
            [
                'password' => [
                    'required',
                    'min:12'
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
                $user->password = Hash::make($request->input('password'));
                $user->save();

                DB::table('password_creates')
                    ->where('email', '=', $request->input(['email']))
                    ->delete();

                return response()->json([], 204);
            }

            return response()->json(['message' => 'Unable to fetch your account to create password, please try again later'], 404);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Unable to create password, please try again later'], 500);
        }
    }

    public function optionsCreatePassword(): Http\JsonResponse
    {
        $response = new CreatePassword([]);

        return $response->create()->response();
    }

    public function createNewPassword(Request $request): Http\JsonResponse
    {
        $email = Str::replaceFirst(' ', '+', urldecode($request->query('email')));
        $token = $request->query('token');

        $tokens = DB::table('password_resets')
            ->where('email', '=', $email)
            ->first();

        if ($tokens === null || Hash::check($token, $tokens->token) === false) {
            return response()->json(
                [
                    'message'=>'Sorry, the email and token you supplied are invalid'
                ],
                404
            );
        }

        $validator = Validator::make(
            $request->only(['password', 'password_confirmation']),
            [
                'password' => [
                    'required',
                    'min:12'
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
                $user->password = Hash::make($request->input('password'));
                $user->save();

                DB::table('password_resets')
                    ->where('email', '=', $request->input(['email']))
                    ->delete();

                return response()->json([], 204);
            }

            return response()->json(['message' => 'Unable to fetch your account to create password, please try again later'], 500);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Unable to create password, please try again later'], 500);
        }
    }

    public function optionsCreateNewPassword(): Http\JsonResponse
    {
        $response = new CreateNewPassword([]);

        return $response->create()->response();
    }

    public function forgotPassword(Request $request): Http\JsonResponse
    {
        $validator = Validator::make(
            $request->only(['email']),
            [
                'email' => 'required|email',
            ]
        );

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => 'Validation error, please review the errors below',
                    'fields' => $validator->errors()
                ],
                422
            );
        }

        $email = $request->input('email');

        $user = User::with([])
            ->where('email', '=', $email)
            ->first();

        if ($user !== null) {
            try {
                $create_token = Str::random(20);

                DB::table('password_resets')->updateOrInsert(
                    [
                        'email' => $email,
                    ],
                    [
                        'email' => $email,
                        'token' => Hash::make($create_token)
                    ]
                );

                if (app()->environment() === 'production' && $request->query('send') === null) {
                    $user->notify(new ForgotPassword($user, $create_token));
                }
            } catch (Exception $e) {
                Log::error($e->getMessage());
                return response()->json(['error' => 'Unable to process your forgot password request, please try again later'], 500);
            }

            return response()->json(
                [
                    'message' => 'Request received, please check your email for instructions on how to create your new password',
                    'uris' => [
                        'create-new-password' => [
                            'uri' => Config::get('api.app.version.prefix') . '/auth/create-new-password?token=' . $create_token . '&email=' . $email,
                            'parameters' => [
                                'token' => $create_token,
                                'email' => $email
                            ]
                        ]
                    ]
                ],
                201
            );
        }

        return response()->json(['message' => 'Unable to fetch your user account, please try again later'], 404);
    }

    public function optionsForgotPassword(): Http\JsonResponse
    {
        $response = new \App\Option\Auth\ForgotPassword([]);

        return $response->create()->response();
    }

    public function login(Request $request): Http\JsonResponse
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

                $request->user()->revokeOldTokens();

                $token = $request->user()->createToken('costs-to-expect-api');
                return response()->json(
                    [
                        'id' => $this->hash->user()->encode($user->id),
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

    public function optionsLogin(): Http\JsonResponse
    {
        $response = new Login([]);

        return $response->create()->response();
    }

    public function logout(): Http\JsonResponse
    {
        Auth::logout();

        return response()->json(['message' => 'Account signed out'], 200);
    }

    public function register(Request $request): Http\JsonResponse
    {
        $validator = Validator::make(
            $request->all(),
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
            $email = $request->input('email');

            $user = new User();
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->password = Hash::make(Str::random(20));
            $user->save();

            $create_token = Str::random(20);

            DB::table('password_creates')->updateOrInsert(
                [
                    'email' => $email,
                ],
                [
                    'email' => $email,
                    'token' => Hash::make($create_token)
                ]
            );

            if ($request->query('send') === null && app()->environment() === 'production') {
                $user->notify(new Registered($user, $create_token));
            }

        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => 'Unable to create the account, please try again later'], 500);
        }

        return response()->json(
            [
                'message' => 'Account created, please check you email for information on how to create your password',
                'uris' => [
                    'create-password' => [
                        'uri' => Config::get('api.app.version.prefix') . '/auth/create-password?token=' . $create_token . '&email=' . $email,
                        'parameters' => [
                            'token' => $create_token,
                            'email' => $email
                        ]
                    ]
                ]
            ],
            201
        );
    }

    public function optionsRegister(): Http\JsonResponse
    {
        $response = new Register([]);

        return $response->create()->response();
    }

    public function updatePassword(Request $request): Http\JsonResponse
    {
        $validator = Validator::make(
            $request->only(['password', 'password_confirmation']),
            [
                'password' => [
                    'required',
                    'min:12'
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
                    'message' => 'Validation error, please review the messages',
                    'fields' => $validator->errors()
                ],
                422
            );
        }

        $user = auth()->guard('api')->user();

        if ($user !== null) {
            $user->password = Hash::make($request->input('password'));
            $user->save();

            return response()->json([], 204);
        }

        return response()->json(['message' => 'Unauthorised, credentials invalid'], 401);
    }

    public function optionsUpdateProfile(): Http\JsonResponse
    {
        $user = auth()->guard('api')->user();

        $response = new UpdateProfile(['view'=> $user !== null && $user->id !== null, 'manage'=> $user !== null && $user->id !== null]);

        return $response->create()->response();
    }

    public function optionsUpdatePassword(): Http\JsonResponse
    {
        $user = auth()->guard('api')->user();

        $response = new UpdatePassword(['view'=> $user !== null && $user->id !== null, 'manage'=> $user !== null && $user->id !== null]);

        return $response->create()->response();
    }

    public function updateProfile(Request $request): Http\JsonResponse
    {
        $validator = Validator::make(
            $request->only(['name', 'email']),
            [
                'name' => [
                    'sometimes'
                ],
                'email' => [
                    'sometimes',
                    'email'
                ]
            ]
        );

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => 'Validation error, please review the messages',
                    'fields' => $validator->errors()
                ],
                422
            );
        }

        $user = auth()->guard('api')->user();

        if ($user !== null) {
            $fields = [];
            if ($request->input('name') !== null) {
                $fields['name'] = $request->input('name');
            }
            if ($request->input('email') !== null) {
                $fields['email'] = $request->input('email');
            }

            if (count($fields) === 0) {
                return response()->json(['message' => 'You have provided any fields to change'], 400);
            }

            try {
                foreach ($fields as $field => $value) {
                    $user->$field = $value;
                }

                $user->save();

            } catch (Exception $e) {
                Log::error($e->getMessage());
                return response()->json(['message' => 'Unable to update your profile, please try again'], 401);
            }

            return response()->json([], 204);
        }

        return response()->json(['message' => 'Unauthorised, credentials invalid'], 401);
    }

    public function user(): Http\JsonResponse
    {
        $user = auth()->guard('api')->user();

        if ($user !== null) {

            $tokens = [];
            foreach ($user->tokens as $token) {
                $tokens[] = [
                    'id' => $token->id,
                    'name' => $token->name,
                    'token' => $token->token,
                    'created' => $token->created_at,
                    'last_used_at' => $token->last_used_at
                ];
            }

            $user = [
                'id' => $this->hash->user()->encode($user->id),
                'name' => $user->name,
                'email' => $user->email,
                'tokens' => $tokens
            ];

            return response()->json($user);
        }

        return response()->json(['message' => 'Unauthorised, credentials invalid'], 401);
    }

    public function optionsUser(): Http\JsonResponse
    {
        $user = auth()->guard('api')->user();

        $response = new \App\Option\Auth\User(['view'=> $user !== null && $user->id !== null]);

        return $response->create()->response();
    }
}
