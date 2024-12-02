<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use App\Notifications\EmailVerificationNotification;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Ichtrojan\Otp\Otp;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rules\Password as RulesPassword;

class NewPasswordController extends Controller
{
    use ResetsPasswords ;

    private $otp ;


    public function __construct()
    {
        //
        $this->otp =new Otp;


    }


    //Send a link to the user if he forgets the password and the user requests to reset it

    public function forgotPassword(Request $request)
    {

    $email=request('email');
    $user=User::where('email',$email)->first();
    // return $user;
    if($user != null)
{
    $user->notify(new EmailVerificationNotification());
    return response()->json(['status'=>true,'message' => "Reset password Code sent on your email"], 200);}
    else{
        return response()->json(['status'=>false,"message"=>'This email does not exist'], 404);
    }

    }

    // public function reset(Request $request)
    // {
    //     return response()->json(request('token'));
    // }

 // When the user clicks on the link that was sent to his email
 //and enters the new password and confirms it correctly, we save it in user model
 public function password_Verification(Request $request)
    {
        $otp2=$this->otp->validate($request->email,$request->otp);
        if(!$otp2->status){
            return response()->json($otp2, 401);

        }
        return response()->json(['status'=>true,'message' => "Your Code is True "], 200);
        }

    public function change (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => ['required'
        ]]);

        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        // $user=auth('sanctum')->user();
        // $user_id = $user->id;
        $email=request('email');
        $user=User::where('email',$email)->first();
        if ($user!=null){

          $user->password=Crypt::encryptString(request('password'));
          $user->email_verified_at=now();
          $user->save();

          return response()->json(["message" => 'Your password changed ']);
        }

        return response()->json(["message" => 'some thing error']);
    }




    //Send a link to the user if he forgets the password and the user requests to reset it

//     public function sendPasswordResetLink(Request $request)
//     {

//     // $this->validateEmail($request);
//     $validator = Validator::make($request->all(), [
//         'email' => 'required|email',
//    ]);


//     $employee = Employee::where('email', $request->email)->first();

//     if (!$employee) {
//         return response()->json([
//             'message' => 'We can\'t find a user with that email address.',
//         ], 404);
//     }

//     $employee->sendPasswordResetNotification($this->broker()->createToken($employee));

//     return response()->json([
//         'message' => 'We have emailed your password reset link!',
//     ]);
// }



    // public function reset(Request $request)
    // {
    //     return response()->json(request('token'));
    // }

 // When the user clicks on the link that was sent to his email
 //and enters the new password and confirms it correctly, we save it in user model
//  public function resetPassword(Request $request)
//  {
//      $request->validate([
//     'token' => 'required',
//     'email' => 'required|email',
//     'password' => ['required', 'confirmed', RulesPassword::defaults()],
// ]);
// $employee = Employee::where('email', request('email'))->first();

// $status = Password::reset(
//     $request->only('email', 'password', 'password_confirmation', 'token'),
//     function ($employee) use ($request) {
//         $employee->forceFill([
//             'password' => Hash::make($request->password),
//             'remember_token' => Str::random(60),
//         ])->save();

//         $employee->tokens()->delete();

//         event(new PasswordReset($employee));
//     }
// );

// if ($status == Password::PASSWORD_RESET) {
//     return response([
//         'message'=> 'Password reset successfully'
//     ]);
// }

// return response([
//     'message'=> __($status)
// ], 500);



//  }
// public function resetPassword(Request $request)
// {
//     $request->validate([
//         'token' => 'required',
//         'email' => 'required|email',
//         'password' => 'required|confirmed|min:8',
//     ]);

//     $employee = Employee::where('email', $request->email)->first();

//     if (!$employee) {
//         return response()->json([
//             'message' => 'We can\'t find a user with that email address.',
//         ], 404);
//     }

//     $response = $this->broker()->reset(
//         $this->credentials($request),
//         function ($user,$password) {
//             $this->resetPassword($user,$password);
//         }
//     );


//     if ($response == Password::PASSWORD_RESET) {
//         return response()->json([
//             'message' => 'Your password has been reset!',
//         ]);
//     } else {
//         return response()->json([
//             'message' => 'Unable to reset password, please try again later.',
//         ], 500);
//     }
// }



}
