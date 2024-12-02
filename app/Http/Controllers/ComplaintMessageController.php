<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\ComplaintMessage;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Employee;
use App\Notifications\NewMessageNotification;
use App\Notifications\NewUserMessageNotification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;

class ComplaintMessageController extends Controller
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
         $user=auth('sanctum')->user();
         //  return  $user;

         $validator=Validator::make($request->all(),
         [
             'complaint_id'=>'required',
             'message'=>'required|max:255',

         ]);
         if($validator->fails()){
             return response()->json($validator->errors(),400);
         }
         // if(User::where('id',$user->id)->first()!=null){
         //     $senderable_type='App\Models\User';

         // }
         // elseif(Employee::where('id',$user->id)->first()!=null){
         //     $senderable_type='App\Models\Employee';
         // }

         try{
             // return $request->complaint_id;
             $complaintMessage=$user->sendMessage()->create([
                 'complaint_id'=>$request->complaint_id,
                 'message'=>$request->message,
                 // 'senderable_id'=>$user->id,
                 // 'senderable_type'=> $senderable_type,

                 ]
             );

             if($complaintMessage -> senderable_type == "App\Models\Employee")
             {
                 $complaint_id=$request->complaint_id;
                 $complaint=Complaint::select('user_id')->where('id',$complaint_id)->first();

                 if($complaint !=null)
                 {
                       $user_id=$complaint->user_id;
                       $user=User::where('id',$user_id)->first();
                     $user->notify(new NewMessageNotification($request->message));
                    }




             }
             elseif($complaintMessage -> senderable_type == "App\Models\User")
             {

                $workers=Employee::whereHas("roles", function($q) {
                    $q->where("name", "employee");
                })->get();
//   return  $workers;
                if( $workers != null )
                {

                    foreach($workers as $worker)
                    {
                    $worker->notify(new NewUserMessageNotification($request->message,$complaintMessage,$user));
                    }
                }
                 }
             return response()->json(["data"=>$complaintMessage],201);
             }
             catch(Exception $ex){
                 return response()->json(["message"=>$ex->getMessage()],400);
                 }

                }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ComplaintMessage  $complaintMessage
     * @return \Illuminate\Http\Response
     */
    public function show(ComplaintMessage $complaintMessage,Request $request)
    {
        //
        $complaintMessageId=request('id');
        $complaintMessage =ComplaintMessage::where('complaint_id',$complaintMessageId)
        ->orderBy('created_at', 'desc')->paginate(20);


        if ($complaintMessage !=null){
            return response()->json($complaintMessage,200);
        }
        else {
            return response()->json(['error'=>' this id does not exist '],404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ComplaintMessage  $complaintMessage
     * @return \Illuminate\Http\Response
     */
    public function edit(ComplaintMessage $complaintMessage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ComplaintMessage  $complaintMessage
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ComplaintMessage $complaintMessage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ComplaintMessage  $complaintMessage
     * @return \Illuminate\Http\Response
     */
    public function destroy(ComplaintMessage $complaintMessage)
    {
        //

        try {
            $complaintMessageId = request('id');
            $complaintMessage = ComplaintMessage::find($complaintMessageId);
              if($complaintMessage == null){
                return response()->json(['message' => 'This Message is not exist'],404);
              }

            $complaintMessage->delete();
            return response()->json([$complaintMessage,'status'=>"success",'message' => "complaintMessage deleted successfully"],200);

 }catch (\Exception $ex) {
         return response()->json(["message"=>$ex->getMessage()], 400);
     }
    }

    //senderable_id

    public function getChatsforUser(ComplaintMessage $complaintMessage,Request $request)
    {

        $user=auth('sanctum')->user();
        // return $user ;
        $id=$user->id;
        $user=User::find($id);
        $employee=Employee::find($id);

        if($user != null)
        {
        $chats=Complaint::Where('user_id',$user->id)->WhereHas('messages')->with('employee:id,first_name,last_name','messages')->latest()->get();
        //  return $chats;
        // $chats=ComplaintMessage::select('complaint_id','message')->where('senderable_type','App\Models\User')->where('senderable_id', $user->id)->with('complaint.employee:id,first_name,last_name')->latest()->get();
        // return $chats;

       }
        elseif($employee!= null)
        {
        $chats=Complaint::Where('employee_id',$employee->id)->WhereHas('messages')->with('user:id,first_name,last_name','messages')->latest()->get();

        // $chats=ComplaintMessage::select('complaint_id')->where('senderable_type','App\Models\Employee')->where('senderable_id', $employee->id)->latest()->paginate(5);
        // $chats=ComplaintMessage::select('complaint_id','message')->where('senderable_type','App\Models\Employee')->where('senderable_id', $employee->id)->with('complaint.user:id,first_name,last_name')->latest()->get();
        }
        if ($chats!=null){
            return response($chats,200);
        }
        else {
            return response()->json(['error'=>' this id does not exist '],404);
        }
        //
    }


}

