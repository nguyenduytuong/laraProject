<?php

namespace App\Http\Controllers\Api\User\Manage;

use Exception;
use App\Models\User;
use App\Models\Renter;
use App\Helper\ResponseUtils;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\MsgCode;

class KycController extends Controller
{
     /// KYC Create function 
     public function createKYC(Request $request)
     {
         $user = User::where('id', $request->user->id)->first();
 
         if ($user) {
             DB::beginTransaction();
             try {
                 $renterExist = Renter::where(['phone_number' => $request->phone_number, 'user_id' => $request->user->id])->first();
                 $renterRes = Renter::create([
                     "user_id" => $request->user_id ?: $request->user->id,
                     "name" => $request->name ?: $renterExist->name,
                     "phone_number" => $request->phone_number ?: $renterExist->phone_number,
                     "email" => $request->email ?: $renterExist->email,
                     "cmnd_number" => $request->cmnd_number ?: $renterExist->cmnd_number,
                     "cmnd_front_image_url" => $request->cmnd_front_image_url ?: $renterExist->cmnd_front_image_url,
                     "cmnd_back_image_url" => $request->cmnd_back_image_url ?: $renterExist->cmnd_back_image_url,
                     "image_url" => ($request->image_url == null ? "https://data3gohomy.ikitech.vn/api/SHImages/ODLzIFikis1681367637.jpg" : $request->image_url) ?: $renterExist->image_url,
                     "address" => $request->address ?: $renterExist->address,
                     "is_hidden" => false,
                     "type" => $user->is_host == 1 ? 1 : 0,
                     "date_of_birth" => $request->date_of_birth ?: $renterExist->date_of_birth,
                     "date_range" => $request->date_range ?: $renterExist->date_range,
                     "sex" => $request->sex ?: $renterExist->sex,
                     "job" => '',
                 ]);
                 DB::commit();
             } catch (Exception $e) {
                 DB::rollBack();
                 throw new Exception($e->getMessage());
             }
             $user->update([
                 'kys_status' => true
             ]);
         } else {
             return "User not found";
         }
 
         return ResponseUtils::json([
             'code' => Response::HTTP_OK,
             'success' => true,
             'msg_code' => MsgCode::SUCCESS[0],
             'msg' => MsgCode::SUCCESS[1],
             'data' => $renterRes,
         ]);
     }
}
