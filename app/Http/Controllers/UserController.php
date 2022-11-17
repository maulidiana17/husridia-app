<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function register()
    {
        return view("user/register");
    }

    public function processRegister(request $request)
    {
        $request->validate([
            "name"             => "required",
            "email"            => "required|unique:users",
            "password"         => "required|min:6",
            "reenter_password" => "required|same:password",
        ]);

        $data = $request->all();

        $data['password'] = bcrypt($data['password']);
        $data['level'] = 0; // Member
        
        $user = User::create($data);
        
        event(new Registered($user));

        return redirect("user/registration-success/{$user->id}")->withSuccess("Pendaftaran berhasil!");
    }

    public function login()
    {
        return view("user/login");
    }

    public function registerSuccess($userID)
    {
        return view("user/register-success", [
            "userID" => $userID,
        ]);
    }

    public function processLogin(Request $request)
    {
        //form validation
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
 
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
        
        dd("login berhasil");
            return redirect('member');
        }
 
        else {
            return redirect('user/login')->withError("Login Gagal");
        }
    }

    public function processLogout(Request $request)
    {
    Auth::logout();
 
    return redirect('/user/login');
    }
}
