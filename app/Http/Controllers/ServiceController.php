<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Exception;
use App\Traits\PhotoTrait;

class ServiceController extends Controller
{

    use PhotoTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $services =Service::all();
        return response()->json(["data"=>$services],200);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validator=Validator::make($request->all(),
        [
            'title'=>'required|max:255',
            'image_path' =>'image|max:512|mimes:jpg,jpeg,bmp,png,webp,svg',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        try{
        $image_path=$this->saveImage($request->image_path,'service','images/services');
         $service= Service::create([
            'title'=>$request->title,
            'image_path'=>$image_path,
            ]
        );
        return response()->json($service,201);
        }
        catch(Exception $ex){
            return response()->json(["message"=>$ex->getMessage()],400);
            }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function show(Service $service)
    {
        $service_id = request('id');
        $service= Service::find($service_id);
        if ($service !=null){
            return response($service,200);
        }
        else {
            return response()->json(['error'=>' this id does not exist '],404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Service $service)
    {

        $validator=Validator::make($request->all(),
        [
            'title'=>'required|max:255',
            'image_path' =>'image|max:512|mimes:jpeg,bmp,png,webp,svg',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        $service_id = request('id');
        $service= Service::find($service_id);
        $image_path= request('image_path');
        if ($service !=null){
            if ( $request->hasFile('image_path'))
        {
            $destination=$service->image_path;
                    if(File::exists($destination))
                    {
                       File::delete($destination);
                    }
                    $image_path=$this->saveImage($request->image_path,'service','images/services');
                }
                    else{
                        $image_path=$service->image_path;
                    }
            $service->update([
                'title' => $request->title ,
                'image_path' => $image_path ,
                ]
            );
            return response($service,200);

        }
        else {
            return response()->json(['error'=>' this id does not exit to modify '],404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function destroy(Service $service)
    {
        try {
            $service_id = request('id');
            $service= Service::find($service_id);

              if($service ==null){
                return response()->json(['message' => 'This service is not exist'],404);
              }
            $destination=$service->image_path;
            if(File::exists($destination))
            {
               File::delete($destination);
            }
            $service->delete();
            return response()->json([$service,'status'=>"success",'message' => "Service deleted successfully"],200);

 }catch (\Exception $ex) {
         return response()->json(["message"=>$ex->getMessage()], 400);
     }
    }

}
