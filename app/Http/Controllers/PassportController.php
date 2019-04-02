<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * Register users and login
 *
 * @package App\Http\Controllers
 */
class PassportController extends Controller
{
    public $successStatus = 200;

    /**
     * login to the API
     *
     * @return \Illuminate\Http\Response
     */
    public function login(){
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
            $user = Auth::user();
            $success['token'] =  $user->createToken('costs-to-expect')->accessToken;
            return response()->json($success, $this->successStatus);
        } else {
            return response()->json(['message'=>'Unauthorised'], 401);
        }
    }

    /**
     * Register with the API
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'password_confirmation' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => 'Validation error',
                    'fields' => $validator->errors()
                ],
                422
            );
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('costs-to-expect')->accessToken;
        $success['name'] =  $user->name;

        return response()->json($success, $this->successStatus);
    }

    /**
     * Return user details
     *
     * @return \Illuminate\Http\Response
     */
    public function user()
    {
        $user = Auth::user();
        return response()->json($user, $this->successStatus);
    }
}
