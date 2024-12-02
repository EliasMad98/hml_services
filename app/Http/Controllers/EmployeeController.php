<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use App\Models\Complaint;
use App\Models\ComplaintVisit;

class EmployeeController extends Controller
{

    public function getEmployeeDataByToken()
    {

        $employee = auth('api-employees')->user();

        return response()->json($employee, 200);
    }


    public function updateFcmToken(Request $request)
    {
        $employee=auth('api-employees')->user();
        if (!$employee) {
            return response()->json(['message' =>'Not Authenticated'], 401);
        }
        $validator = Validator::make(
            $request->all(),['token' => 'required',]);

        if ($validator->fails()) {
            return response()->json(['message' =>'The token field is required'], 400);
        }
        try {
            $employee->update(['fcm_token' => $request->token]);
            return response()->json(['message' => 'Employee token has been updated successfully'],200);
        } catch (\Exception $e) {
            report($e);
            return response()->json(['message' => 'Error'], 500);
        }
    }

    public function create_employee_token() {
        //just for developing create user token
        $id=request('id');
        $employee=Employee::find($id);
        $token = $employee->createToken('authToken',['*'])->plainTextToken;
        return response()->json($token,200);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(request('employee_name') != null){
                $search_text = request('employee_name');
                $employees =Employee::where('first_name','LIKE','%'.$search_text.'%')
                            ->orWhere('last_name','LIKE','%'.$search_text.'%')->with('roles:name')
                            ->latest()->paginate(10);
                        }
         else{

            $employees =Employee::with('roles:name')->latest()->paginate(10);
        }
        return response()->json($employees,200);
    }

    public function dataCounts()
    {
        $employee = auth('api-employees')->user();
        $employee_id=$employee->id;

        $jobDone=Complaint::where('employee_id',$employee_id)->where('job_finished',1)->count();
        $pendingJobs=Complaint::where('employee_id',$employee_id)->whereNotNull('service_started')->where('job_finished',0)->count();
        $to_do=Complaint::where('employee_id',$employee_id)->whereNull('service_started')->where('job_finished',0)->count();

        return response()->json(['jobDone'=>$jobDone,'pendingJobs' => $pendingJobs,'to_do'=>$to_do],200);
    }

