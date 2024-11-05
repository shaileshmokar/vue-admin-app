<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Login 
     */
    public function login()
    {
        return Inertia::render('Auth/Login');
    }

    /**
     * Login
     */
    public function loginStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8']
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $credentials = [
            'email'=> $request->email,
            'password'=> $request->password
        ];

        if(Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'Provided credentials do not match.'
        ]);
    }

    /**
     * Signup
     */
    public function signup()
    {
        return Inertia::render('Auth/Signup');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function signupStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name'=> $request->name,
            'email'=> $request->email,
            'password'=> bcrypt($request->password)
        ]);

        return redirect()->route('login');
    }

    /**
     * Logout
     */
    public function logout(Request $request) 
    {
        Auth::logout();
        $request->session()->invalidate();

        return redirect()->route('login');
    }
}
