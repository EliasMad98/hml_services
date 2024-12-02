<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Exception;

class ApartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $apartments =Apartment::with("building:id,building_name","tenant:id,user_id","tenant.user:id,first_name,last_name")->withCount('complaints')
        ->when($request->has('building_id'), function ($query) use ($request) {
            $query->where('building_id', $request->building_id);
        })
        ->latest()->paginate(10);
        return response()->json($apartments,200);
    }

    public function getUserApartments()
    {
      $auth=auth('sanctum')->user();
      $user=User::where('id',$auth->id)->with('apartments',"apartments.building")
    ->first();
      $apartments=  $user->apartments;
      return response()->json(["data"=>$apartments],200);
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

            'building_id'=>'required|max:255',
            'tenant_id'=>'max:255',
            'location_name'=>'max:255',
            'unit_number'=>'required|max:255',
            'unit_type'=>'required|max:255',

        ]);
        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }

        try{
             $apartment= Apartment::create([
                'building_id'=>$request->building_id,
                'tenant_id'=>$request->tenant_id,
                'location_name'=>$request->location_name,
                'unit_number'=>$request->unit_number,
                'unit_type'=>$request->unit_type,
                ]
            );
            return response()->json(["data"=>$apartment],201);
            }
            catch(Exception $ex){
                return response()->json(["message"=>$ex->getMessage()],400);
                }

    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Apartment  $apartment
     * @return \Illuminate\Http\Response
     */
    public function show(Apartment $apartment)
    {
        $apartment_id = request('id');
        $apartment= Apartment::find($apartment_id);
        if ($apartment !=null){
            return response(["data"=>$apartment],200);
        }
        else {
            return response()->json(['error'=>' this id does not exist '],404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Apartment  $apartment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Apartment $apartment)
    {

        $validator=Validator::make($request->all(),
        [
            'building_id'=>'required|max:255',
            'tenant_id'=>'max:255',
            'location_name'=>'max:255',
            'unit_number'=>'required|max:255',
            'unit_type'=>'required|max:255',

        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        $apartment_id = request('id');
        $apartment= Apartment::find($apartment_id);

        if ($apartment !=null){

            $apartment->update([
                'building_id'=>$request->building_id,
                'tenant_id'=>$request->tenant_id,
                'location_name'=>$request->location_name,
                'unit_number'=>$request->unit_number,
                'unit_type'=>$request->unit_type,
                ]
            );
            return response($apartment,200);
        }
        else {
            return response()->json(['error'=>' this id does not exit to modify '],404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Apartment  $apartment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Apartment $apartment)
    {
        try {
            $apartment_id = request('id');
            $apartment= Apartment::find($apartment_id);
              if($apartment ==null){
                return response()->json(['message' => 'This address is not exist'],404);
              }

            $apartment->delete();
            return response()->json([$apartment,'status'=>"success",'message' => "Apartment deleted successfully"],200);

 }catch (\Exception $ex) {
         return response()->json(["message"=>$ex->getMessage()], 400);
     }
    }
    public function getApartementByBuildingId()

    {
        $building_id = request('building_id');
        $apartments =Apartment::where('building_id',$building_id)->get();
        return response()->json($apartments,200);

    }
}
