<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class MainController extends Controller
{
    //returns the view to the login blade file under AUTH folder
    function login(){
        return view('auth.login');
    }
    
    //returns the view to the register blade file under AUTH folder    
    function register(){
        return view('auth.register');
    }

    //creates a function for saving the input into the database
    function save(Request $request){
        
        //Validate requests
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:admins',
            'password' => 'required|min:5|max:12'
        ]);

        //Insert data into database
        $admin = new Admin;
        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->password = Hash::make($request->password);
        $save = $admin->save();

        if($save){
            return back()->with('success','New User has been successfuly added to database');

        }else{
            return back()->with('fail','Something went wrong, try again later');
        }
    }

    function check(Request $request){
            //validate requests
            $request->validate([
                'email' =>'required|email',
                'password' =>'required|min:5|max:12'
            ]);

            $userInfo = Admin::where('email','=', $request->email)->first();

            if(!$userInfo){
                return back()->with('fail','Email is not recognized');
             }else{
                 //check password
                    if(Hash::check($request->password, $userInfo->password)){
                        $request->session()->put('LoggedUser', $userInfo->id);
                        return redirect('admin/dashboard');
                    }else{
                        return back()->with('fail','Incorrect password');
                    }
             }
     
    }

    function logout(){
        if(session()->has('LoggedUser')){
            session()->pull('LoggedUser');
            return redirect('/auth/login');
        }
    }

    //returns the view to the dashboard blade file under ADMIN folder
    function dashboard(){
        $data = ['LoggedUserInfo'=>Admin::where('id','=', session('LoggedUser'))->first()];
        return view('admin.dashboard', $data);
    }
}
