<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use Hash;
use App\User;
use App\Log;
use Auth;

class SettingController extends Controller
{
    public function profile()
    {
        $auth = Auth::user();

        if($auth->roles === 'ADMINISTRATOR') {
            $profile = User::with(['administrator.city', 'administrator.province', 'logs'])->findOrFail($auth->id);
        } elseif($auth->roles === 'ENGINEER'){
            $profile = User::with(['engineer.city', 'engineer.province', 'logs'])->findOrFail($auth->id);
        } else {
            $profile = User::with(['partner_user.partner.city', 'partner_user.partner.province', 'logs'])->findOrFail($auth->id);
        }


        return response()->json([
            'status' => true,
            'message' => 'Success fetch profile',
            'results' => $profile
        ], 200);
    }

    public function change_password(Request $request)
    {
        $auth = Auth::user();

        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string',
            'new_password' => 'required|string',
            'retype_password' => 'required|string|same:new_password',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Fields Required',
                'errors' => $validator->errors()
            ], 422);
        }

        if(!Hash::check($request->old_password, $auth->password )) 
        {
             return response()->json([
                'status' => false,
                'message' => 'Wrong password'
            ], 422);
        }

        DB::beginTransaction();
            try {
                $auth->password = bcrypt($request->new_password);
                $update = $auth->save();
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed change password',
                    'error' => $e->getMessage()
                ], 500);
            }

            try {
                $log = new Log;
                $log->user_id = $auth->id;
                $log->description = 'Change Password';
                $log->reference_id = $auth->id;
                $log->url = '#/setting';
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
            'message' => 'Success change password',
        ], 200);
    }

    public function log()
    {
        $auth = Auth::user();

        $logs = Log::where('user_id', $auth->id)->orderBy('created_at', 'desc')->limit(15)->get();

        return response()->json([
            'status' => true,
            'message' => 'Success fetch logs',
            'results' => $logs
        ], 200);
    }
}
