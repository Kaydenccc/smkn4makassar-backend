<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ExcelService;
use App\Services\ExcelServiceGuru;

class ExcelController extends Controller
{
    public function uploadAndSave(Request $request)
    {
        // Validasi request
        $request->validate([
            'file' => 'required|mimes:xlsx|max:10240', // Sesuaikan dengan ekstensi file dan ukuran yang diinginkan
        ]);

        // Ambil file dari request
        $excelFile = $request->file('file');

        // Panggil service untuk proses upload dan penyimpanan data
        $excelService = new ExcelService();
        $data = $excelService->parseAndSaveExcel($excelFile);

        return response()->json(['success' => true, 'data' => $data], 200);
    }
    
    public function uploadAndSaveGuru(Request $request)
    {
        // Validasi request
        $request->validate([
            'file' => 'required|mimes:xlsx|max:10240', // Sesuaikan dengan ekstensi file dan ukuran yang diinginkan
        ]);

        // Ambil file dari request
        $excelFile = $request->file('file');

        // Panggil service untuk proses upload dan penyimpanan data
        $excelService = new ExcelServiceGuru();
        $data = $excelService->parseAndSaveExcel($excelFile);

        return response()->json(['success' => true, 'data' => $data], 200);
    }
}

//     Siswa::create([
//         "nama"=> $row["Nama"],
//         "nis"=> (int)$row["NIS"],
//         "jenis_kelamin"=> $row["Jenis Kelamin"],
//         "id_kelas"=> $row["Kelas"],
//         "kontak"=> $row["Kontak"],
//         "kontak_orang_tua"=> $row["Kontak Orang Tua"],
//         "alamat"=> $row["Alamat"]
//     ]);

