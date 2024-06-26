<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SiswaRequest extends FormRequest
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
            //
            "nama" => ['required', "max:100"],
            "nis" => ['required', "max:100"],
            "password"=> ["required", "max:100"],
            "jenis_kelamin" => ['required', "max:15"],
            "id_kelas" => ['required'],
            "kontak" => ['required', "max:15"],
            "kontak_orang_tua" => ['required', "max:15"],
            "alamat" => ['required', "max:100"],
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([  
            "errors" => $validator->getMessageBag()
        ], 400));
    }
}
