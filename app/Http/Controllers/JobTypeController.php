<?php

namespace App\Http\Controllers;

use App\Models\JobType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;


class JobTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        if(request('job_type') != null){
            $search_text = request('job_type');
            $job_name =JobType::where('name','LIKE','%'.$search_text.'%')
                        ->latest()->paginate(10);
                    }
     else{

        $job_name =JobType::latest()->paginate(10);
    }
    return response()->json($job_name,200);
    }

    public function getJobTypeNames()
    {
        //

        $job_names=JobType::all();

    return response()->json($job_names,200);
    }

    public function getEmployeesForJob()
    {
        //
        $job_type_id = request('id');
        $job_type= JobType::where('id',$job_type_id)->with('employees')->get();
        if ($job_type !=null){
            return response(["data"=>$job_type],200);
        }
        else {
            return response()->json(['error'=>' this id does not exist '],404);
        }
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

            'name'=>'required|max:255',

        ]);
        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }

        try{
             $job_type= JobType::create([

                'name'=>$request->name,

                ]
            );
            return response()->json(["data"=>$job_type],201);
            }
            catch(Exception $ex){
                return response()->json(["message"=>$ex->getMessage()],400);
                }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\JobType  $jobType
     * @return \Illuminate\Http\Response
     */
    public function show(JobType $jobType)
    {
        //
        $job_type_id = request('id');
        $job_type= JobType::find($job_type_id);
        if ($job_type !=null){
            return response(["data"=>$job_type],200);
        }
        else {
            return response()->json(['error'=>' this id does not exist '],404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\JobType  $jobType
     * @return \Illuminate\Http\Response
     */
    public function edit(JobType $jobType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\JobType  $jobType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, JobType $jobType)
    {
        //
        $validator=Validator::make($request->all(),
        [
            'name'=>'required|max:255',

        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        $job_type_id = request('id');
        $job_type= JobType::find($job_type_id);

        if ($job_type !=null){

            $job_type->update([
                'name'=>$request->name,
                ]
            );
            return response($job_type,200);
        }
        else {
            return response()->json(['error'=>' this id does not exit to modify '],404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\JobType  $jobType
     * @return \Illuminate\Http\Response
     */
    public function destroy(JobType $jobType)
    {
        //
        try {
            $job_type_id = request('id');
            $job_type= JobType::find($job_type_id);
              if($job_type ==null){
                return response()->json(['message' => 'This Job_type is  exist'],404);
              }

            $job_type->delete();
            return response()->json([$job_type,'status'=>"success",'message' => "Job_type deleted successfully"],200);

 }catch (\Exception $ex) {
         return response()->json(["message"=>$ex->getMessage()], 400);
     }
    }
}
