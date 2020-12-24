<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    public function create () {
        return view('users.create');
    }

    public function show(User $user) {
        return view('users.show', compact('user'));
    }

    public function store(Request $request) {
        $this->validate($request, [
           'name' => 'required|unique:users|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);
        Auth::login($user);
        session()->flash('success', "注册成功~");
        return redirect()->route('users.show', [$user]);
    }

    public function edit(User $user) {
        return view('users.edit', compact('user'));
    }

    public function update(User $user, Request $request) {
        $credntials = $this->validate($request, [
           'name' => 'required|max:50',
            'password' => 'nullable|confirmed|min:6'
        ]);

        if(!empty($request->password)) {
            $updateData = array(
                'name' => $request->name,
                'password' => bcrypt($request->password)
            );
        }else {
            $updateData = array(
                'name' => $request->name,
            );
        }

        $user->update($updateData);
        session()->flash('success', '更新资料成功');

        return redirect()->route('users.show', $user);
    }
}
