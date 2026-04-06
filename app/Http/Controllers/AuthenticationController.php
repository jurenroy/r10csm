<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthenticationController extends Controller
{
    // Login view
    public function login_view() {
        return view('login');
    }

    // Login process
    public function login_process(Request $request) {
        // Get credentials from the request payload
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required']
        ]);

        // Authenticated credentials
        if(Auth::attempt($credentials, true)) {

            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.'
        ])->onlyInput('username');
    }

    // Logout process
    public function logout(Request $request) {
        Session::flush();
        Auth::logout();
        return redirect()->intended(route('login.view'));
    }

    // Registration API
    public function register(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:6',
        ]);
    
        // Create the user
        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);
    
        return response()->json([
            'message' => 'User registered successfully!',
            'user' => $user,
        ], 201);
    }
}
