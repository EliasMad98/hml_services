<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\ComplaintAsset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\PhotoTrait;
use App\Notifications\JobFinishedNotification;
use Exception;

class ComplaintAssetController extends Controller
{
    use PhotoTrait;
  public function UploadBefor(Request $request)

  {


        $validator=Validator::make($request->all(),
        [
            'complaint_id'=>'required',

        ]);
        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        try{

             $complaint_id=request('complaint_id');
             $employee=auth('api-employees')->user();

            if($request->images)
            {
            $files= $request->images;
            foreach($files as $file)
            {
             $photo_path=$this->saveBase64File($file,"complaintImage",'images/assets');
            $employee->upload()->create([
                'complaint_id'=>$complaint_id,
                'status'=>'before',
                'type'=>'Image',
                'path'=>$photo_path
            ]);
            }
             }

            return response()->json(["data"=>'done'],201);
            }
            catch(Exception $ex){
                return response()->json(["message"=>$ex->getMessage()],400);
                }
  }

  public function UploadAfter(Request $request)
  {
        $validator=Validator::make($request->all(),
        [
            'complaint_id'=>'required'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        try{

             $complaint_id=$request->complaint_id;
             $employee=auth('api-employees')->user();

            if($request->images)
            {
            $files= $request->images;
            foreach($files as $file)
            {
             $photo_path=$this->saveBase64File($file,"complaintImage",'images/assets');
            $employee->upload()->create([
                'complaint_id'=>$complaint_id,
                'status'=>'after',
                'type'=>'Image',
                'path'=>$photo_path
            ]);

            }
            $complaint = Complaint::find($complaint_id);
            $complaint->update([
                'job_finished' => 1,
                'service_ended' => now()
            ]);
            $user_notify= $complaint->user;
            $user_notify->notify(new JobFinishedNotification($complaint_id));

             }

            return response()->json(["data"=>'done'],201);
            }
            catch(Exception $ex){
                return response()->json(["message"=>$ex->getMessage()],400);
                }

  }

  public function ComplaintGallery(Request $request)
  {
    $validator=Validator::make($request->all(),
    [
        'complaint_id'=>'required',

    ]);
    if($validator->fails()){
        return response()->json($validator->errors(),400);
    }
    try{

         $complaint_id=request('complaint_id');

         $complaintAsset=Complaint::where('id', $complaint_id)->with('assets')->get();
        return response()->json(["data"=>$complaintAsset],200);
        }
        catch(Exception $ex){
            return response()->json(["message"=>$ex->getMessage()],400);
            }
  }


}
