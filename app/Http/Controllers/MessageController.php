<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Participant;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Redis;
use Exception;
use App\Notifications\NewWorkerMessageNotification;

class MessageController extends Controller
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
        // $employee=auth('api-employees')->user();
        $employee=auth('api-employees')->user();
        $validator=Validator::make($request->all(),
        [
            'conversation_id'=>'required',
            'message'=>'required|max:255',

        ]);
        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }

        try{
            $conversation_id=request('conversation_id');


            $conversation=Conversation::find($conversation_id);

            if($conversation != null)
            {
                $message= Message::create([
                    'conversation_id'=>$conversation->id,
                    'sender_id'=>$employee->id,
                    'message'=>request('message'),
                    'read_at'=>null,
                ]);


            event(new MessageSent($conversation_id,$message));

            Redis::publish('pmessage',json_encode(['event'=>'MessageSent','data'=>['channel'=>'private-'.$conversation_id,'conversation_id'=>$conversation_id,'message'=>$message,]]));
            
            $otherUserIDs = Participant::where('conversation_id', $conversation->id)
                ->where('employee_id', '!=', $employee->id)
                ->pluck('employee_id');
            $worker=Employee::where('id',$otherUserIDs)->first();
            $worker->notify(new NewWorkerMessageNotification($request->message));

            return response()->json(["data"=>$message],201);

            }
            else{

            }

            return response()->json(['error' => ' this id does not exit to modify '], 404);

            }

            catch(Exception $ex){
                return response()->json(["message"=>$ex->getMessage()],400);
                }



    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function show(Message $message)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function edit(Message $message)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Message $message)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function destroy(Message $message)
    {
        //
    }
}



// public function store(Request $request)
//     {

//         //
//         // $employee=auth('api-employees')->user();
//         $employee=auth('api-employee')->user();
//         $validator=Validator::make($request->all(),
//         [
//             'complaint_id'=>'required',
//             'message'=>'required|max:255',

//         ]);
//         if($validator->fails()){
//             return response()->json($validator->errors(),400);
//         }

//         try{

//             $conversation= Conversation::whereHas('participants', function($query) {
//                 $employee=auth('api-employees')->user();
//                 $query->where('employee_id', $employee);

//             })->first();

//             if($conversation != null)
//             {
//                 $message= Message::create([
//                     'conversation_id'=>$conversation->id,
//                     'sender_id'=>$employee->id,
//                     'message'=>request('message'),
//                     'read_at'=>null,
//                 ]);

//             }
//             else{

//                 $conversation=Conversation::create([]);
//                 $participants=Participant::create([
//                     'conversation_id'=>$conversation->id,
//                     'employee_id'=>$employee->id,
//                 ]);

//                 $message= Message::create([
//                     'conversation_id'=>$conversation->id,
//                     'sender_id'=>$employee->id,
//                     'message'=>request('message'),
//                     'read_at'=>null,
//                 ]);

//             }

//             return response()->json(["data"=>$message],201);

//             }

//             catch(Exception $ex){
//                 return response()->json(["message"=>$ex->getMessage()],400);
//                 }



//     }

