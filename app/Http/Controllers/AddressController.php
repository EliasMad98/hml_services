<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\NonTenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         $non_tenant_id=request('non_tenant_id');
         if($non_tenant_id != null){

         $addresses=Address::where('non_tenant_id',$non_tenant_id)->with('non_tenant.user:id,first_name,last_name')->latest()->paginate(10);

         }
         else
         {
            $addresses=Address::with('non_tenant.user:id,first_name,last_name')->latest()->paginate(10);

         }
            if($addresses !=null){

        return response()->json($addresses,200);
      }
      else{
        return response()->json(["message"=>'Unauthenticated.'], 401);
      }

    }

    public function getUserAddresses()
    {
      $user=auth('sanctum')->user();
      if($user !=null){
        $addresses=  $user->addresses;
        return response()->json(["data"=>$addresses],200);
      }
      else{
        return response()->json(["message"=>'Unauthenticated.'], 401);
      }
    }

    // public function getNon_TenantAddresses()
    // {
    //     $non_tenant_id=request('non_tenant');
    //    $addresses=NonTenant::where('id',$non_tenant_id)->with('addresses','user:id,first_name,last_name')->get();

    //   if($addresses !=null){

    //     return response()->json(["data"=>$addresses],200);
    //   }
    //   else{
    //     return response()->json(["message"=>'Unauthenticated.'], 401);
    //   }
    // }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $non_tenant=auth()->user()->non_tenant()->first();
        $non_tenant_id=$non_tenant->id;
        $validator=Validator::make($request->all(),
        [
            'location_details'=>'required',
            'location_name'=>'required',
            'unit_number'=>'max:255',
            'unit_type'=>'max:255',
            'contact_name'=>'max:255',
            'contact_mobile'=>'max:255',
            'lat'=>'max:255',
            'long'=>'max:255',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }

        try{
             $address= Address::create([
                'non_tenant_id'=>$non_tenant_id,
                'location_details'=>$request->location_details,
                'location_name'=>$request->location_name,
                'unit_number'=>$request->unit_number,
                'unit_type'=>$request->unit_type,
                'contact_name'=>$request->contact_name,
                'contact_mobile'=>$request->contact_mobile,
                'lat'=>$request->lat,
                'long'=>$request->long,
                ]
            );
            return response()->json(["data"=>$address],201);
            }
            catch(Exception $ex){
                return response()->json(["message"=>$ex->getMessage()],400);
                }
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Address  $address
     * @return \Illuminate\Http\Response
     */
    public function show(Address $address)
    {
        $address_id = request('id');
        $address= Address::find($address_id);
        if ($address !=null){
            return response(["data"=>$address],200);
        }
        else {
            return response()->json(['error'=>' this id does not exist '],404);
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Address  $address
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Address $address)
    {
        $validator=Validator::make($request->all(),
        [
            'location_details'=>'required',
            'location_name'=>'required',
            'unit_number'=>'max:255',
            'unit_type'=>'max:255',
            'contact_name'=>'max:255',
            'contact_mobile'=>'max:255',
            'lat'=>'max:255',
            'long'=>'max:255',

        ]);
        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        $address_id = request('id');
        $address= Address::find($address_id);
        $non_tenant_id = request('non_tenant_id');

        if ($address !=null){
            $address->update([
                'non_tenant_id'=>$non_tenant_id,
                'location_details'=>$request->location_details,
                'location_name'=>$request->location_name,
                'unit_number'=>$request->unit_number,
                'unit_type'=>$request->unit_type,
                'contact_name'=>$request->contact_name,
                'contact_mobile'=>$request->contact_mobile,
                'lat'=>$request->lat,
                'long'=>$request->long,
                ]
            );
            return response($address,200);
        }
        else {
            return response()->json(['error'=>' this id does not exit to modify '],404);
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Address  $address
     * @return \Illuminate\Http\Response
     */
    public function destroy(Address $address)
    {
        try {
            $address_id = request('id');
            $address= Address::find($address_id);
              if($address ==null){
                return response()->json(['message' => 'This address is not exist'],404);
              }

            $address->delete();
            return response()->json([$address,'status'=>"success",'message' => "Address deleted successfully"],200);

 }catch (\Exception $ex) {
         return response()->json(["message"=>$ex->getMessage()], 400);
     }
    }
}
