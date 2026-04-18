<?php
/**
 * Created by PhpStorm.
 * User: irock
 * Date: 05.04.2019
 * Time: 16:07
 */

namespace App\Http\Controllers\TAdmin;

use App\Domain\Users\Jobs\ChangePasswordJob;
use App\Domain\Users\Requests\ChangePasswordRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('admin2.profile.edit', ['user' => $request->user()]);
    }

    public function update(ChangePasswordRequest $request)
    {
        try{
            $this->dispatchSync(new ChangePasswordJob($request));
            return redirect()->back()->with('success', trans('admin.update_success'));
        } catch (\Exception $exception) {
            return redirect()->back()->withErrors(['current_password' => trans('admin.wrong_current_password')]);
        }
    }
}
