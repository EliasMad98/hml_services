<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notifications\EmailVerificationNotification;
use App\Models\NonTenant;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use App\Notifications\CustomNotification;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function updateFcmToken(Request $request)
    {
        $user=auth()->user();
        if (!$user) {
            return response()->json(['message' =>'Not Authenticated'], 401);
        }
        $validator = Validator::make(
            $request->all(),['token' => 'required',]);

        if ($validator->fails()) {
            return response()->json(['message' =>'The token field is required'], 400);
        }
        try {
            $request->user()->update(['fcm_token' => $request->token]);
            return response()->json(['message' => 'User token has been updated successfully'],200);
        } catch (\Exception $e) {
            report($e);
            return response()->json(['message' => 'Error'], 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function create_user_token()
    {
        //just for developing create user token
        $id = request('id');
        $user = User::find($id);
        $token = $user->createToken('authToken', ['*'])->plainTextToken;
        return response()->json($token, 200);
    }

    public function register(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'first_name' => 'required',
                'last_name' => 'required',
                'phone' => 'required|unique:users',
                'email' => 'required|email|unique:users',
                'password' => 'required',
            ]
        );

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = User::create([
            'first_name' => request('first_name'),
            'last_name' => request('last_name'),
            'phone' => request('phone'),
            'email' => request('email'),
            'password' => Crypt::encryptString(request('password'))
        ]);
        $non_tenant = NonTenant::create([
            'user_id' => $user->id,
        ]);
        // $token = $user->createToken('authToken',['user'])->plainTextToken;

        $user->notify(new EmailVerificationNotification());
        $response = [
            'user' => $user,
            // 'token' => 'n'.'_'.$token,
            'message' => 'User created successfully'
        ];

        return response()->json($response, 201);
    }

    public function CreateUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ], [
            'c_password' . 'same' => 'The password and confirm password must be the same'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }


        $user = User::create([
            'first_name' => request('first_name'),
            'last_name' => request('last_name'),
            'phone' => request('phone'),
            'email' => request('email'),
            'email_verified_at' => now(),
            'password' => Crypt::encryptString(request('password'))
        ]);
        $tenant = Tenant::create([
            'user_id' => $user->id,
        ]);


        // $token = $user->createToken('authToken',['tenant'])->plainTextToken;
        $response = [
            'user' => $user,
            // 'token' => 't'.'_'.$token,
            'message' => 'User created successfully'
        ];
        return response()->json($response, 201);
    }
    // method to login user

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $email = request('email');
        $password = request('password');
       $user = User::where('email', $email)->with('tenant', 'non_tenant')->first();

        if ($user != null) {
            $user_password = Crypt::decryptString($user->password);

            if ($password == $user_password) {


                // $id=$user->id;
                if ($user->tenant != null) {
                    $token = $user->createToken('authToken', ['tenant'])->plainTextToken;
                    $response = [
                        'user' => $user,
                        'token' => 't' . '_' . $token,
                        'message' => 'Tenant logged in successfully'
                    ];

                    return response($response, 200);
                } else {
                    $token = $user->createToken('authToken', ['non_tenant'])->plainTextToken;
                    $response = [
                        'user' => $user,
                        'token' => 'n' . '_' . $token,
                        'message' => 'Non_Tenant logged in successfully'
                    ];

                    return response($response, 200);
                }
            } else {
                return response(['message' => 'Sorry ,You entered a wrong password'], 400);
            }
        } else {
            return response(['message' => 'This User Email is not exist'], 400);
        }
    }
    // method to update email for user and send a new verification to a new his email

    public function updateemail(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $user = auth('sanctum')->user();
        $user_id = $user->id;
        $user = User::find($user_id);
        if ($user != null) {

            if (request('email') != $user->email) {
                $user->email_verified_at = null;
                $user->notify(new EmailVerificationNotification());
            }

            return response()->json(['message' => ' verifyyy your email '], 200);
        } else {
            return response()->json(['error' => ' this id does not exit to modify '], 404);
        }
    }

    // method to update first_name , last_name for user

    public function updatename(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $user = auth('sanctum')->user();
        $user_id = $user->id;
        $user = User::find($user_id);
        if ($user != null) {

            $user->update(
                [
                    'first_name' => request('first_name'),
                    'last_name' => request('last_name'),

                ]
            );
            return response($user, 200);
        } else {
            return response()->json(['error' => ' this id does not exit to modify '], 404);
        }
    }

    // method to update phone number for user

    public function updatephone(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'phone' => 'required'

        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $user = auth('sanctum')->user();
        $user_id = $user->id;
        $user = User::find($user_id);
        if ($user != null) {
            if ($user->tenant != null) {

            $user->update(
                [
                    'phone' => request('phone'),


                ]
            );
            return response($user, 200);
        }} else {
            return response()->json(['error' => ' this id does not exit to modify '], 404);
        }
    }

    // method to update password and verify if his old password right or not to commplete this change

    public function updatepassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $user = auth('sanctum')->user();
        $user_id = $user->id;
        $user = User::find($user_id);
        if ($user != null) {
            $user_password = Crypt::decryptString($user->password);
            $password = request('old_password');

            if ($password == $user_password) {

                $user->password = Crypt::encryptString(request('new_password'));
                $user->save();

                return response()->json(["message" => 'Your password changed ']);
            }

            return response()->json(["message" => 'some thing error']);
        }
    }


    public function getUserDataByToken()
    {
        $user = auth('sanctum')->user();
        // return 'aaa' ;
        $user = User::where('id', $user->id)->with('tenant', 'non_tenant')->first();
        // $user=auth('sanctum')->user();
        if($user != null){
        if($user->tenant != null){
            $user=User::where('id',$user->id)->with('apartments',"apartments.building")
            ->first();
              $apartments=$user->apartments;
              return response()->json(['user'=>$user , 'addresses'=>$apartments],200);
        }
        elseif($user->non_tenant != null){
        $addresses = $user->addresses;
        return response()->json([['user'=>$user ,
         'addresses'=>$addresses]],200);
        }

      }
      else{
        return response()->json(["message"=>'Unauthenticated.'], 401);
      }

        // return response()->json($user, 200);
    }

    //method to logout user

    public function logout()
    {
        try {
            auth('sanctum')->user()->currentAccessToken()->delete();
            $user = auth('sanctum')->user();
        // return 'aaa' ;
        $user = User::where('id', $user->id)->first();
        $user->update([
            'fcm_token'=>null
        ]);

            return [
                'message' => 'Logged out'
            ];
        } catch (\Error $ex) {
            return  response()->json(["error" => $ex->getMessage(), "message" => "The token is not valid or expired"], 401);
        } catch (\Exception $ex) {
            return  response()->json(["error" => $ex->getMessage(), "message" => "The token is not valid or expired"], 401);
        }
    }

    public function deleteUser()
    {
        //find the User then delete it
        $user_id =  request('id');
        $user = User::find($user_id);

        if ($user == null) {
            return response()->json(['message' => 'This User is not exist'], 404);
        }
        $user->delete();
        return response()->json(["user" => $user, 'message' => "User deleted successfully"], 200);
    }

    public function getAllUsers()
    {
        $users = User::latest()->with('tenant','non_tenant')->get();
        return response()->json($users, 200);
    }
    public function revealPassword()
    {
        $user_id = request('user_id');
        $user = User::find($user_id);
        if ($user != null) {
            $user_password = Crypt::decryptString($user->password);
            return response()->json(["user_id"=> $user_id,"user_password"=>$user_password ], 200);

        } else {
            return response()->json(['error' => ' this id does not exit to modify '], 404);
        }
    }
}
