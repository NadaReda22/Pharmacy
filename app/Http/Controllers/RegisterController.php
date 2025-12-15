<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\RegisteredMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\RequiredIf;
use Illuminate\Foundation\Console\MailMakeCommand;

class RegisterController extends Controller
{

    /**
     * 
     * Show Register Page
     */
   public function show()
   {
    return view('auth.register');
   }


     /**
     * 
     * Register Post Request
     */
   public function register(Request $request)
   {

    $validated=$request->validate([
        'name'=>'string|required',
        'email'=>'required|email|unique:users,email',
        'phone'=>'nullable|string|min:11|max:11',
        'password'=>'required|string|min:8',
        'pharmacy_id' => 'required_if:role,vendor|integer|exists:pharmacies,id',
        'role'=>'required|in:vendor,client',
        'license_file'=>'required_if:role,vendor|mimes:png,jpg,jpeg',
    ]);

    //Password validation

    $validated['password']=Hash::make($validated['password']);

    $user= User::create($validated);

    /**
     * If the user is a pharmacy owner has products to show
     * sending his license as a proof
     */

    if($request->hasFile('license_file'))
    {
      $file=$request->file('license_file');
      $original_name=pathinfo($file->getClientOriginalName(),PATHINFO_FILENAME);

      $originalExtension=$file->getClientOriginalExtension();

      $file_name=$original_name .'_' .time() . '.' . $originalExtension;

      $file->storeAs('uploads/licenses',$file_name,'public');
      $user->license_file='uploads/licenses/'.$file_name;
    }

    $user->save();

    Auth::login($user);
    $licensePath=$user->license_file;

    //sending registerd users mails in queue 
    Mail::to($request->email) ->later(now()->addMinutes(1), new RegisteredMail($user, $licensePath));

    //
    return view('register_verification_notification');

   }
}
