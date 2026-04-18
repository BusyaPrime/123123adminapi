<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Domain\UserDeleteRequests\Model\UserDeleteRequest;
use App\Domain\Users\Models\User;

class UserDeleteRequestsController extends Controller
{
    public function index(){
        $userdeleterequests = UserDeleteRequest::orderByDesc('created_at')->paginate();
        return view(
            'admin.userdeleterequests.index',
            [
                'userdeleterequests' => $userdeleterequests
            ]
        );
    }

    public function deleteUser(Request $request, $delete_req){
        try {
            $user = User::find($request->input('user_id'));
            $deleteUser = UserDeleteRequest::find($delete_req);
            if($deleteUser->status != UserDeleteRequest::STATUS_REJECTED && $deleteUser->status != UserDeleteRequest::STATUS_ACCEPTED){
                $deleteUser->status = UserDeleteRequest::STATUS_ACCEPTED;
                $deleteUser->update();
                $deleteUser->user->delete();
                return redirect()->route('admin.deleterequests.index')->with('inner_success', trans('admin.update_success'));
            } else return redirect()->back()->with('danger', trans('admin.update_failed'));
        } catch (\Exception $exception) {
            report($exception);
            return redirect()->back()->with('danger', trans('admin.update_failed'));
        }

    }
    
    public function declineUser(Request $request){
        try {
            $deleteUser = UserDeleteRequest::find($request->input('request_id'));
			if($deleteUser->status != UserDeleteRequest::STATUS_ACCEPTED){
                $deleteUser->status = UserDeleteRequest::STATUS_REJECTED;
			    $deleteUser->update();
            }
            return redirect()->route('admin.deleterequests.index')->with('inner_success', trans('admin.update_success'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('danger', trans('admin.update_failed'));
        }
    }
}
