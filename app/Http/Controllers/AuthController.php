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
    /**
     * The secret key used for generating the password reset token.
     *
     * This key should be kept secret and never exposed to users or stored in the database.
     *
     * @var string
     */
    private $secretKey = "qQKPjndxljuYQi/POiXJa8O19nVO/vTf/DpXO541g=qQKPjndxljuYQi/POiXJa8O19nVO/vTf/DpXO541g=";
    
    /**
     * Register a new user.
     *
     * @param Request $request The HTTP request containing the user data.
     * @return \Illuminate\Http\JsonResponse The JSON response containing the user data and a success message.
     */
    public function register(Request $request)
    {
        // Get all the fields from the request
        $fields = $request->all();

        // Validate the fields
        $errors = Validator::make($fields, [
            'email' => [
                'required','string','email','lowercase','max:255',
                Rule::unique(User::class), // Ensure the email is unique
            ],
            'password' => [
                'required','min:6','max:8',
                Password::min(8)->letters()->numbers()->symbols(), // Ensure the password meets the requirements
            ],
        ]);

        // If there are any validation errors, return a 422 response with the errors
        if ($errors->fails()) {
            return response($errors->errors()->all(), 422);
        }

        // Create the user data array
        $user_data = [
            'email' => $fields['email'], // Set the email
            'password' => bcrypt($fields['password']), // Hash the password
            'isValidEmail' => User::IS_INVALID_EMAIL, // Set the email validation status
            'remember_token' => Generators::generateRandomCode(), // Generate a random code for the remember token
        ];
        
        // Create a new user with the user data
        $user = User::create($user_data);
        
        // Dispatch the NewUserCreated event with the user
        NewUserCreated::dispatch($user);
        
        // Return a 200 response with the user data and a success message
        return response(['user' => $user, 'message' => 'user created'], 200);
    }
    
    /**
     * Handle user login.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        // Get all the fields from the request
        $fields = $request->all();

        // Validate the fields
        $errors = Validator::make($fields, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // If there are any validation errors, return a 422 response with the errors
        if ($errors->fails()) {
            return response($errors->errors()->all(), 422);
        }

        // Retrieve the user based on the email
        $user = User::where('email', $fields['email'])->first();

        // Check if the user exists and email is not yet validated
        if (!is_null($user)) {
            if (intval($user->isValidEmail) !== User::IS_VALID_EMAIL) {
                NewUserCreated::dispatch($user);
                return response([
                    'message' => 'We send you an email verification !',
                    'isLoggedIn' => false
                ], 422);
            }
        }

        // Check if user or password is invalid
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response(['message' => 'email or password invalid', 'isLoggedIn' => false], 422);
        }

        // Create a token for the user
        $token = $user->createToken($this->secretKey)->plainTextToken;

        // Return a 200 response with user data, token, and login message
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
    
    /**
     * Logout a user.
     *
     * @param \Illuminate\Http\Request $request The HTTP request containing the user ID.
     * @return \Illuminate\Http\Response
     */
    public function logoutUser(Request $request)
    {
        // Delete the personal access token for the specified user ID
        DB::table('personal_access_tokens')
            ->where('tokenable_id', $request->userId)
            ->delete();

        // Return a success message with the logout status
        return response([
            'message' => 'logout user',
        ], 200);
    }
    
    /**
     * Validate the user's email.
     *
     * @param string $token The token to validate the email.
     * @return \Illuminate\Http\RedirectResponse Redirects to the login page.
     */
    public function validEmail($token)
    {
        // Update the user's email validation status and the email verification timestamp
        User::where('remember_token', $token)
            ->update([
                'isValidEmail' => User::IS_VALID_EMAIL,
                'email_verified_at' => DB::raw('NOW()')
            ]);

        // Redirect the user to the login page
        return redirect('/app/login');
    }
}
