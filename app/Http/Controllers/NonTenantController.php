<?php

namespace App\Http\Controllers;

use App\Models\NonTenant;
use Illuminate\Http\Request;
use App\Models\User;

class NonTenantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        if(request('user_name') != null || request('street_name') != null){
            $users = User::whereHas('non_tenant', function ($query) {
                $search_text = request('user_name');
                    if(request('user_name') != null){
                        $query->Where(function($query) use($search_text) {
                            $query->
                            where('first_name','LIKE','%'.$search_text.'%')
                            ->orWhere('last_name','LIKE','%'.$search_text.'%');
                        });

                }
                  elseif(request('street_name') != null){
                $query->whereHas('addresses', function ($query) {
                    $query->where('street_name',request('street_name'));

           });}

         })->paginate(10);}
         else{
            $users= User::whereHas('non_tenant')->latest()->paginate(10);

        }
           $users->load('addresses');
              return response()->json($users,200);
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
     * @param  \App\Models\NonTenant  $nonTenant
     * @return \Illuminate\Http\Response
     */
    public function show(NonTenant $nonTenant)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\NonTenant  $nonTenant
     * @return \Illuminate\Http\Response
     */
    public function edit(NonTenant $nonTenant)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\NonTenant  $nonTenant
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, NonTenant $nonTenant)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\NonTenant  $nonTenant
     * @return \Illuminate\Http\Response
     */
    public function destroy(NonTenant $nonTenant)
    {
        //
    }
    public function getnNonTenantsNames()
    {

      $non_tenants =User::select('id','first_name','last_name')->with('non_tenant')->whereHas('non_tenant')->get();
       return response()->json($non_tenants,200);
    }

}
