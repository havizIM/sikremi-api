<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Administrator;
use App\User;
use App\Log;

class AdministratorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewAny', Administrator::class);
        
        $administrator = Administrator::with(['user', 'city', 'province'])->get();
        
        return response()->json([
            'status' => true,
            'message' => 'Success fetch administrator',
            'results' => $administrator
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
        $this->authorize('create', Administrator::class);

        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string',
            'email' => 'required|string',
            'phone' => 'required|string',
            'address' => 'required|string',
            'username' => 'required|string',
            'city_id' => 'required|exists:cities,id',
            'province_id' => 'required|exists:provinces,id',
            'active' => 'required|in:Y,N',
            'level' => 'required|in:ADMIN,MANAGER',
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
            $user->roles = 'ADMINISTRATOR';
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

                $photo->storeAs('public/administrator/', $store_as);
            } else {
                $store_as = NULL;
            }

            $administrator = new Administrator;
            $administrator->full_name = $request->full_name;
            $administrator->user_id = $user->id;
            $administrator->city_id = $request->city_id;
            $administrator->province_id = $request->province_id;
            $administrator->phone = $request->phone;
            $administrator->address = $request->address;
            $administrator->level = $request->level;
            $administrator->other_information = $request->other_information;
            $administrator->photo = $store_as;
            $administrator->save();
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed add administrator',
                'error' => $e->getMessage()
            ], 500);
        }

        try {
            $log = new Log;
            $log->user_id = Auth::id();
            $log->description = 'Add Administrator #'.$administrator->id;
            $log->reference_id = $administrator->id;
            $log->url = '#/administrator/'.$administrator->id;

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
            'message' => 'Success add administrator',
            'results' => $administrator,
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
        $administrator = Administrator::with(['user', 'city', 'province'])->findOrFail($id);

        $this->authorize('view', $administrator);

        return response()->json([
            'status' => true,
            'message' => 'Success fetch specific administrator',
            'results' => $administrator
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
        $administrator = Administrator::findOrFail($id);

        $this->authorize('delete', $administrator);

        DB::beginTransaction();

            try {
                $delete_adm = $administrator->delete();
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed delete administrator',
                    'error' => $e->getMessage()
                ], 500);
            }

            try {
                $log = new Log;
                $log->user_id = Auth::id();
                $log->description = 'Delete Administrator #'.$administrator->id;
                $log->reference_id = $administrator->id;
                $log->url = '#/administrator';

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
            'message' => 'Success archive admministrator',
            'results' => $administrator,
        ], 200);
    }

    public function picture($filename)
    {
        $path = base_path().'/storage/app/public/administrator/'.$filename;
        return Response::download($path);  
    }
}
