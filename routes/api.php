<?php

use App\Http\Controllers\AbsenController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\MapelController;
use App\Http\Controllers\SiswaController;
use App\Http\Middleware\ApiAuthMiddleware;
use App\Http\Middleware\AuthAdminMiddleware;
use App\Http\Middleware\AuthSiswaMiddleware;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/admin/login', [AdminController::class, 'login']);
Route::post('/gurus/login', [GuruController::class, 'login']);
Route::post('/siswa/login', [SiswaController::class, 'login']);
Route::get('/test', function () {
    return response()->json(['message' => 'API Laravel berhasil diakses']);
});
Route::middleware(AuthAdminMiddleware::class)->group(function(){
    Route::post('/upload-excel', [ExcelController::class, 'uploadAndSave']);
    Route::post('/upload-excel/guru', [ExcelController::class, 'uploadAndSaveGuru']);

    Route::post('/gurus', [GuruController::class, 'store']);
    Route::get('/gurus', [GuruController::class, 'getAllGuru']);
    Route::get('/gurus/{id_guru}', [GuruController::class, 'getGuru'])->where('id_guru', '[0-9]+');
    Route::patch('/gurus/{id_guru}', [GuruController::class, 'updateGuru'])->where('id_guru', '[0-9]+');
    Route::delete('/gurus/{id_guru}', [GuruController::class, 'delete'])->where('id_guru', '[0-9]+');
    
    Route::post('/admin', [AdminController::class, 'store']);
    Route::get('/admin', [AdminController::class, 'getAllAdmin']);
    Route::get('/admin/current', [AdminController::class, 'get']);
    Route::get('/admin/{id}/get', [AdminController::class, 'getAdmin'])->where('id', '[0-9]+');
    Route::delete('/admin/{id}/delete', [AdminController::class, 'delete'])->where('id', '[0-9]+');
    Route::patch('/admin/{id}', [AdminController::class, 'update'])->where('id', '[0-9]+');
    Route::delete('/admin/logout', [AdminController::class, 'logout']);
    Route::get('/admin/siswa/all', [SiswaController::class, 'getAllSiswa']);

    
    Route::patch('/mapels/{id}', [MapelController::class, 'update'])->where('id', '[0-9]+');
    Route::delete('/mapels/{id}', [MapelController::class, 'delete'])->where('id', '[0-9]+');
    Route::get('/mapels/{id}', [MapelController::class, 'get'])->where('id', '[0-9]+');
    Route::get('/mapels', [MapelController::class, 'getAll']);
    Route::post('/mapels', [MapelController::class, 'store']);
    
    Route::post('/kelas', [KelasController::class, 'store']);
    Route::get('/kelas', [KelasController::class, 'getAll']);
    Route::get('/kelas/{id}', [KelasController::class, 'get'])->where('id', '[0-9]+');
    Route::get('/kelas/siswa/{id}', [KelasController::class, 'getKelasSiswa'])->where('id', '[0-9]+');
    Route::patch('/kelas/{id}', [KelasController::class, 'update'])->where('id', '[0-9]+');
    Route::delete('/kelas/{id}', [KelasController::class, 'delete'])->where('id', '[0-9]+');
    
    Route::post('/siswa', [SiswaController::class, 'store']);
    Route::get('/siswa/{id}', [SiswaController::class, 'get'])->where('id', '[0-9]+');
    Route::patch('/siswa/{id}/byadmin', [AdminController::class, 'updateSiswaByAdmin'])->where('id', '[0-9]+');
    Route::delete('/siswa/{id}', [SiswaController::class, 'delete'])->where('id', '[0-9]+');
    Route::get('/siswa/{kelas_id}/all', [SiswaController::class, 'getAllSiswaByClass'])->where('kelas_id', '[0-9]+');
    
    Route::get('/search/siswa/admin', [SiswaController::class, 'search']);
    Route::get('/search/siswa/perkelas', [SiswaController::class, 'searchSiswaPerkelas']);
});


