<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    // use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    // protected $redirectTo = RouteServiceProvider::HOME;
    // use ResetsPasswords;

    protected function resetPassword($user, $password)
    {
        // Auth::guard('api-employees');
        $user->password =Crypt::encryptString($password);
        $user->save();
        //  Auth::guard('api-employees');
    }
     /**
     * Get the broker to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    protected function broker()
    {
        return Password::broker('employees');
    }


   /**
     * Get the password reset credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only(
            'email', 'password', 'token'
        );
    }

    public function reset(Request $request)
{
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8',
    ]);

    $user = Employee::where('email', $request->email)->first();
    // return $user;

    if (!$user) {
        return response()->json([
            'message' => 'We can\'t find a user with that email address.',
        ], 404);
    }

    $response = $this->broker('employees')->reset(
        $this->credentials($request),
        function ($user, $password) {
            $this->resetPassword($user, $password);
        }
    );
// return $response;
    if ($response == Password::PASSWORD_RESET) {
        return response()->json([
            'message' => 'Your password has been reset!',
        ]);
    } else {
        return response()->json([
            'message' => 'Unable to reset password, please try again later.',
        ], 500);
    }
}
}
