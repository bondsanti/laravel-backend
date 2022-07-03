<?php

namespace App\Http\Controllers;

use App\Models\User; //conect database Eloquent
use Illuminate\Http\Request;
//use App\Http\Requests\UserRegisterRequest;
use App\Http\Resources\User as UserResources;

class AuthController extends Controller
{
    public function register(Request $request){
        //dd('eiei');
        $this->validate($request,[
            'email' => 'email|required|unique:users,email',
            'name' => 'required',
            'password' => 'required|min:6'
        ],
        [
            'email.email'=>"รูปแบบอีเมล์ไม่ถูกต้อง",
            'email.required'=>"required",
            'email.unique'=>"มีอีเมล์นี้ในระบบแล้ว",
            'name.required'=>"กรุณาป้อนชื่อ",
            'password.required'=>"กรุณาป้อนรหัสผ่าน",
            'password.min'=>"รหัสผ่านต้องมากกว่า 6 ตัวอักษร"
        ]);

        $user = User::create([
            'email' => $request->email,
            'name' => $request->name,
            'password' => bcrypt($request->password)
        ]);

        if(!$token = auth()->attempt($request->only(['email', 'password']))){
            return abort(401);
        }

        return (new UserResources($request->user()))->additional([
            'meta'=>[
                'token' => $token
            ]
        ]);
    }
}
