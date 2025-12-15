<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * Show Login Page
     */

   public function show()
   {
    return view('auth.login');
   }

   /**
    * Login Post Request
    */

   public function login(Request $request)
   {

    $validated=$request->validate([
        'email'=>'required|email|exists:users,email',
        'password'=>'required|string|min:8',
    ]);

    $user=User::where('email',$validated['email'])->first();

    if(!$user || !Hash::check($validated['password'], $user->password))
        {
            return redirect()->back()
            ->withErrors([
                'email'=>'Please check your email or password !'
                ])->withInput();
        }

        session()->flash('success', 'Logged in successfully!');

        Auth::login($user);


        $request->session()->regenerate();
         

        if($user->role==='vendor')
         return redirect()->route('filament.admin.pages.dashboard');

 
       return redirect()->route('home');
    
   }

   /**
    *Logout
    *
    */

   public function logout(Request $request)
     {

    Auth::logout();

    $request->session()->invalidate();

    $request->session()->regenerateToken();

    return redirect()->route('home');
    }
}
