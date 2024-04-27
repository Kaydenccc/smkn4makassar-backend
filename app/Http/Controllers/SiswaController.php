<?php

namespace App\Http\Controllers;

use App\Http\Requests\SiswaRequest;
use App\Http\Requests\LoginSiswaRequest;
use App\Http\Requests\SiswaUpdateRequest;
use App\Http\Resources\SiswaResponse;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class SiswaController extends Controller
{
    //
    public function store(SiswaRequest $request): JsonResponse
    {
        // VALIDASI DATA
        $data = $request->validated();

        if(Siswa::where('nis', $data['nis'])->count()==1){
            throw new HttpResponseException(response([
                "errors"=>[
                    "message" => [
                        "NIS already exist."
                    ]
                ]
                    ],400));
        }

        $siswa = new Siswa($data);
        $siswa->save();

        return (new SiswaResponse($siswa))->response()->setStatusCode(201);

    }

    public function get(int $id) : SiswaResponse
    {
        $siswa = Siswa::with("kelas:id,kelas")->find($id);

        if(!$siswa) {
            throw new HttpResponseException(response([
                "errors"=>[
                    "message" => [
                        "Not found."
                    ]
                ]
                    ],404));
        }

        return new SiswaResponse($siswa);
    }

    public function login(LoginSiswaRequest $request) : SiswaResponse 
    {
        $data = $request->validated();
        $user = Siswa::where("nis", $data["nis"])->first();

        if(!$user || !Hash::check($data['password'], $user->password)) {
            throw new HttpResponseException(response([
                "errors"=>[
                    "message"=>[
                        "NISN atau password salah."
                    ]
                ]
            ], 401));
        }
        $user->token = Str::uuid()->toString();
        $user->save(); 
        return new SiswaResponse($user);
    }

    public function logout(Request $request): JsonResponse
    {
        $siswa = Siswa::find(Auth::user()->id);
        $siswa->token = null;
        $siswa->save();
        return response()->json([
            "data" => true
        ])->setStatusCode(200);
    }

    public function getLoginSiswa(Request $request): SiswaResponse
    {
        $siswa = Auth::user();
        return new SiswaResponse($siswa);
    }

    public function getKelasSiswa(int $id): JsonResponse
    {
        $siswa = Siswa::find($id);
        if(!$siswa){
            throw new HttpResponseException(response([
                'errors'=> [
                    "message"=> [
                        "Not found."
                    ]
                ]
            ], 404));
        }
        // return KelasReponse::collection();
        return response()->json([
            "data" => [
                "siswa" => $siswa,
                "kelas" => Kelas::all()
            ]
        ])->setStatusCode(200);
    } 

    public function updateSiswaLogin(SiswaRequest $request) : JsonResponse
    {
        $data = $request->validated();

        $siswa = Siswa::find(Auth::user()->id);
        if(isset($data['username'])){
            $siswa->username = $data['username'];
        }
        if(isset($data['password'])){
            $siswa->password = Hash::make($data['password']);
        }

        $siswa->save();
        return (new SiswaResponse($siswa))->response()->setStatusCode(201);

    }

    public function getAllSiswaByClass(Request $request, int $kelas_id): AnonymousResourceCollection  {
        $kelass = Siswa::with("kelas:id,kelas")->where('id_kelas', $kelas_id)->get();

        if($kelass->count() <= 0) {
            throw new HttpResponseException(response([
                "errors"=>[
                    "message" => [
                        "Not found."
                    ]
                ]
                    ],404));
        }
        return SiswaResponse::collection($kelass);
    }
    public function getAllSiswa(): AnonymousResourceCollection  {
        $siswa = Siswa::with("kelas:id,kelas")->orderBy('created_at', 'desc')->paginate(10);
        return SiswaResponse::collection($siswa);
    }

    public function update(SiswaUpdateRequest $request, int $id) : JsonResponse
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

    public function delete(int $id): AnonymousResourceCollection {
        $siswa = Siswa::find($id);

        if(!$siswa){
            throw new HttpResponseException(response([
                "errors"=>[
                    "message" => [
                        "Not found."
                    ]
                ]
                    ],404));
        }

        $siswa->delete();
        $siswaall = Siswa::with("kelas:id,kelas")->orderBy('created_at', 'desc')->paginate(10);
        return SiswaResponse::collection($siswaall);
        
    }

    public function search(Request $request): AnonymousResourceCollection {
        $page = $request->input("page", 1);
        $size = $request->input("size", 10);

        $siswa = Siswa::query();

        $siswa = $siswa->where(function (Builder $builder) use ($request) {
            $cari = $request->input('cari');
            if($cari){
                $builder->orWhere('nama','like', "%".$cari."%");
                $builder->orWhere('nis','like', "%".$cari."%");
            }
        });

        $siswa = $siswa->paginate(perPage:$size,page:$page);

        return SiswaResponse::collection($siswa);
    }

    

    // public function search(Request $request): AnonymousResourceCollection {
    //     $user = Auth::user();

    //     $page = $request->input("page", 1);
    //     $size = $request->input("size", 10);

    //     $siswa = Siswa::query()->where('id', $user->id);

    //     $siswa = $siswa->where(function (Builder $builder) use ($request) {
    //         $nama = $request->input('nama');
    //         if($nama){
    //                 $builder->orWhere('nama','like', "%".$nama."%");
    //         }

    //         $nis = $request->input('nis');
    //         if($nis){
    //                 $builder->where('nis','like', "%".$nis."%");
    //         }

    //         $kontak = $request->input('kontak');
    //         if($kontak){
    //                 $builder->where('kontak','like', "%".$kontak."%");
    //         }
    //     });

    //     $siswa = $siswa->paginate(perPage:$size,page:$page);

    //     return SiswaResponse::collection($siswa);
    // }
}
