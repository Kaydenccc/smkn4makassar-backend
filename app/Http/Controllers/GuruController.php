<?php

namespace App\Http\Controllers;

use App\Http\Requests\GuruRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UpdateGuruRequest;
use App\Http\Resources\GuruRequestResponse;
use App\Models\Guru;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class GuruController extends Controller
{
    public function store(GuruRequest $request): JsonResponse {
        $data = $request->validated();

        if(Guru::where('nip', $data["nip"])->count()==1 || Guru::where('email', $data["email"])->count()==1) 
        {
            // lemabalikan error
            throw new HttpResponseException(response([
                'errors'=> [
                    "message"=> [
                        "nip or email already exist"
                    ]
                ]
            ], 400));
        }

        $guru = new Guru($data);
        $guru->password = Hash::make($data["password"]);
        $guru->save();

        return (new GuruRequestResponse($guru))->response()->setStatusCode(201);
    }


    public function login(LoginRequest $request): GuruRequestResponse {
        $data = $request->validated();

        $guru = Guru::where('nip', $data['nip'])->first();
        if(!$guru || !Hash::check($data['password'], $guru->password)) {
            throw new HttpResponseException(response([
                "errors"=>[
                    "message"=>[
                        "NIP or password is wrong."
                    ]
                ]
            ], 401));
        }

        $guru->token = Str::uuid()->toString();
        $guru->save(); 

        return new GuruRequestResponse($guru);

    }

    public function get(Request $request) : GuruRequestResponse 
    {
        $user = Auth::user();
        return new GuruRequestResponse($user);
    }
    public function getGuru(int $id_guru) : GuruRequestResponse 
    {
        $guru = Guru::find($id_guru);
        if(!$guru) {
            throw new HttpResponseException(response([
                "errors"=>[
                    "message"=>[
                        "NIP or password is wrong."
                    ]
                ]
            ], 401));
        }
        return new GuruRequestResponse($guru);
    }

    public function update(UpdateGuruRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = Guru::find(Auth::user()->id);
        
        if(isset($data['nama'])){
            $user->nama = $data['nama'];
        }
        if(isset($data['nip'])){
            $user->nip = $data['nip'];
        }
        if(isset($data['email'])){
            $user->email = $data['email'];
        }
        if(isset($data['password'])){
            $user->password = Hash::make($data['password']);
        }
        if(isset($data['no_hp'])){
            $user->no_hp = $data['no_hp'];
        }

        $user->save();
        return (new GuruRequestResponse($user))->response()->setStatusCode(201);
    }
    public function updateGuru(UpdateGuruRequest $request, int $id_guru): JsonResponse
    {
        $data = $request->validated();
        $user = Guru::find($id_guru);
        
        if(isset($data['nama'])){
            $user->nama = $data['nama'];
        }
        if(isset($data['nip'])){
            $user->nip = $data['nip'];
        }
        if(isset($data['email'])){
            $user->email = $data['email'];
        }
        if(isset($data['password'])){
            $user->password = Hash::make($data['password']);
        }
        if(isset($data['no_hp'])){
            $user->no_hp = $data['no_hp'];
        }

        $user->save();
        return (new GuruRequestResponse($user))->response()->setStatusCode(201);
    }
    

    public function logout(Request $request): JsonResponse {
        $user = Guru::find(Auth::user()->id);
        $user->token = null;
        $user->save();
        return response()->json([
            "data"=> true,
        ])->setStatusCode(200);
    }


    public function getAllGuru() : AnonymousResourceCollection {
        $gurus = Guru::all();
        return GuruRequestResponse::collection($gurus);
    }

    public function delete(int $id): AnonymousResourceCollection {
        $guru = Guru::find($id);

        if(!$guru){
            throw new HttpResponseException(response([
                "errors"=>[
                    "message" => [
                        "Not found."
                    ]
                ]
                    ],404));
        }

        $guru->delete();
        $guruall = Guru::all();
        return GuruRequestResponse::collection($guruall);
        
    }
}
