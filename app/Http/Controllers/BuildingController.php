<?php

namespace App\Http\Controllers;

use App\Models\Building;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class BuildingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
           $buildings =Building::latest()->paginate(10);
        return response()->json($buildings,200);
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
            'location_details'=>'required|max:255',
            'street_name'=>'required|max:255',
            'building_name'=>'required|max:255',
            'building_number'=>'required|max:255',
            'lat'=>'required|max:255',
            'long'=>'required|max:255',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }

        try{
             $building= Building::create([
                'location_details'=>$request->location_details,
                'street_name'=>$request->street_name,
                'building_name'=>$request->building_name,
                'building_number'=>$request->building_number,
                'lat'=>$request->lat,
                'long'=>$request->long,
                ]
            );
            return response()->json(["data"=>$building],201);
            }
            catch(Exception $ex){
                return response()->json(["message"=>$ex->getMessage()],400);
                }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Building  $building
     * @return \Illuminate\Http\Response
     */
    public function show(Building $building)
    {
        $building_id = request('id');
        $building= Building::find($building_id);
        if ($building !=null){
            return response(["data"=>$building],200);
        }
        else {
            return response()->json(['error'=>' this id does not exist '],404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Building  $building
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Building $building)
    {

        $validator=Validator::make($request->all(),
        [
            'location_details'=>'required|max:255',
            'street_name'=>'required|max:255',
            'building_name'=>'required|max:255',
            'building_number'=>'required|max:255',
            'lat'=>'required|max:255',
            'long'=>'required|max:255',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        $building_id = request('id');
        $building= Building::find($building_id);

        if ($building !=null){

            $building->update([
                'location_details'=>$request->location_details,
                'street_name'=>$request->street_name,
                'building_name'=>$request->building_name,
                'building_number'=>$request->building_number,
                'lat'=>$request->lat,
                'long'=>$request->long,
                ]
            );
            return response($building,200);
        }
        else {
            return response()->json(['error'=>' this id does not exit to modify '],404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Building  $building
     * @return \Illuminate\Http\Response
     */
    public function destroy(Building $building)
    {
        try {
            $building_id = request('id');
            $building= Building::find($building_id);
              if($building ==null){
                return response()->json(['message' => 'This building is not exist'],404);
              }

            $building->delete();
            return response()->json([$building,'status'=>"success",'message' => "Building deleted successfully"],200);

 }catch (\Exception $ex) {
         return response()->json(["message"=>$ex->getMessage()], 400);
     }
    }
    public function getBuildingsName(){

  $buildings =Building::select('id','building_name','building_number')->get();
        return response()->json($buildings,200);
    }
}