Route::middleware(ApiAuthMiddleware::class)->group(function(){
    Route::patch('/gurus/current', [GuruController::class, 'update']);
    Route::get('/gurus/current', [GuruController::class, 'get']);
    Route::delete('/gurus/logout', [GuruController::class, 'logout']);
    Route::post('/absens', [AbsenController::class, 'create']);
    Route::get('/absens/{id}/{id_mapel}/{id_guru}/{id_kelas}/{tgl}', [AbsenController::class, 'setAbsen'])->where('id', '[0-9]+')->where('id_mapel', '[0-9]+')->where('id_guru', '[0-9]+')->where('id_kelas', '[0-9]+');
    Route::patch('/absens/{id}/{id_mapel}/{id_guru}/{id_kelas}/{tgl}', [AbsenController::class, 'setAbsenUpdate'])->where('id', '[0-9]+')->where('id_mapel', '[0-9]+')->where('id_guru', '[0-9]+')->where('id_kelas', '[0-9]+');
    Route::put('/absen/array', [AbsenController::class, 'setAbsenArray']);
    Route::delete('/absens/{id}/{id_guru}/{id_mapel}/{id_kelas}/{tanggal}', [AbsenController::class, 'delete'])->where('id', '[0-9]+');
    Route::delete('/absens/guru/{id}/{id_guru}/{id_mapel}/{id_kelas}/laporan', [AbsenController::class, 'deleteLaporan'])->where('id', '[0-9]+');
    Route::get('/absens/{id_mapel}/{id_guru}/{id_kelas}/{tgl}', [AbsenController::class, 'getAbsenByKelasByMapelByGuruAndTanggal'])->where('id_mapel', '[0-9]+')->where('id_guru', '[0-9]+')->where('id_kelas', '[0-9]+');
    Route::get('/absens/guru/{id_guru}', [AbsenController::class, 'getAllAbsenByGuru'])->where('id_guru', '[0-9]+');
    Route::get('/absens/guru/{id_guru}/laporan', [AbsenController::class, 'getAllAbsenByMapelByKelasByGuru'])->where('id_guru', '[0-9]+');
    Route::get('/absens', [AbsenController::class, 'getAll']);
    Route::get('/kelasmapel', [AbsenController::class, 'getAllKelasAndMapel']);
    Route::get('/absens/{id_mapel}/{id_guru}/{id_kelas}', [AbsenController::class, 'getRekapAbsen'])->where('id_mapel', '[0-9]+')->where('id_guru', '[0-9]+')->where('id_kelas', '[0-9]+');

     
    Route::get('/search/absen', [AbsenController::class, 'search']);
    Route::get('/search/absen/rekap', [AbsenController::class, 'searchRekap']);
    Route::get('/search/siswa', [AbsenController::class, 'searchSiswa']);
    Route::get('/guru/absens/siswa/{id_siswa}/{id_mapel}/{id_kelas}/statistik', [AbsenController::class, 'getStatistik'])->where('id_siswa', '[0-9]+');

});
Route::middleware(AuthSiswaMiddleware::class)->group(function(){
    Route::patch('/siswa/{id}', [SiswaController::class, 'update']);
    Route::get('/siswa/current', [SiswaController::class, 'getLoginSiswa']);
    Route::delete('/siswa/logout', [SiswaController::class, 'logout']);
    Route::get('/absens/siswa/{id_siswa}', [AbsenController::class, 'getAllAbsenBySiswa'])->where('id_siswa', '[0-9]+');
    Route::get('/search/absens/siswa', [AbsenController::class, 'searchAbsenBySiswa']);
    Route::get('/search/mapel/siswa', [AbsenController::class, 'searchMapelBySiswa']);
    Route::get('/absens/siswa/{id_siswa}/laporan', [AbsenController::class, 'getAllAbsenByMapelByKelasBySiswa'])->where('id_siswa', '[0-9]+');
    Route::get('/absens/siswa/{id_siswa}/{id_mapel}/{id_kelas}/statistik', [AbsenController::class, 'getStatistik'])->where('id_siswa', '[0-9]+');
    Route::get('/kelas/siswa/{id}/bysiswa', [SiswaController::class, 'getKelasSiswa'])->where('id', '[0-9]+');


});