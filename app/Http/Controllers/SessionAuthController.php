<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SessionAuthController extends Controller
{

    public function csrf(Request $request)
    {
        if (!$request->session()->has('csrf_token')) {
            $request->session()->regenerate();
            $request->session()->put(
                'csrf_token',
                bin2hex(random_bytes(32))
            );
        }


        return response()->json([
            "csrfToken" => $request->session()->get('csrf_token')
        ]);
    }



    public function register(Request $request)
    {
        try{
            $this->validateCsrf($request);


            $data = $request->validate([
                'name'=>'required|min:2',
                'email'=>'required|email|unique:users',
                'password'=>[
                    'required',
                    'min:8',
                    'regex:/[A-Z]/',
                    'regex:/[a-z]/',
                    'regex:/\d/',
                    'regex:/[\W_]/'
                ]
                
            ]);


            $id = DB::table('users')->insertGetId([
                'name'=>$data['name'],
                'email'=>$data['email'],
                'password'=>Hash::make($data['password']),
                'created_at'=>now(),
                'updated_at'=>now(),
            ]);

            $request->session()->regenerate();

            $request->session()->put(
                'csrf_token',
                bin2hex(random_bytes(32))
            );


            $request->session()->put('user', [
                "id"=>$id,
                "name"=>$data['name'],
                "email"=>$data['email'],
                "role"=>"user",
            ]);


            return response()->json([
                "success"=>true
            ]);

        } catch(\Throwable $e) {

            return response()->json([
                "message"=>"Server error"
            ],500);

        }
    }




    public function login(Request $request)
    {
        try {
            $this->validateCsrf($request);


            $user = DB::table('users')
                ->where('email',$request->email)
                ->first();


            if(!$user || !Hash::check(
                $request->password,
                $user->password
            )){
                return response()->json([
                    "message"=>"Invalid credentials"
                ],401);
            }


            $request->session()->regenerate();

            $request->session()->put(
                'csrf_token',
                bin2hex(random_bytes(32))
            );

            $request->session()->put('user',[
                "id"=>$user->id,
                "name"=>$user->name,
                "email"=>$user->email,
                "role" => $user->role ?? "user"
            ]);


            return response()->json([
                "success"=>true,
                "user"=>$request->session()->get('user')
            ]);

        } catch(\Throwable $e) {

            return response()->json([
                "message"=>"Server error"
            ],500);

        }
    }




    public function me(Request $request)
    {
        return response()->json([
            "authenticated" => $request->session()->has('user'),
            "user"=>$request->session()->get('user')
        ]);
    }





    public function logout(Request $request)
    {

        $request->session()->flush();

        $request->session()->regenerate();

        $request->session()->put(
            'csrf_token',
            bin2hex(random_bytes(32))
        );

        return response()->json([
            "success"=>true
        ]);
    }





    private function validateCsrf(Request $request)
    {

        $token = $request->header('X-CSRF-TOKEN');


        if(
            !$token ||
            $token !== $request->session()->get('csrf_token')
        ){
            abort(response()->json([
                "message"=>"CSRF token mismatch"
            ],419));
        }
    }

}