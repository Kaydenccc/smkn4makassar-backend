<?php

namespace App\Http\Controllers;

use App\Http\Requests\MapelRequest;
use App\Http\Resources\MapelResponse;
use App\Models\Mapel;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MapelController extends Controller
{
    
    public function store(MapelRequest $request): JsonResponse 
    {
        $data = $request->validated();

        if(Mapel::where("mapel", $data["mapel"])->count()==1)
        {
            throw new HttpResponseException(response([
                'errors'=> [
                    "message"=> [
                        "The name already exist"
                    ]
                ]
            ], 400));
        }

        $mapel = new Mapel($data);
        $mapel->save();

        return (new MapelResponse($mapel))->response()->setStatusCode(201);

    }
    public function get(int $id): MapelResponse 
    {
        $mapel = Mapel::find($id);
        if(!$mapel) {
            throw new HttpResponseException(response([
                "errors" => [
                    "message" => [
                        "Not found."
                    ]
                ]
                    ], 404));
        }
        return new MapelResponse($mapel);
    }

    public function getAll(Request $request): AnonymousResourceCollection 
    {
        return MapelResponse::collection(Mapel::all());
    }

    public function update(MapelRequest $request, int $id) : MapelResponse
    {

        $mapel = Mapel::where('id', $id)->first();

        if(!$mapel) {
            throw new HttpResponseException(response([
                'errors'=> [
                    "message"=> [
                        "Not found."
                    ]
                ]
            ], 404));
        }
        
        // validatasi data
        $data = $request->validated();
        $mapel->fill($data);
        $mapel->save();

        return new MapelResponse($mapel);
    }

    public function delete(int $id): AnonymousResourceCollection {

        $mapel = Mapel::find($id);

        if(!$mapel){
            throw new HttpResponseException(response([
                'errors'=> [
                    "message"=> [
                        "Not found."
                    ]
                ]
            ], 404));
        }

        $mapel->delete();

        return MapelResponse::collection(Mapel::all());
    }
}
