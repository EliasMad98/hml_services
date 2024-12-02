<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Http\Requests\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Notifications\EmailVerificationNotification;
use Ichtrojan\Otp\Otp;
use Illuminate\Auth\Events\Verified;

class VerifyEmailController extends Controller
{
    //
    private $otp ;



    /**
     * Create a new notification instance.
     *
     * @return void
     */


    public function __construct()
    {
        //
        $this->otp =new Otp;


    }



    public function email_Verification(EmailVerificationRequest $request)
{

    $otp2=$this->otp->validate($request->email,$request->otp);
    if(!$otp2->status){
        return response()->json($otp2, 401);

    }
    $user=User::where('email',$request->email)->with('tenant','non_tenant')->first();

    // // return $user;
    // $user->update(['email_verified_at'=>now()]);
    // // $user->save();
    // $success['success']=true;
    // return response()->json($success, 200);
    // $user = User::find($request->route('id'));

    if ($user->hasVerifiedEmail()) {
        return response()->json(["message"=>'your email is already verified'], 200);
    }

    if ($user->markEmailAsVerified()) {
        event(new Verified($user));
        //  $success['success']=true;

        if($user->tenant != null){
            $token1 = $user->createToken('authToken',['tenant'])->plainTextToken;
            $token = 't'.'_'.$token1;
         return response()->json(['status'=>true,'message' => "Your Email has been verified successfully","token"=>$token], 200);

        // return response($response, 200);

        }


        else{
        $token1 = $user->createToken('authToken',['non_tenant'])->plainTextToken;

          $token = 'n'.'_'.$token1;

      return response()->json(['status'=>true,'message' => "Your Email has been verified successfully","token"=>$token], 200);
    }

        //    $token = $user->createToken('authToken',['user'])->plainTextToken;
    }



    }
public function reSendEmailVerification(Request $request)
{
    $email=request('email');
    $user=User::where('email',$email)->first();
    // return $user;
    if($user != null)
{
    $user->notify(new EmailVerificationNotification());
    return response()->json(['status'=>true,'message' => "The Verification Code has been sent successfully"], 200);}
    else{
        return response()->json(['status'=>false,"message"=>'This email does not exist'], 404);
    }
}
    }
