<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class UsersController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth', [
           'except' => ['show', 'create', 'store', 'index', 'confirmEmail']
        ]);

        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }

    public function index () {
        $users = User::paginate(6);
        return view('users.index', compact('users'));
    }

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
//        Auth::login($user);
//        session()->flash('success', "注册成功~");
        //return redirect()->route('users.show', [$user]);
        $this->sendEmailConfirmationTo($user);
        session()->flash('success', '验证邮件已发送到你的注册邮箱上，请注意查收。');
        return redirect('/');
    }

    public function edit(User $user) {
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }

    public function update(User $user, Request $request) {
        $this->authorize('update', $user);
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

    public function destroy(User $user) {
        $this->authorize('destroy', $user);
        $user->delete();
        session()->flash('success', '删除用户成功');
        return back();
    }

    public function sendEmailConfirmationTo ($user) {
        $view = 'emails.confirm';
        $data = compact('user');
        $from = '1115338663@qq.com';
        $name = 'Roi';
        $to = $user->email;
        $subject = "感谢注册Weibo应用,请确认您的邮箱.";

        Mail::send($view,$data,function ($message) use ($from, $name, $to, $subject) {
           $message->from($from, $name)->to($to)->subject($subject);
        });
    }

    public function confirmEmail ($token) {
        $user = User::where('activation_token', $token)->firstOrFail();

        $user->activated = true;
        $user->activation_token = null;
        $user->save();

        Auth::login($user);

        session()->flash('success', '恭喜你，激活成功！');
        return redirect()->route('users.show', [$user]);
    }
}
