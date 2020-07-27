<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

use App\Building;
use App\Log;

use Help;

class BuildingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Building::class);

        $search = $request->search;
        $partner = $request->partner;
        $limit = $request->limit;

        switch(Auth::user()->roles){
            case 'ADMINISTRATOR' :
                 if($limit || $search || $partner){
                    $building = Building::where(function($query) use($search){
                        $query->where('building_code', 'like', '%'.$search.'%')
                              ->orWhere('building_name', 'like', '%'.$search.'%');
                    })->where('partner_id', '=', $partner)->with(['city', 'province'])->limit($limit)->get();
                } else {
                    $building = Building::with(['city', 'province'])->get();
                }
            break;

            case 'PARTNER' : 
                if($search){
                    $building = Building::whereHas('partner', function(Builder $query){
                        $auth = Auth::user()->load('partner_user.partner');
                        $query->where('id', '=', $auth->partner_user->partner->id);
                    })->where(function($query) use($search){
                        $query->where('building_code', 'like', '%'.$search.'%')
                              ->orWhere('building_name', 'like', '%'.$search.'%');
                    })->with(['city', 'province'])->get();
                } else {
                    $building = Building::whereHas('partner', function(Builder $query){
                        $auth = Auth::user()->load('partner_user.partner');
                        $query->where('id', '=', $auth->partner_user->partner->id);
                    })->with(['city', 'province'])->get();
                }
                
                
            break;
        }
        
        return response()->json([
            'status' => true,
            'message' => 'Success fetch building',
            'results' => $building
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', Building::class);

        $validator = Validator::make($request->all(), [
            'building_name' => 'required|string',
            'partner_id' => 'required|exists:partners,id',
            'phone' => 'required|string',
            'address' => 'required|string',
            'city_id' => 'required|exists:cities,id',
            'province_id' => 'required|exists:provinces,id'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Fields Required',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $building = new Building;
            $building->building_name = $request->building_name;
            $building->building_code = Help::numberCode('BD', 'buildings', 'building_code');
            $building->type = $request->type;
            $building->phone = $request->phone;
            $building->fax = $request->fax;
            $building->email = $request->email;
            $building->longitude = $request->longitude;
            $building->latitude = $request->latitude;
            $building->address = $request->address;
            $building->city_id = $request->city_id;
            $building->province_id = $request->province_id;
            $building->partner_id = $request->partner_id;
            $building->other_information = $request->other_information;
            $building->save();
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed add building',
                'error' => $e->getMessage()
            ], 500);
        }

        try {
            $log = new Log;
            $log->user_id = Auth::id();
            $log->description = 'Add Building #'.$building->id;
            $log->reference_id = $building->id;
            $log->url = '#/partner/building/'.$building->id;

            $log->save();
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed add log',
                'error' => $e->getMessage()
            ], 500);
        }

        DB::commit();

        return response()->json([
            'status' => true,
            'message' => 'Success add building',
            'results' => $building,
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        switch(Auth::user()->roles){
            case 'ADMINISTRATOR' :
                $building = Building::with(['city', 'province', 'schedules', 'partner'])->findOrFail($id);
            break;

            case 'PARTNER' : 
                $building = Building::whereHas('partner', function(Builder $query){
                    $auth = Auth::user()->load('partner_user.partner');
                    $query->where('id', '=', $auth->partner_user->partner->id);
                })->with(['city', 'province', 'schedules'])->findOrFail($id);
            break;

            case 'ENGINEER' : 
                $building = Building::with(['city', 'province', 'schedules'])->findOrFail($id);
            break;
        }

        $this->authorize('view', $building);
        
        return response()->json([
            'status' => true,
            'message' => 'Success fetch building',
            'results' => $building
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $building = Building::findOrFail($id);

        $this->authorize('delete', $building);

        DB::beginTransaction();

            try {
                $building->delete();
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed delete building',
                    'error' => $e->getMessage()
                ], 500);
            }

            try {
                $log = new Log;
                $log->user_id = Auth::id();
                $log->description = 'Delete building #'.$building->id;
                $log->reference_id = $building->id;
                $log->url = '#/partner';

                $log->save();
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed add log',
                    'error' => $e->getMessage()
                ], 500);
            }
        
        DB::commit();

        return response()->json([
            'status' => true,
            'message' => 'Success archive building',
            'results' => $building,
        ], 200);
    }
}
