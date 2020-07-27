<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Engineer;
use App\User;
use App\Log;

class EngineerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewAny', Engineer::class);
        
        $engineer = Engineer::with(['user', 'city', 'province'])->get();
        
        return response()->json([
            'status' => true,
            'message' => 'Success fetch engineer',
            'results' => $engineer
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
        $this->authorize('create', Engineer::class);

        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string',
            'email' => 'required|string',
            'phone' => 'required|string',
            'address' => 'required|string',
            'username' => 'required|string',
            'city_id' => 'required|exists:cities,id',
            'province_id' => 'required|exists:provinces,id',
            'active' => 'required|in:Y,N',
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
            $user = new User;
            $user->username = $request->username;
            $user->email = $request->email;
            $user->password = bcrypt($request->username);
            $user->roles = 'ENGINEER';
            $user->active = $request->active;
            $user->save();
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed add user',
                'error' => $e->getMessage()
            ], 500);
        }

        try {
            $photo = $request->file('photo');
        
            if($photo){
                $name       = $photo->getClientOriginalName();
                $filename   = pathinfo($name, PATHINFO_FILENAME);
                $extension  = $photo->getClientOriginalExtension();

                $store_as   = $filename.'_'.time().'.'.$extension;

                $photo->storeAs('public/engineer/', $store_as);
            } else {
                $store_as = NULL;
            }

            $engineer = new Engineer;
            $engineer->full_name = $request->full_name;
            $engineer->user_id = $user->id;
            $engineer->city_id = $request->city_id;
            $engineer->province_id = $request->province_id;
            $engineer->phone = $request->phone;
            $engineer->address = $request->address;
            $engineer->other_information = $request->other_information;
            $engineer->photo = $store_as;
            $engineer->save();
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed add engineer',
                'error' => $e->getMessage()
            ], 500);
        }

        try {
            $log = new Log;
            $log->user_id = Auth::id();
            $log->description = 'Add Engineer #'.$engineer->id;
            $log->reference_id = $engineer->id;
            $log->url = '#/engineer/'.$engineer->id;

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
            'message' => 'Success add engineer',
            'results' => $engineer,
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
        $engineer = Engineer::with(['user', 'city', 'province', 'teams.schedule.building'])->findOrFail($id);

        $this->authorize('view', $engineer);

        return response()->json([
            'status' => true,
            'message' => 'Success fetch specific engineer',
            'results' => $engineer
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
        $engineer = Engineer::findOrFail($id);

        $this->authorize('delete', $engineer);

        DB::beginTransaction();

            try {
                $delete_eng = $engineer->delete();
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed delete engineer',
                    'error' => $e->getMessage()
                ], 500);
            }

            try {
                $log = new Log;
                $log->user_id = Auth::id();
                $log->description = 'Delete Engineer #'.$engineer->id;
                $log->reference_id = $engineer->id;
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
            'message' => 'Success archive engineer',
            'results' => $engineer,
        ], 200);
    }

    public function picture($filename)
    {
        $path = base_path().'/storage/app/public/engineer/'.$filename;
        return Response::download($path);  
    }
}
