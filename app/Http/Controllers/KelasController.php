<?php

namespace App\Http\Controllers;

use App\Http\Requests\KelasRequest;
use App\Http\Resources\KelasReponse;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class KelasController extends Controller
{
    //
    public function store(KelasRequest $request): JsonResponse
    {
        $data = $request->validated();
        if(Kelas::where("kelas", $data["kelas"])->count()==1) {
            throw new HttpResponseException(response([
                'errors'=> [
                    "message"=> [
                        "Kelas already exist"
                    ]
                ]
            ], 400));
        }

        $kelas = new Kelas($data);
        $kelas->save();

        return (new KelasReponse($kelas))->response()->setStatusCode(201);
    }

    public function get(int $id) : KelasReponse
    {
        $kelas = Kelas::find($id);
        if(!$kelas) {
            throw new HttpResponseException(response([
                'errors'=> [
                    "message"=> [
                        "Not found."
                    ]
                ]
            ], 404));
        }
        return new KelasReponse($kelas);
    }

    public function getAll(Request $request): AnonymousResourceCollection
    {
        return KelasReponse::collection(Kelas::all());
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

    public function update(KelasRequest $request, int $id): JsonResponse
    {
        //FIND THE KELAS DATA FROM DATABASE
        $kelas = Kelas::find($id);

        if(!$kelas){
            throw new HttpResponseException(response([
                'errors'=> [
                    "message"=> [
                        "Not found."
                    ]
                ]
            ], 404));
        }

        //VALIDATED DATA
        $data = $request->validated();

        $kelas->fill($data);
        $kelas->save();

        return (new KelasReponse($kelas))->response()->setStatusCode(201);
    }

    public function delete(Request $request, int $id): AnonymousResourceCollection
    {
        $kelas = Kelas::find($id);
        if(!$kelas){
            throw new HttpResponseException(response([
                "errors" => [
                    "message" => [
                        "Not found."
                    ]
                ]
                    ],404));
        }
        $kelas->delete();
        return KelasReponse::collection(Kelas::all());
    }

}