    public function getWorker(Request $request)
    {
        $workers=Employee::select("id","first_name","last_name","job_type_id","email","phone","fcm_token","created_at")
        ->whereHas("roles", function($q) {
            $q->where("name", "worker");
        })
        ->withCount([
            "complaints as jobDone" => function($q) {
                $q->where('job_finished',1); } ,
                "complaints as pendingJobs" => function($q) {
                $q->whereNotNull('service_started')->where('job_finished',0);} ,
                "complaints as to_do" => function($q) {
                $q->whereNull('service_started')->where('job_finished',0);}
            ])
            ->with('job_type:id,name')
        // ->whereHas("roles", function($q) {
        //     $q->where("name", "worker");
        // })
        ->when($request->has('job_type_id'), function ($query) use ($request) {
            $query->where('job_type_id', $request->job_type_id);
        })
        ->when($request->has('worker_name'), function ($query) use ($request) {
              $search_text =$request->worker_name;
            $query->where('first_name','LIKE','%'.$search_text.'%')
                  ->orWhere('last_name','LIKE','%'.$search_text.'%');
        })
          ->whereHas("roles", function($q) {
            $q->where("name", "worker");
        })
        ->when($request->has('sort'), function ($query) use ($request) {
            if($request->sort=='a-z'){
                $query->orderby('first_name','asc');
            }elseif($request->sort=='pendingJobs'){
                $query->orderby('pendingJobs','asc');
            }
        })
        ->latest()->paginate(10);


        return response()->json([$workers],200);
    }
    public function getWorkersNames()
    {

            $employees =Employee::select('id','first_name','last_name')->whereHas("roles", function($q) {
                $q->where("name", "worker");
            })->get();


        return response()->json($employees,200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function show(Employee $employee)
    {
        //
        $emplyee_id = request('id');
        $emplyee_id = Employee::find($emplyee_id);
        if ($emplyee_id !=null){
            return response($emplyee_id,200);
        }
        else {
            return response()->json(['error'=>' this id does not exist '],404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function edit(Employee $employee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Employee $employee)
    {
        //
        $validator=Validator::make($request->all(),
        [
         'first_name' => 'required',
         'last_name' => 'required',
        //  'role' => 'required',
        // 'job_type_id' => 'required',
         'email' => 'required|email|unique:employees',
         'phone' => 'required|unique:employees',
         'password' => 'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        $employee_id = request('id');
        $employee = Employee::find($employee_id);
        return $employee;
        if ($employee !=null){

            $employee->update([

                'first_name'=> request('first_name'),
                'last_name'=> request('last_name'),
                'job_type_id' => request('job_type_id') ?? null,
                'email'=>request('email'),
                'phone' =>request('phone'),
                'password'=>Crypt::encryptString(request('password')),

                ]
            );
            return response($employee,200);

        }
        else {
            return response()->json(['error'=>' this id does not exit to modify '],404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy(Employee $employee)
    {
        //
        try {
            $employee_id = request('id');
            $employee= Employee::find($employee_id);

              if($employee == null){
                return response()->json(['message' => 'This employee is not exist'],404);
              }
            $employee->delete();
            return response()->json([$employee,'status'=>"success",'message' => "Employee deleted successfully"],200);

 }catch (\Exception $ex) {
         return response()->json(["message"=>$ex->getMessage()], 400);
     }
    }

    public function createEmployee(Request $request)
    {
     $validator = Validator::make($request->all(), [
         'first_name' => 'required',
         'last_name' => 'required',
        //  'job_type_id' => 'required',
         'role' => 'required',
         'email' => 'required|email|unique:employees',
         'phone' => 'required|unique:employees',
         'password' => 'required',
     ]   );

     if($validator->fails()){
         return response()->json($validator->errors(),400);
     }

     $employee = Employee::create([
         'first_name'=> $request->first_name,
         'last_name'=> $request->last_name,
         'job_type_id' => $request->job_type_id,
         'email'=>$request->email,
         'phone' =>$request->phone,
         'password'=>Crypt::encryptString($request->password),
     ]);

   if ($request->role =='worker')
   {
    $employee->assignRole('worker');
    //   $token = $employee->createToken('authToken',['worker'])->plainTextToken;
     $response = [
        'worker' => $employee,
        // 'token' => 'w'.'_'.$token,
        'message'=>'worker created successfully'
    ];

    return response($response, 200);

   }
   if ($request->role =='sub-admin')
    {
     $employee->assignRole('sub-admin');
    //   $token = $employee->createToken('authToken',['sub-admin'])->plainTextToken;
      $response = [
         'worker' => $employee,
        //  'token' => 's'.'_'.$token,
         'message'=>'worker created successfully'
     ];


    return response($response, 200);

   }
   elseif ($request->role =='employee')
   {
    $employee->assignRole('employee');
    //  $token = $employee->createToken('authToken',['employee'])->plainTextToken;
     $response = [
        'employee' => $employee,
        // 'token' => 'e'.'_'.$token,
        'message'=>'Employee created successfully'
    ];

    return response($response, 200);

   }


    //  $token = $employee->createToken('authToken',['employee'])->plainTextToken;
    return response(['message'=>' Sorry , This Role dosent exist'], 400);
 }


  // method to login user

  public function login(Request $request)
  {
      $validator = Validator::make($request->all(), [

          'email' => 'required|email',
          'password' => 'required',
      ]);

      if($validator->fails()){
          return response()->json($validator->errors(),400);
      }
      $email=request('email');
      $password=request('password');
     $employee = Employee::where('email', $email)->first();

//&&  $employee->hasRole('super-admin')
      if ($employee!=null){

       $employee_password = Crypt::decryptString($employee->password);
    //    return  $employee_password ;
              if($password == $employee_password ) {
                  // $id=$user->id;
                  if($employee->hasRole('employee')){
                    $token = $employee->createToken('authToken',['employee'])->plainTextToken;
                //   return $employee;

                    $response = [
                      'employee' => $employee,
                      'token' => 'e'.'_'.$token,
                      'message'=>'Employee logged in successfully'
                  ];

                return response($response, 200);

                }
                elseif($employee->hasRole('worker')){
                    $token = $employee->createToken('authToken',['worker'])->plainTextToken;
                    $response = [
                      'employee' => $employee,
                      'token' => 'w'.'_'.$token,
                      'message'=>'Worker logged in successfully'
                  ];

                return response($response, 200);

                }

            elseif($employee->hasRole('sub-admin')){
                $token = $employee->createToken('authToken',['sub-admin'])->plainTextToken;
                $response = [
                  'employee' => $employee,
                  'token' => 's'.'_'.$token,
                  'message'=>'Worker logged in successfully'
              ];

            return response($response, 200);

            }
        }

              else{
                  return response(['message'=>'Sorry ,You entered a wrong password'], 400);

              }
            }

             else{
              return response([ 'message'=>'Employee dosent exist'], 400);

          }

      }


      public function EmployeeJobs(Request $request)
      {
          try {

            $employee=auth('api-employees')->user();
              $jobs = Complaint::where('employee_id',$employee->id)->whereNull('service_started')
              ->when($request->has('urgent'), function ($query) use ($request) {
                $query->where('urgent',$request->urgent);
            })->latest()->get();

            $query = ComplaintVisit::where('employee_id',$employee->id)->with('complaint',function($query) use ($request) {
                $query->whereNull('service_started')
            ->when($request->has('urgent'), function ($query) use ($request) {
              $query->where('urgent',$request->urgent);
          });})->latest()->get();


              return response()->json(['jobs'=>$jobs ,'tasks'=>$query ],200);

      }catch (\Exception $ex) {
           return response()->json(["message"=>$ex->getMessage()], 400);
       }
      }
      public function EmployeeLog()
      {
          try {
            $employee=auth('api-employees')->user();
              $jobs = Complaint::where('employee_id',$employee->id)->whereNotNull('service_started')->latest()->paginate(10);
              return response()->json($jobs,200);


   }catch (\Exception $ex) {
           return response()->json(["message"=>$ex->getMessage()], 400);
       }
    }

    public function getAllEmployeeNotifications()
    {
        $employee=auth('api-employees')->user();
        $employee=Employee::find($employee->id);
        $notifications=$employee->notifications()->paginate(10);
        return response()->json($notifications,200);

    }

    public function getNotReadenEmployeeNotifications()
    {
        $employee=auth('api-employees')->user();
        $employee=Employee::find($employee->id);
        $notifications=$employee->unreadNotifications()->paginate(10);
        return response()->json($notifications,200);

    }

    public function markEmployeeNotificationsAsRead()
    {
        $employee=auth('api-employees')->user();
        $employee=Employee::find($employee->id);
        $employee->unreadNotifications->markAsRead();
        return response()->json("ok",200);

    }
 //method to logout employee

 public function logout() {
    try {
        auth('sanctum')->user()->currentAccessToken()->delete();

return  response()->json(
    ['message' => 'Logged out']
);
}catch (\Error $ex) {
    return  response()->json(["error"=>$ex->getMessage(),"message"=>"The token is not valid or expired" ],401);
}
 }

public function revealPassword()
{
    $employee_id = request('employee_id');
    $employee = Employee::find($employee_id);
    if ($employee != null) {
        $employee_password = Crypt::decryptString($employee->password);
        return response()->json(["employee_password"=> $employee_password,"employee_id"=>$employee_id ], 200);
    } else {
        return response()->json(['error' => ' this id does not exit to modify '], 404);
    }
}

}
