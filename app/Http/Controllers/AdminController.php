<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminLoginRequest;
use App\Http\Requests\AdminRequest;
use App\Http\Requests\SiswaUpdateRequest;
use App\Http\Requests\UpdateAdminRequest;
use App\Http\Resources\AdminResponse;
use App\Http\Resources\SiswaResponse;
use App\Models\Admin;
use App\Models\Siswa;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    //

    public function store(AdminRequest $request): JsonResponse 
    {
        $data = $request->validated();

        if(Admin::where('username', $data["username"])->count()==1 ){
            throw new HttpResponseException(response([
                'errors' => [
                    "message"=>[
                        "Username has been used."
                    ]
                ]
                    ], 400));
        }

        $admin = new Admin($data);
        $admin->password = Hash::make($data['password']);
        $admin->save();

        return (new AdminResponse($admin))->response()->setStatusCode(201);
    } 

    public function login(AdminLoginRequest $request) : AdminResponse 
    {
        $data = $request->validated();
        $user = Admin::where("username", $data["username"])->first();

        if(!$user || !Hash::check($data['password'], $user->password)) {
            throw new HttpResponseException(response([
                "errors"=>[
                    "message"=>[
                        "Username or password is wrong."
                    ]
                ]
            ], 401));
        }
        $user->token = Str::uuid()->toString();
        $user->save(); 
        return new AdminResponse($user);
    }

    public function get(Request $request): AdminResponse
    {
        $admin = Auth::user();
        return new AdminResponse($admin);
    }
    public function getAdmin(int $id): AdminResponse
    {
        $admin = Admin::find($id);
        if(!$admin){
            throw new HttpResponseException(response([
                "errors"=>[
                    "message" => [
                        "Not found."
                    ]
                ]
                    ],404));
        }
        return new AdminResponse($admin);
    }

    public function updateSiswaByAdmin(SiswaUpdateRequest $request, int $id) : JsonResponse
    {
        // validasi data 
        $data = $request->validated();
        $siswa = Siswa::with("kelas:id,kelas")->find($id);

        if(!$siswa){
            throw new HttpResponseException(response([
                "errors"=>[
                    "message" => [
                        "Not found."
                    ]
                ]
                    ],404));
        }

        if(isset($data['password'])){
            $data["password"] = Hash::make($data['password']);
        }else {
            // If password is not set, remove it from the data array
            unset($data['password']);
        }
        $siswa->fill($data);
        $siswa->save();

        return (new SiswaResponse($siswa))->response()->setStatusCode(201);
    }

    public function update(UpdateAdminRequest $request, int $id) : JsonResponse
    {
        $data = $request->validated();

        $admin = Admin::find($id);
        if(isset($data['username'])){
            $admin->username = $data['username'];
        }
        if(isset($data['password'])){
            $admin->password = Hash::make($data['password']);
        }

        $admin->save();
        return (new AdminResponse($admin))->response()->setStatusCode(201);

    }

    public function logout(Request $request): JsonResponse
    {
        $admin = Admin::find(Auth::user()->id);
        $admin->token = null;
        $admin->save();
        return response()->json([
            "data" => true
        ])->setStatusCode(200);
    }

    public function getAllAdmin() : JsonResponse {
        $admins = Admin::select('id', 'username', 'password')->get();
        return response()->json([
            "data" => $admins
        ], 200);
    }

    public function delete(int $id): JsonResponse {
        $data = Admin::find($id);

        if(!$data){
            throw new HttpResponseException(response([
                "errors"=>[
                    "message" => [
                        "Not found."
                    ]
                ]
                    ],404));
        }

        $data->delete();
        $admins = Admin::select('id', 'username', 'password')->get();
        return response()->json([
            "data" => $admins
        ], 200);
    }
}
