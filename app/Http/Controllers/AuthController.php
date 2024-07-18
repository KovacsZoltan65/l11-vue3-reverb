<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use App\Events\NewUserCreated;
use App\Support\Generators;

class AuthController extends Controller
{
    private $secretKey = "qQKPjndxljuYQi/POiXJa8O19nVO/vTf/DpXO541g=qQKPjndxljuYQi/POiXJa8O19nVO/vTf/DpXO541g=";
    
    public function register(Request $request)
    {
        $fields = $request->all();

        $errors = Validator::make($fields, [
            'email' => [
                'required','string','email','lowercase','max:255',
                Rule::unique(User::class),
            ],
            'password' => [
                'required','min:6','max:8',
                Password::min(8)->letters()->numbers()->symbols(),
            ],
        ]);

        if ($errors->fails()) {
            return response($errors->errors()->all(), 422);
        }

        $user_data = [
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']),
            'isValidEmail' => User::IS_INVALID_EMAIL,
            'remember_token' => Generators::generateRandomCode(),
        ];
        
        $user = User::create($user_data);
        
        NewUserCreated::dispatch($user);
        
        return response(['user' => $user, 'message' => 'user created'], 200);
    }
    
    public function login(Request $request)
    {

        $fields = $request->all();

        $errors = Validator::make($fields, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($errors->fails()) {
            return response($errors->errors()->all(), 422);
        }

        $user = User::where('email', $fields['email'])->first();

        if (!is_null($user)) {

            if (intval($user->isValidEmail) !== User::IS_VALID_EMAIL) {
                NewUserCreated::dispatch($user);
                return response([
                    'message' => 'We send you an email verification !',
                    'isLoggedIn' => false
                ],422);
            }
        }

        if (!$user || !Hash::check($fields['password'], $user->password)) {

            return response(['message' => 'email or password invalid', 
            'isLoggedIn' => false], 422);
        }


        $token = $user->createToken($this->secretKey)->plainTextToken;
        return response(
            [
                'user' => $user,
                'message' => 'loggedin',
                'token' => $token,
                'isLoggedIn' => true

            ],
            200
        );
    }
    
    public function logoutUser(Request $request){

        DB::table('personal_access_tokens')
        ->where('tokenable_id',$request->userId)
        ->delete();

        return response(['message' => 'logout user'],200);
    }
    
    public function validEmail($token)
    {

        User::where('remember_token', $token)
            ->update([
                'isValidEmail' => User::IS_VALID_EMAIL,
                'email_verified_at' => DB::raw('NOW()')
            ],
            );

        return redirect('/app/login');
    }
}
