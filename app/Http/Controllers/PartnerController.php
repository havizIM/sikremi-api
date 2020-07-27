<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Partner;
use App\User;
use App\Log;

class PartnerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Partner::class);

        $search = $request->search;
        $limit = $request->limit;

        if($limit || $search){
            $partner = Partner::with(['city', 'province'])->where(function($query) use($search){
                $query->where('partner_name', 'like', '%'.$search.'%');
            })->limit($limit)->get();
        } else {
            $partner = Partner::with(['city', 'province'])->get();
        }
        
        return response()->json([
            'status' => true,
            'message' => 'Success fetch partner',
            'results' => $partner
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
        $this->authorize('create', Partner::class);

        $validator = Validator::make($request->all(), [
            'partner_name' => 'required|string',
            'email' => 'required|string',
            'phone' => 'required|string',
            'address' => 'required|string',
            'city_id' => 'required|exists:cities,id',
            'province_id' => 'required|exists:provinces,id',
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
            $logo = $request->file('logo');
        
            if($logo){
                $name       = $logo->getClientOriginalName();
                $filename   = pathinfo($name, PATHINFO_FILENAME);
                $extension  = $logo->getClientOriginalExtension();

                $store_as   = $filename.'_'.time().'.'.$extension;

                $logo->storeAs('public/partner/', $store_as);
            } else {
                $store_as = NULL;
            }

            $partner = new Partner;
            $partner->partner_name = $request->partner_name;
            $partner->city_id = $request->city_id;
            $partner->province_id = $request->province_id;
            $partner->phone = $request->phone;
            $partner->address = $request->address;
            $partner->fax = $request->fax;
            $partner->email = $request->email;
            $partner->npwp = $request->npwp;
            $partner->website = $request->website;
            $partner->other_information = $request->other_information;
            $partner->logo = $store_as;
            $partner->save();
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed add partner',
                'error' => $e->getMessage()
            ], 500);
        }

        try {
            $log = new Log;
            $log->user_id = Auth::id();
            $log->description = 'Add Partner #'.$partner->id;
            $log->reference_id = $partner->id;
            $log->url = '#/partner/'.$partner->id;

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
            'message' => 'Success add partner',
            'results' => $partner,
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
        $partner = Partner::with(['partner_users.user', 'procedures', 'equipments.building', 'equipments.category', 'city', 'province', 'buildings.city', 'buildings.province', 'categories'])->findOrFail($id);

        $this->authorize('view', $partner);

        return response()->json([
            'status' => true,
            'message' => 'Success fetch specific partner',
            'results' => $partner
        ], 200);
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
        $partner = Partner::findOrFail($id);

        $this->authorize('delete', $partner);

        DB::beginTransaction();

            try {
                $partner->delete();
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed delete partner',
                    'error' => $e->getMessage()
                ], 500);
            }

            try {
                $log = new Log;
                $log->user_id = Auth::id();
                $log->description = 'Delete Engineer #'.$partner->id;
                $log->reference_id = $partner->id;
                $log->url = '#/engineer';

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
            'message' => 'Success archive partner',
            'results' => $partner,
        ], 200);
    }

    public function picture($filename)
    {
        $path = base_path().'/storage/app/public/partner/'.$filename;
        return Response::download($path);  
    }
}
