<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
// use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
// use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\Validator;

// use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */


    use SendsPasswordResetEmails;

    public function sendResetLinkEmail(Request $request)
    {
        // $this->validateEmail($request);
            $validator = Validator::make($request->all(), [
        'email' => 'required|email',
   ]);
//    return ($request->email);

        $employee = Employee::where('email', $request->email)->first();

        if (!$employee) {
            return response()->json([
                'message' => 'We can\'t find a user with that email address.',
            ], 404);
        }

        $employee->sendPasswordResetNotification($this->broker()->createToken($employee));

        return response()->json([
            'message' => 'We have emailed your password reset link!',
        ]);
    }
}
