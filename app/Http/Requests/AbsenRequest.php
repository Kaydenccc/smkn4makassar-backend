<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AbsenRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() != null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_guru'=>["required"],
            'id_siswa'=>["nullable"],
            "id_kelas"=>["required"], 
            "id_mapel"=>["required"], 
            'status'=> ["nullable"], 
            "jam"=> ["nullable"], 
            "tanggal"=> ["required"], 
            "keterangan"=> ["nullable", "max:100"],
            'materi'=> ["required", "max:100"]
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            "errors" => $validator->getMessageBag()
        ], 400));
    }
}
