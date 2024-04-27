<?php

namespace App\Http\Middleware;

use App\Models\Siswa;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthSiswaMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        //AMBIL TOKEN DARI HEADERS Authorization
        $token = $request->header('Authorization');
        $authenticate = true;
        if(!$token) {
            $authenticate = false;
        }
        //AMBIL DATA SISWA
         $siswa = Siswa::where('token', $token)->first();

         if(!$siswa) {
            $authenticate= false;
         } else {
            Auth::login($siswa); 
         }

        // JIKA SISWA ADA LANJUTKAN KE PROSES SELANJUTNYA
         if($authenticate){
             return $next($request);
         } else {
            return response()->json([
                "errors"=>[
                    "message"=>[
                        'Unauthorized.'
                    ]
                ]
            ])->setStatusCode(401);
         }
    }
}
