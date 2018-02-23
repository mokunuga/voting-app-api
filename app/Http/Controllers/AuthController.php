<?php

namespace App\Http\Controllers;

use App\Permission;
use App\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\User;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function index()
    {
//        return User::all();
        return response()->json(User::all());
    }

    public function register(Request $request)
    {
        $rules = [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6|confirmed',
            'NIN' => 'required|integer|unique:users',
        ];
        $input = $request->only(
            'name',
            'email',
            'password',
            'password_confirmation',
            'NIN'
        );
        $validator = Validator::make($input, $rules);
        if($validator->fails()) {
            $error = $validator->messages();
            return response()->json(['success'=> false, 'error'=> $error]);
        }

        $user = User::create(['name' => $request->name,
                              'email' => $request->email,
                              'NIN' => $request->NIN,
                              'password' => Hash::make($request->password)]);
        $role = Role::where('name', 'user')->first();
        $user->roles()->attach($role->id);

        return response()->json(['success'=> true, 'message'=> 'Thanks for signing up!', 'data' =>$user]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = \auth()->attempt($credentials)) {
                return response()->json(['success' => false, 'error' => 'Invalid Credentials. Please make sure you entered the right information.'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['success' => false, 'error' => 'could_not_create_token'], 500);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $user = auth()->user();
        if($user) {
            return response()->json([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'NIN' => $user->NIN,
                'role' => $user->roles()->get()[0]->name]);
        } else {
            return response()->json(['status' => false, 'error' => 'No Authenticated user found.']);
        }
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();
        return response()->json(['success' => true, 'message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondWithToken($token)
    {
        return response()->json([
            'success' => true,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    public function createRole(Request $request){
        $rules = ['name' => 'required|max:255', 'display_name' => 'required|max:255'];
        $input = $request->only('name', 'display_name');

        $validator = Validator::make($input, $rules);
        if($validator->fails()) {
            $error = $validator->messages()->toJson();
            return response()->json(['success'=> false, 'error'=> $error]);
        }
        $role = new Role();
        $role->name = $request->name;
        $role->display_name = $request->display_name;
        $role->save();

        return response()->json(['status' => 'created', 'data' => $role]);
    }

    public function createPermission(Request $request){
        $rules = ['name' => 'required|max:255', 'display_name' => 'required|max:255'];
        $input = $request->only('name', 'display_name');

        $validator = Validator::make($input, $rules);
        if($validator->fails()) {
            $error = $validator->messages()->toJson();
            return response()->json(['success' => false, 'error' => $error]);
        }

        $permission = new Permission();
        $permission->name = $request->name;
        $permission->display_name = $request->display_name;
        $permission->save();

        return response()->json(['status' => 'created', 'data' => $permission]);
    }

    public function assignRole(Request $request){
        $user = User::where('email', '=', $request->input('email'))->first();
        $role = Role::where('name', '=', $request->input('role'))->first();
        $user->roles()->attach($role->id);

        return response()->json(['status' => 'created', 'data' => $role]);
    }

    public function attachPermission(Request $request){
        $role = Role::where('name', '=', $request->input('role'))->first();
        $permission = Permission::where('name', '=', $request->input('name'))->first();
        $role->attachPermission($permission);

        return response()->json(['status' => 'created', 'data' => $role]);
    }
}
