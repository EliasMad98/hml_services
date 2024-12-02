<?php

namespace App\Http\Controllers;

use App\Models\ComplaintVisit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
USE App\Models\Employee;
USE App\Notifications\VisitScheduleNotification;
use Exception;

class ComplaintVisitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $complaintVisit =ComplaintVisit::all();
        return response()->json(["data"=>$complaintVisit],200);
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
        $validator=Validator::make($request->all(),
        [
            'complaint_id'=>'required',
            'date'=>'required|max:255',
            'time'=>'required|max:255',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }

        try{
             $complaintVisit= ComplaintVisit::create([
                'complaint_id'=>$request->complaint_id,
                'date'=>$request->date,
                'time'=>$request->time
                ]
            );


            $admins=Employee::whereHas("roles", function($q) {
                $q->where("name", "employee");
            })->get();

            if( $admins != null )
            {

                foreach($admins as $admin)
                {
                $admin->notify(new VisitScheduleNotification($complaintVisit));
                }
            }

            return response()->json(["data"=>$complaintVisit],201);
            }
            catch(Exception $ex){
                return response()->json(["message"=>$ex->getMessage()],400);
                }
    }
    public function assignVisitToEmployee(Request $request)
    {
        //
        $validator=Validator::make($request->all(),
        [
            'complaintVisit_id'=>'required',
            'employee_id'=>'required',

        ]);
        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }

        try{
            $complaintVisitId = request('complaintVisit_id');
            $complaintVisit = ComplaintVisit::find($complaintVisitId);
            if($complaintVisit !=null){
             $complaintVisit->update([
                'employee_id'=>$request->employee_id,
                ]
            );
            return response()->json(["data"=>$complaintVisit],201);
            }
            else {
                return response()->json(['error'=>' this id does not exist '],404);
            }
        }
            catch(Exception $ex){
                return response()->json(["message"=>$ex->getMessage()],400);
                }

}

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ComplaintVisit  $complaintVisit
     * @return \Illuminate\Http\Response
     */
    public function show(ComplaintVisit $complaintVisit)
    {
        //
        $complaintVisitId = request('id');
        $complaintVisit = ComplaintVisit::where('id',$complaintVisitId)->with('employee:id,first_name,last_name','complaint')->first();
        $complaint=$complaintVisit->complaint;
        if($complaint !=null){
            $address= $complaint->addressable()->first();
            if($complaint->addressable_type=="App\\Models\\Apartment"){
            $address= $complaint->addressable()->with('building')->first();
            }
            elseif($complaint->addressable_type=="App\\Models\\Address"){
            $address= $complaint->addressable()->first();
            }
            return response()->json(["complaintVisit"=>$complaintVisit,"address"=>$address],200);
        }
        else{
            return response()->json(['error'=>'This id does not exist'],404);
        }
        if ($complaintVisit !=null){
            return response($complaintVisit,200);
        }
        else {
            return response()->json(['error'=>' this id does not exist '],404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ComplaintVisit  $complaintVisit
     * @return \Illuminate\Http\Response
     */
    public function edit(ComplaintVisit $complaintVisit)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ComplaintVisit  $complaintVisit
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ComplaintVisit $complaintVisit)
    {
        //
        $validator=Validator::make($request->all(),
        [
            'complaint_id'=>'required',
            'date'=>'required|max:255',
            'time'=>'required|max:255',
            // 'employee_id'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        $complaintVisitId = request('id');
        $complaintVisit= ComplaintVisit::find($complaintVisitId);

        if ($complaintVisit !=null){

            $complaintVisit->update([
                'date'=>$request->date,
                'time'=>$request->time,
                ]
            );
            return response($complaintVisit,200);
        }
        else {
            return response()->json(['error'=>' this id does not exit to modify '],404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ComplaintVisit  $complaintVisit
     * @return \Illuminate\Http\Response
     */
    public function destroy(ComplaintVisit $complaintVisit)
    {
        //
        try {
            $complaintVisitId = request('id');
            $complaintVisit = ComplaintVisit::find($complaintVisitId);
              if($complaintVisit == null){
                return response()->json(['message' => 'This Visit is not exist'],404);
              }

            $complaintVisit->delete();
            return response()->json([$complaintVisit,'status'=>"success",'message' => "complaintVisit deleted successfully"],200);

 }catch (\Exception $ex) {
         return response()->json(["message"=>$ex->getMessage()], 400);
     }
    }
    }

