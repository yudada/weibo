<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class PasswordController extends Controller
{

    public function __construct()
    {
        $this->middleware('throttle:2,1',[
            'only' => ['showLinkRequestForm']
        ]);

        $this->middleware('throttle:3,10', [
            'only' => ['sendResetLinkEmail']
        ]);
    }

    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    public function sendResetLinkEmail(Request $request)
    {
        // 1. 验证邮箱
        $request->validate(['email' => 'required|email']);
        $email = $request->email;

        // 2. 获取对应用户
        $user = User::where("email", $email)->first();

        // 3. 如果不存在
        if (is_null($user)) {
            session()->flash('danger', '邮箱未注册');
            return redirect()->back()->withInput();
        }

        // 4. 生成 Token，会在视图 emails.reset_link 里拼接链接
        $token = hash_hmac('sha256', Str::random(40), config('app.key'));

        // 5. 入库，使用 updateOrInsert 来保持 Email 唯一
        DB::table('password_resets')->updateOrInsert(['email' => $email], [
            'email' => $email,
            'token' => Hash::make($token),
            'created_at' => new Carbon,
        ]);

        // 6. 将 Token 链接发送给用户
        Mail::send('emails.reset_link', compact('token'), function ($message) use ($email) {
            $message->to($email)->subject("忘记密码");
        });

        session()->flash('success', '重置邮件发送成功，请查收');
        return redirect()->back();
    }

    public function showResetForm (Request $request) {
        $token = $request->route()->parameter('token');

        return view('auth.passwords.reset', compact('token'));
    }

    public function reset(Request $request) {



        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|confirmed|min:8'
        ]);

        $email = $request->email;
        $token = $request->token;
        $expires = 60*10;

        $user = User::where('email', $email)->first();
        if(is_null($user)) {
            session()->flash('danger', '邮箱未注册');
            return redirect()->back()->withInput();
        }

        $record = (array) DB::table('password_resets')->where('email', $email)->first();
        if($record) {
            if(Carbon::parse($record['created_at'])->addSeconds($expires)->isPast()) {
                session()->flash('danger', '链接已过期，请重新发送');
                return redirect()->back();
            }

            if(!Hash::check($token, $record['token'])) {
                session()->flash('danger', '令牌错误');
                return redirect()->back();
            }

            $user->update([
                'password' => bcrypt($request->password)
            ]);

            session()->flash('success', '密码重置成功，请使用新密码登录');
            return redirect()->route('login');

        }else {
            session()->flash('danger', '未找到重置记录');
            return redirect()->back();
        }
    }
}