<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Http\Request;
use App\Models\Participant;
use Illuminate\Support\Facades\Validator;
use App\Models\Message;
use App\Events\MessageSent;
use Exception;

class ConversationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


     public function index()
     {
         //
         // Get all conversations the authenticated user is a part of, with their last message
         $employee=auth('api-employees')->user();
         $conversations = Conversation::whereHas('employees', function($query) use($employee) {

         $query->where('employee_id', $employee->id);
     })
     ->with(['participants.employee'=> function($query) use ($employee) {
         $query->where('id','!=',$employee->id)->get();
     },'messages' => function($query) {
         $query->orderBy('created_at', 'desc')->first();
     }])
     ->get();

     return response()->json($conversations,200);
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
        $validator=Validator::make($request->all(),
        [
            'employee_id'=>'required',

        ]);
        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }

        try{
        $employee_id=request('employee_id');
    $employee=auth('api-employees')->user();
   $conversation =  $employee->conversations() ->whereHas('participants', function ($query) use ($employee_id) {
        // $employee=auth('api-employees')->user();
    $query->where('employee_id', $employee_id);})->with('participants.employee') ->with(['messages' => function($query) {
        $query->orderBy('created_at', 'desc')->first();
    }])->first();
    //Check if there is an existing conversation with the same participants
    // $conversation = Conversation::has('participants', '=', 2)
    //     ->whereHas('participants', function ($query) use ($employee_id) {
    //             $employee=auth('api-employees')->user();
    //         $query->where('employee_id', $employee_id)->where('employee_id', $employee->id);

    //     })
    //     ->with('participants')
    //     ->first();
        // $exists = $user->likes->contains($product_id);

            // $conversation= Conversation::whereHas('employees', function($query) {
            //     $employee=auth('api-employees')->user();
            //        $employee_id=request('employee_id');
            //        $query->where('employee_id', $employee_id)->where('employee_id', $employee->id)
            //        ->with(['messages' => function($query) {
            //         $query->orderBy('created_at', 'desc')->first();
            //     }]);
            // })->first();

            if($conversation == null)
            {
                $conversation=Conversation::create([]);
                $participants=Participant::create([
                    'conversation_id'=>$conversation->id,
                    'employee_id'=>$employee_id,
                ]);
                $participants=Participant::create([
                    'conversation_id'=>$conversation->id,
                    'employee_id'=>$employee->id,
                ]);
            }
            return response()->json(["data"=>$conversation],201);
            }
            catch(Exception $ex){
                return response()->json(["message"=>$ex->getMessage()],400);
                }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Conversation  $conversation
     * @return \Illuminate\Http\Response
     */
    public function show(Conversation $conversation)
    {
//
        $conversation_id = request('id');
        $conversations = Message::select('id' ,'conversation_id','sender_id','message','created_at')->with('sender:id,first_name,last_name')->where('conversation_id',$conversation_id)
        ->orderBy('created_at', 'desc')
        ->paginate(20);

        return response()->json($conversations,200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Conversation  $conversation
     * @return \Illuminate\Http\Response
     */
    public function edit(Conversation $conversation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Conversation  $conversation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Conversation $conversation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Conversation  $conversation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Conversation $conversation)
    {
        //
    }
}
