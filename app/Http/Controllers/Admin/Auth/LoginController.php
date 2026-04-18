<?php
/**
 * Created by PhpStorm.
 * User: irock
 * Date: 05.04.2019
 * Time: 14:26
 */

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Auth\LoginController as LaravelLogin;
use Illuminate\Http\Request;

class LoginController extends LaravelLogin
{
    protected $redirectTo = '/';

    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function username()
    {
        return 'username';
    }

    protected function credentials(Request $request)
    {
        return [
            'username' => $request->input('username'),
            'password' => $request->input('password'),
            'active' => 1
        ];
    }
}
