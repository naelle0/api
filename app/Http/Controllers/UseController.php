<?php

namespace App\Http\Controllers;

use App\Helpers\ApiFormatter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UseController extends Controller
{
    public function index()
    {
        try {
            $data = User::all()->toArray();

            return ApiFormatter::sendResponse(200, 'success', $data);
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {   
            $this->validate($request, [
                'username' => 'required|min:4|unique:users,username',
                'email' => 'required|unique:users,email',
                'password' => 'required|min:6',
                'role' => 'required'
            ]);

            $prosesData = User::create([
                'username' => $request->username, 
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role'=> $request->role
            ]);
            
            if ($prosesData) { // Memeriksa apakah $prosesData adalah instance model yang valid
                return ApiFormatter::sendResponse(200, 'success', $prosesData);
            } else {
                return ApiFormatter::sendResponse(400, 'bad_request', 'Gagal menambahkan data, silahkan coba lagi !');
            }
        } catch (\Exception $err){
            return ApiFormatter::sendResponse(400, 'bad_request', $err->getMessage());
        }
    }
    public function show($id)
    {
        try {
            $data = User::where('id', $id)->first();
            return ApiFormatter::sendResponse(200, 'success', $data);
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    public function update(Request $request, $id)
{
    try {
        $getUser = User::find($id);
        $this->validate($request, [
            'username' => 'required|min:4|unique:users,username,' . $id,
            'email' => 'required|unique:users,email,' . $id,
            'role' => 'required'
        ]);


        if ($request->password) {
            $UpdateUser = $getUser->update([
                'username' => $request->username,
                'email' => $request->email,
                'password' => hash::make($request->password),
                'role' => $request->role
            ]);
        }else {
            $UpdateUser = $getUser->update([
                'username' => $request->username,
                'email' => $request->email,
                'role' =>$request->role, 
            ]);
        }
       

        if ($checkProses) {
            $data = User::where('id', $id)->first();

            return ApiFormatter::sendResponse(200, 'success', $data);
        }
    } catch (\Exception $err) {
        return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
    }
}

    public function destroy($id)
    {
        try {
            $checkproses = User::where('id', $id)->delete();

            if ($checkproses) {
                return
                    ApiFormatter::sendResponse(200, 'succes', 'berhasil hapus data User!');
            }
        } catch (\Exception $err) {
            return
                ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    public function trash()
    {
        try {
            $data = User::onlyTrashed()->get();

            return
                ApiFormatter::sendResponse(200, 'succes', $data);
        } catch (\Exception $err) {
            return
                ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    public function restore($id)
    {
        try {
            $checkRestore = User::onlyTrashed()->where('id',$id)->restore();

            if ($checkRestore) {
                $data = User::where('id', $id)->first();
                return ApiFormatter::sendResponse(200, 'succes', $data);
            }
        }catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    public function permanentDelete($id)
    {
        try{
            $cekPermanentDelete = User::onlyTrashed()->where('id', $id)->forceDelete();

            if ($cekPermanentDelete) {
                return
                ApiFormatter::sendResponse(200, 'success','Berhasil menghapus data secara permanen' );
            }
        } catch (\Exception $err) {
            return
            ApiFormatter::sendResponse(400,'bad_request', $err->getMessage());
        }

    }
    public function login(Request $request)
    {
        try {
            $this->validate($request, [
                'email' => 'required',
                'password' => 'required|min:8',
            ], [
                'email.required' => 'Email Harus Diisi',
                'password.required' => 'Password Harus Diisi',
                'password.min' => 'Password Minimal 8 Karakter'
            ]);

            $user = User::where('email', $request->email)->first(); //mencari dan mendapatkan data user berdasarkan email

            if (!$user) {
                return ApiFormatter::sendResponse(400, false, 'Login Failed! User Doesnt Exists');
            } else {
                $isValid = Hash::check($request->password, $user->password);

                if (!$isValid) {
                    return ApiFormatter::sendResponse(400, false, 'Login Failed! Password Doesnt Match');
                }else {
                    $generateToken = bin2hex(random_bytes(40));
                    //bin2hex Mengonversi bilangan biner ke heksadesimal.

                    $user->update([
                        'token' => $generateToken
                    ]);
                    return ApiFormatter::sendResponse(200, 'Login Succesfully', $user);
                }
            }
       }catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, false, $err->getMessage());
       }
    }
    public function __construct()
{
    $this->middleware('auth:api');
}
    public function logout(Request $request)
    {
        try{
            $this->validate($request, [
                'email' => 'required',
            ]);

            $user = User::where('email', $request->email)->first();

            
            if (!$user) {
                return ApiFormatter::sendResponse(400, 'Login Failed! User Doesnt Exists');
            } else {
                if (!$user->token) {
                    return ApiFormatter::sendResponse(400,'Logout Failed! User Doenst Login Sciene');
                } else {
                    $logout = $user->update(['token' => null]);

                    if ($logout) {
                        return ApiFormatter::sendResponse(200, 'Logout Succesfully');
                    }
                }
            }
        }catch (\Exception $err) {
        return ApiFormatter::sendResponse(400, $err->getMessage());
    }
  }
}

   

