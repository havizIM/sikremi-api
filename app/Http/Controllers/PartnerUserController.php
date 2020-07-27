<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\PartnerUser;
use App\User;
use App\Log;

class PartnerUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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
        $this->authorize('create', PartnerUser::class);

        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string',
            'partner_id' => 'required|exists:partners,id',
            'email' => 'required|string',
            'phone' => 'required|string',
            'username' => 'required|string',
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
            $user->roles = 'PARTNER';
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
            $partner_user = new PartnerUser;
            $partner_user->full_name = $request->full_name;
            $partner_user->user_id = $user->id;
            $partner_user->partner_id = $request->partner_id;
            $partner_user->position = $request->position;
            $partner_user->phone = $request->phone;
            $partner_user->address = $request->address;
            $partner_user->other_information = $request->other_information;
            $partner_user->save();
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed add partner user',
                'error' => $e->getMessage()
            ], 500);
        }

        try {
            $log = new Log;
            $log->user_id = Auth::id();
            $log->description = 'Add Partner User #'.$partner_user->id;
            $log->reference_id = $partner_user->id;
            $log->url = '#/partner/user/'.$partner_user->id;

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
            'message' => 'Success add partner user',
            'results' => $partner_user,
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
        $partner_user = PartnerUser::with(['user', 'partner'])->findOrFail($id);

        $this->authorize('view', $partner_user);
        
        return response()->json([
            'status' => true,
            'message' => 'Success fetch partner_user',
            'results' => $partner_user
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
        $partner_user = PartnerUser::findOrFail($id);

        $this->authorize('delete', $partner_user);

        DB::beginTransaction();

            try {
                $partner_user->delete();
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed delete partner user',
                    'error' => $e->getMessage()
                ], 500);
            }

            try {
                $log = new Log;
                $log->user_id = Auth::id();
                $log->description = 'Delete partner user #'.$partner_user->id;
                $log->reference_id = $partner_user->id;
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
            'message' => 'Success archive partner user',
            'results' => $partner_user,
        ], 200);
    }
}
