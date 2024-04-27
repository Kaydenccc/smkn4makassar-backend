<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GuruRequestResponse extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=> $this->id,
            "nama"=>$this->nama,
            "nip"=> $this->nip,
            "email"=> $this->email,
            "no_hp"=> $this->no_hp,
            "token"=> $this->token
        ];
    }

}
