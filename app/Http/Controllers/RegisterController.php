<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class RegisterController extends Controller
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request): JsonResponse
    {

        if (!json_decode(Auth::user()->scopes)->users->create) {
            return $this->sendError('You do not have permissions to access this resource.', ['error' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        if (User::where('email', $request->email)->exists()) {
            return $this->sendError('User already exist.', ['error' => 'Duplicated'], 409);
        }

        $input = $request->all();
        $input['scopes'] = json_encode($request->scopes);
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('YonluiApp')->plainTextToken;
        $success['name'] =  $user->name;
        $success['scopes'] =  json_decode($user->scopes);

        return $this->sendResponse($success, 'User register successfully.');
    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $success['token'] =  $user->createToken('YonluiApp')->plainTextToken;
            $success['name'] =  $user->name;
            $success['scopes'] =  json_decode($user->scopes);

            return $this->sendResponse($success, 'User login successfully.');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->sendResponse("Success", 'User logout successfully.');
    }
}
