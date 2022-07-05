<?php

namespace App\Http\Controllers;

use App\Models\User; //conect database Eloquent


use App\Http\Resources\User as UserResources;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserLoginRequest;
use Illuminate\Http\Request;



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
            'email.required'=>"กรุณาป้อนอีเมล์",
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
                'token' => $token,
                'token_type' => 'bearer',
            ]
        ]);
    }
    public function login(UserLoginRequest $request){
        $this->validate($request,[
            'email' => 'email|required',
            'password' => 'required|min:6'
        ],
        [
            'email.email'=>"รูปแบบอีเมล์ไม่ถูกต้อง",
            'email.required'=>"กรุณาป้อนอีเมล์",
            'password.required'=>"กรุณาป้อนรหัสผ่าน",
            'password.min'=>"รหัสผ่านต้องมากกว่า 6 ตัวอักษร"
        ]);

        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['errors' =>['email' =>'ไม่พบอีเมล์นี้ในระบบ'] ], 401);
        }

        return $this->respondWithToken($token);

        // if(!$token = auth()->attempt($request->only(['email', 'password']))){
        //     return response()->json([
        //         'errors' =>[
        //             'email' => 'ไม่พบอีเมล์นี้ในระบบ'
        //         ]
        //         ],422);
        // }

        // return (new UserResources($request->user()))->additional([
        //     'meta'=>[
        //         'token' => $token,
        //         'token_type' => 'bearer'
        //     ]
        // ]);
    }
    public function profile(Request $request){
        //dd('ddd');
     //return new UserResources($request->user());
     return response()->json(auth()->user());

    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
