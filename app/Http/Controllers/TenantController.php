<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;

use Illuminate\Http\Request;

class TenantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(request('tenant_name') != null){
            $search_text = request('tenant_name');

            $users =User::whereHas('tenant')
              ->where('first_name','LIKE','%'.$search_text.'%')
            ->orWhere('last_name','LIKE','%'.$search_text.'%')
             ->latest()->get();
            }


          elseif(request('building_id') != null ||request('apartement_id') != null){
        $users = User::whereHas('tenant', function ($query) {

            $query->whereHas('apartments', function ($query) {
                if(request('building_id') != null){
              $query->where('building_id',request('building_id'));}
              elseif(request('apartement_id') != null){
                $query->where('id',request('apartement_id'));}

       });

     })->paginate(10);}
     else{
        $users= User::whereHas('tenant')->latest()->paginate(10);

    }
       $users->load('apartments');
          return response()->json($users,200);
    }

    public function getTenantsNames()
    {

      $tenants =User::select('id','first_name','last_name')->with('tenant')->whereHas('tenant')->get();
       return response()->json($tenants,200);
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
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Tenant  $tenant
     * @return \Illuminate\Http\Response
     */
    public function show(Tenant $tenant)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Tenant  $tenant
     * @return \Illuminate\Http\Response
     */
    public function edit(Tenant $tenant)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tenant  $tenant
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tenant $tenant)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tenant  $tenant
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tenant $tenant)
    {
        //
    }
}
