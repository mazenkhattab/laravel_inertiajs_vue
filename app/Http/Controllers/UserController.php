<?php

namespace App\Http\Controllers;

use App\Models\User;

use Inertia\Inertia;
use Illuminate\Http\Request;


class UserController extends Controller
{
    public function index(){
        $users= User::all();
        return inertia::render('users/index',[
            'users'=>$users
        ]);
    }

  
    
    public function edit($id)
    {
        $user = User::find($id);

        
        return inertia::render('users/edit',[
            'user'=>$user
        ]);

    }

    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();

        return redirect()->route('users.index')->with('message', 'user deleted Successfully');

    }
    public function update(Request $request)
    {
        $user = User::find($request->id);
        // dd($user);
        $user->update([
            //     'path' => Storage::put('images',$request->image),
            'name'=>$request->name,
            'email'=>$request->email,
            ]);
       
            return redirect()->route('users.index')->with('message', 'user updated Successfully');
        

    }

    public function store(Request $request)
    {
        User::create($request->validate([
          'name' => ['required', 'max:50'],
          'email' => ['required', 'max:50', 'email'],
        ]));

        return redirect()->route('users.index')->with('message', 'user created Successfully');



}

public function create()
{
    return inertia::render('users/create',);
}

}