<?php

namespace App\Http\Controllers;

use App\Http\Requests\AbsenRequest;
use App\Http\Requests\AbsenUpdateRequest;
use App\Http\Resources\AbsenResponse;
use App\Http\Traits\WablasTrait;
use App\Models\Absen;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Siswa;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AbsenController extends Controller
{
    //
    public function create(AbsenRequest $request) : JsonResponse
    {
        
        $data = $request->validated();
        $siswas = Siswa::where('id_kelas', $data["id_kelas"])->get();
        $data["tanggal"] = Carbon::parse($data["tanggal"])->format('Y-m-d');

        if($siswas->count() == 0) {
            throw new HttpResponseException(response([
                "errors"=>[
                    "message" =>[
                        "Not found."
                    ]
                ]
                    ],404));
        }

        foreach($siswas as $item) {
            $siswaExist = Absen::where('id_siswa', $item['id'])
            ->where('id_guru', $data['id_guru'])
            ->where('id_kelas', $item['id_kelas'])
            ->where('id_mapel', $data['id_mapel'])
            ->where('tanggal', $data['tanggal'])->count() == 1;
            if(!$siswaExist) {
                $data["id_siswa"] = $item['id'];
                $absen = new Absen($data);
                $absen->save();
            } 
        }

        return response()->json([
            "data"=>[
                "Absen created successfully."
            ]
        ])->setStatusCode(201);
    }

    public function setAbsen(int $id, int $id_mapel,int $id_guru, int $id_kelas, string $tgl): JsonResponse
    {
        // Tetapkan zona waktu yang diinginkan
        $desiredTimeZone = new DateTimeZone('Asia/Makassar');

        // Dapatkan waktu saat ini dengan zona waktu yang diinginkan
        $currentTime = new DateTime('now', $desiredTimeZone);

        // Mendapatkan jam dan menit
        $time = $currentTime->format('H:i'); 


        //mendapatkan data absen
        $absen = Absen::with('guru:id,nama,nip,email,no_hp', 'siswa:id,nama,nis,jenis_kelamin,kontak_orang_tua', 'kelas:id,kelas', 'mapel:id,mapel')
        ->where("id_siswa",$id)
        ->where('id_kelas', $id_kelas)->where('id_guru', $id_guru)->where('id_mapel', $id_mapel)->where('tanggal',$tgl)->first();
        // dd($absen);

        if(!$absen){
            throw new HttpResponseException(response([
                "errors"=>[
                    "message" =>[
                        "Siswa not found."
                    ]
                ]
                    ],404)); 
        }
        if($absen['status'] == 'Hadir') {
            throw new HttpResponseException(response([
                "errors"=>[
                    "message" =>[
                        "Siswa already absen."
                    ]
                ]
                    ],400)); 
        }
        $kumpulan_data = [];
        $pesan = "*Status Kehadiran Siswa* 
*SMKN 4 MAKASSAR*\n
Nama : *".$absen->siswa->nama."*
NIS : *".$absen->siswa->nis."*
Kelas : *".$absen->kelas->kelas."*
Tanggal : *".$absen->tanggal."*
Jam : *".$time."*\n
Dinyatakan *hadir* dalam kelas.";  

        $data['phone'] = '082359036670';
        // $data['phone'] = $absen->siswa->kontak_orang_tua;
        $data['message'] = $pesan;
        $data['secret'] = false;
        $data['retry'] = false;
        $data['isGroup'] = false;
        array_push($kumpulan_data, $data);
        WablasTrait::sendText($kumpulan_data);

        $absen->status = "Hadir";
        $absen->jam = $time;
        $absen->save();

        return (new AbsenResponse($absen))->response()->setStatusCode(201);
    }
    public function setAbsenUpdate(AbsenUpdateRequest $request,int $id, int $id_mapel,int $id_guru, int $id_kelas, string $tgl): JsonResponse
    {
        $dataBaru = $request->validated();
        // // Tetapkan zona waktu yang diinginkan
        // $desiredTimeZone = new DateTimeZone('Asia/Makassar');

        // // Dapatkan waktu saat ini dengan zona waktu yang diinginkan
        // $currentTime = new DateTime('now', $desiredTimeZone);
        // error_log($dataBaru);
        // // Mendapatkan jam dan menit
        // $time = $currentTime->format('H:i'); 
        error_log(serialize($dataBaru));
        //mendapatkan data absen
        $absen = Absen::with('guru:id,nama,nip', 'siswa:id,nama,nis,jenis_kelamin,kontak_orang_tua', 'kelas:id,kelas', 'mapel:id,mapel')
        ->where("id_siswa",$id)
        ->where('id_kelas', $id_kelas)->where('id_guru', $id_guru)->where('id_mapel', $id_mapel)->where('tanggal',$tgl)->first();
        // dd($dataBaru);
        // dd($absen);

        if(!$absen){
            throw new HttpResponseException(response([
                "errors"=>[
                    "message" =>[
                        "Siswa not found."
                    ]
                ]
                    ],404)); 
        }
       
        $kumpulan_data = [];
        $pesan = "*Status Kehadiran Siswa* 
*SMKN 4 MAKASSAR*\n
Nama : *".$absen->siswa->nama."*
NIS : *".$absen->siswa->nis."*
Kelas : *".$absen->kelas->kelas."*
Tanggal : *".$absen->tanggal."*
Jam : *".$dataBaru["jam"]."*\n
Dinyatakan *".$dataBaru["status"]."* dalam kelas.";  

        $data['phone'] = '082359036670';
        // $data['phone'] = $absen->siswa->kontak_orang_tua;
        $data['message'] = $pesan;
        $data['secret'] = false;
        $data['retry'] = false;
        $data['isGroup'] = false;
        array_push($kumpulan_data, $data);
        WablasTrait::sendText($kumpulan_data);

        $absen->status = $dataBaru["status"];
        $absen->keterangan = $dataBaru["keterangan"];
        $absen->jam = $dataBaru["jam"];
        $absen->save();

        return (new AbsenResponse($absen))->response()->setStatusCode(201);
    }
    public function setAbsenArray(Request $request): JsonResponse
    {
        // validasi data
        $request->validate([
                "absens" => ['required']
        ]);


        $data = $request->input('absens');
        if(count($data) == 0) {
            throw new HttpResponseException(response([
                        "errors"=>[
                            "message" =>[
                                "Not found."
                            ]
                        ]
                            ],404)); 
        }

        foreach ($data as $absenData) {
            $id = $absenData['id'];
            $status = $absenData['status'];
            $jam=$absenData["jam"];
            $keterangan = $absenData['keterangan'];
            $kumpulan_data = [];

            // Lakukan update pada model Absen
            $absen = Absen::with('guru:id,nama,nip,email,no_hp', 'siswa:id,nama,nis,jenis_kelamin,kontak_orang_tua', 'kelas:id,kelas', 'mapel:id,mapel')->find($id);
            //buat templet pesan
            $pesan = "*Status Kehadiran Siswa* 
*SMKN 4 MAKASSAR*\n
Nama : *".$absen->siswa->nama."*
NIS : *".$absen->siswa->nis."*
Kelas : *".$absen->kelas->kelas."*
Tanggal : *".$absen->tanggal."*
Jam : *".$jam."*\n
Dinyatakan *".$status."*.";  
        
        // masukan data untuk dikirimkan ke wa
        // $data['phone'] = '082359036670';
        $data['phone'] = $absen->siswa->kontak_orang_tua;
        $data['message'] = $pesan;
        $data['secret'] = false;
        $data['retry'] = false;
        $data['isGroup'] = false;
        array_push($kumpulan_data, $data);
        if($status) {
            WablasTrait::sendText($kumpulan_data);
        }

        $absen->status = $status;
        $absen->jam = $jam;
        $absen->keterangan = $keterangan;
        $absen->save();
        }
        return response()->json($data) ;
    }
    public function getAbsenByKelasByMapelByGuruAndTanggal(int $id_mapel,int $id_guru, int $id_kelas, string $tgl)
    {
        // $tanggal = Carbon::createFromFormat('d/m/Y', $tgl)->format('Y-m-d');
        $absens = Absen::where('id_kelas', $id_kelas)->where('id_guru', $id_guru)->where('id_mapel', $id_mapel)->where('tanggal',$tgl)
        ->orderBy('tanggal', 'desc')
        ->with('guru:id,nama,nip,email,no_hp', 'siswa:id,nama,nis,jenis_kelamin', 'kelas:id,kelas', 'mapel:id,mapel')->get();
        if($absens->count() <= 0) {
            throw new HttpResponseException(response([
                "errors"=>[
                    "message" => [
                        "Not found."
                    ]
                ]
                    ],404));
        }
        return AbsenResponse::collection($absens);
    }

    public function delete( int $id, int $id_guru,int $id_mapel,int $id_kelas, string $tanggal) : AnonymousResourceCollection
    {
        $absen = Absen::find($id);

        if(!$absen) {
            throw new HttpResponseException(response([
                "errors"=>[
                    "message" => [
                        "Not found."
                    ]
                ]
                    ],404));
        }

        Absen::where('id_guru', $id_guru)
        ->where('id_mapel', $id_mapel)
        ->where('id_kelas', $id_kelas)
        ->where('tanggal', $tanggal)
        ->delete();

        $Newabsens = Absen::with('guru:id,nama,nip,email,no_hp', 'siswa:id,nama,nis,jenis_kelamin', 'kelas:id,kelas', 'mapel:id,mapel')
        ->select([
            DB::raw('(select count(*) from absens where id_guru = ' . $id_guru . ' and absens.id = absens.id) as total_absen'),
            'id_guru',
            'id_kelas',
            'id_mapel',
            'tanggal',
        ])
        ->groupBy("id_guru",'id_kelas', 'id_mapel', 'tanggal')
        ->orderBy('tanggal', 'desc')
        ->where('id_guru', $id_guru)->paginate(10);  

        return AbsenResponse::collection($Newabsens);
    }

    public function getAllAbsenByGuru(int $id_guru): AnonymousResourceCollection {

        $absens = Absen::with('guru:id,nama,nip,email,no_hp', 'siswa:id,nama,nis,jenis_kelamin', 'kelas:id,kelas', 'mapel:id,mapel')
            ->selectRaw('count(*) as total_absen, GROUP_CONCAT(id) as absen_ids')  // Gunakan fungsi agregat
            ->groupBy("id_guru",'id_kelas', 'id_mapel', 'tanggal')
            ->orderBy('tanggal', 'desc')
            ->where('id_guru', $id_guru)
            ->paginate(10);
    
        return AbsenResponse::collection($absens);
    }
    

    public function getAllAbsenByMapelByKelasByGuru(int $id_guru): AnonymousResourceCollection {

        $absens = Absen::with('guru:id,nama,nip,email,no_hp', 'kelas:id,kelas', 'mapel:id,mapel')
        ->selectRaw('id_guru, id_kelas, id_mapel, tanggal, count(*) as total_absen')
        ->groupBy("id_guru",'id_kelas', 'id_mapel')
        ->orderBy('tanggal', 'desc')
        ->where('id_guru', $id_guru)->paginate(15);  
        return AbsenResponse::collection($absens);
    }
    
    public function getAllAbsenByMapelByKelasBySiswa(int $id_siswa): JsonResponse {

        $absens = Absen::with('guru:id,nama,nip,email,no_hp','siswa:id,nama,nis,jenis_kelamin', 'kelas:id,kelas', 'mapel:id,mapel')
            ->selectRaw('id_siswa, id_kelas, id_mapel, tanggal, count(*) as total_absen')
            ->groupBy("id_siswa",'id_kelas', 'id_mapel')
            ->orderBy('tanggal', 'desc')
            ->where('id_siswa', $id_siswa)->get();
    
        return response()->json([
            "data"=>$absens->toArray()
        ])->setStatusCode(200);
    }
    
    
    public function getStatistik(int $id_siswa, int $id_mapel, int $id_kelas): JsonResponse {

            // Mengambil data absensi berdasarkan id_siswa, id_mapel, dan id_kelas
            $absens = Absen::with('siswa:id,nama,nis,jenis_kelamin', 'kelas:id,kelas', 'mapel:id,mapel')
            ->where('id_kelas', $id_kelas)
            ->where('id_mapel', $id_mapel)
            ->where('id_siswa', $id_siswa)
            ->get();

            // Menghitung jumlah kehadiran, sakit, izin, dan alpa
            $statistik = [
            'total' => $absens->count(),
            'hadir' => $absens->where('status', 'Hadir')->count(),
            'sakit' => $absens->where('status', 'Sakit')->count(),
            'izin' => $absens->where('status', 'Izin')->count(),
            'alpa' => $absens->where('status', 'Alpa')->count(),
            ];

            // Mengembalikan data statistik dalam format JSON
            return response()->json([
            "statistik" => $statistik
            ], 200);
    }
    public function getAllAbsenBySiswa(int $id_siswa): AnonymousResourceCollection {

        $absens = Absen::with('guru:id,nama,nip,email,no_hp', 'siswa:id,nama,nis,jenis_kelamin', 'kelas:id,kelas', 'mapel:id,mapel')
        ->select([
            DB::raw('(select count(*) from absens where id_siswa = ' . $id_siswa . ' and absens.id = absens.id) as total_absen'),
            'id_guru',
            'id_kelas',
            'id_mapel',
            'tanggal',
          ])
          ->groupBy("id_guru",'id_kelas', 'id_mapel', 'tanggal')
          ->orderBy('tanggal', 'desc')
          ->where('id_siswa', $id_siswa)
          ->paginate(10);  // Ubah ke count() jika hanya butuh jumlah total
      
        return AbsenResponse::collection($absens);
    }
    public function getAll(): AnonymousResourceCollection {
        $absens = Absen::with('guru:id,nama,nip,email,no_hp', 'siswa:id,nama,nis,jenis_kelamin', 'kelas:id,kelas', 'mapel:id,mapel')
        ->selectRaw('id_guru, id_kelas, id_mapel, tanggal, count(*) as total_absen')
        ->orderBy('tanggal', 'desc')
        ->groupBy("id_guru",'id_kelas', 'id_mapel', 'tanggal')
        ->get();  
        return AbsenResponse::collection($absens);
    }

    public function getAllKelasAndMapel(): JsonResponse {
        $kelas = Kelas::select('id', 'kelas')->get();
        $mapels = Mapel::select('id', 'mapel')->get();

        return response()->json([
            "data"=> [
                "kelas"=> $kelas,
                "mapel"=> $mapels
            ]
            ]);
    }

    public function getRekapAbsen(int $id_mapel,int $id_guru, int $id_kelas)
    {
        // $tanggal = Carbon::createFromFormat('d/m/Y', $tgl)->format('Y-m-d');

        $absens = Absen::where('id_kelas', $id_kelas)->where('id_guru', $id_guru)->where('id_mapel', $id_mapel)
        ->with('guru:id,nama,nip,email,no_hp', 'siswa:id,nama,nis,jenis_kelamin', 'kelas:id,kelas', 'mapel:id,mapel')
        ->orderBy('tanggal')
        // ->groupBy('tanggal')
        ->get();
        if($absens->count() <= 0) {
            throw new HttpResponseException(response([
                "errors"=>[
                    "message" => [
                        "Not found."
                    ]
                ]
                    ],404));
        }

        $formattedData = $this->formatData($absens);
        return response()->json([
            "data" => $formattedData
        ])->setStatusCode(200);
    }
    private function formatData($data)
    {
        $formattedDataSiswa = [];
        $formattedDataTanggal = [];
        $formattedDataMateri = [];
        $pengampuh='';
        $kelas='';
        $mapel='';
        foreach ($data as $item) {
            $indexData = 0;
            $formattedDataAbsen = array_pad([], 24, null);
            foreach ($data as $siswa) {
                if($item['siswa']['id']==$siswa['siswa']['id']) {
                    if(!in_array($siswa, $formattedDataAbsen)){
                        $formattedDataAbsen[$indexData] = [
                            "id_absen" => $siswa['id'],
                            "tanggal"=> $siswa['tanggal'],
                            "status" => $siswa['status']];
                            $indexData++;
                    }
                }
            }
             $absen = [
                'id_siswa' => $item['siswa']['id'],
                'nama_siswa' => $item['siswa']['nama'],
                'nis' => $item['siswa']['nis'],
                'jenis_kelamin' => $item['siswa']['jenis_kelamin'],
                'guru' => $item['guru']['nama'],
                'kelas' => $item['kelas']['kelas'],
                'mapel' => $item['mapel']['mapel'],
                'statusAbsen' => $formattedDataAbsen,
            ];
            if (!in_array($absen, $formattedDataSiswa)){
                $formattedDataSiswa[] =  $absen;     
            }
            if ($pengampuh != $item['guru']['nama']){
                $pengampuh =  $item['guru']['nama'];     
            }
            if ($kelas != $item['kelas']['kelas']){
                $kelas =  $item['kelas']['kelas'];     
            }
            if ($mapel != $item['mapel']['mapel']){
                $mapel =  $item['mapel']['mapel'];     
            }
            // $dateTime = new DateTime($item['tanggal']);
                // Format ulang tanggal sesuai kebutuhan ("d/m/Y")
            // $tanggal =$dateTime->format("d/m/Y");
            if (!in_array($item['tanggal'], $formattedDataTanggal)){
                $formattedDataTanggal[] = $item['tanggal'];      
            }
            if (!in_array($item['materi'], $formattedDataMateri)){
                $formattedDataMateri[] =  $item['materi'];
            }
        }
      
        // Format data tambahan (judul, kelas, mapel, dsb.)
        $formattedData = [
                'kelas' => $kelas,
                'mapel' => $mapel,
                'pengampuh' => $pengampuh,
                'tanggal_pertemuan' => $formattedDataTanggal,
                'siswa' => $formattedDataSiswa,
                'materi' => $formattedDataMateri,
        ];

        return $formattedData;
    }

    public function deleteLaporan( int $id, int $id_guru,int $id_mapel,int $id_kelas) : AnonymousResourceCollection
    {
        $absen = Absen::find($id);

        if(!$absen) {
            throw new HttpResponseException(response([
                "errors"=>[
                    "message" => [
                        "Not found."
                    ]
                ]
                    ],404));
        }

        Absen::where('id_guru', $id_guru)
        ->where('id_mapel', $id_mapel)
        ->where('id_kelas', $id_kelas)
        ->delete();

        $absens = Absen::with('guru:id,nama,nip,email,no_hp', 'kelas:id,kelas', 'mapel:id,mapel')
        ->selectRaw('id_guru, id_kelas, id_mapel, tanggal, count(*) as total_absen')
        ->groupBy("id_guru",'id_kelas', 'id_mapel')
        ->orderBy('tanggal', 'desc')
        ->where('id_guru', $id_guru)->paginate(15);  
        return AbsenResponse::collection($absens);
    }


    public function search(Request $request): AnonymousResourceCollection {
        $user = Auth::user();

        $page = $request->input("page", 1);
        $size = $request->input("size", 10);

        $absens = Absen::with('guru:id,nama,nip,email,no_hp', 'siswa:id,nama,nis,jenis_kelamin', 'kelas:id,kelas', 'mapel:id,mapel')
        ->selectRaw('id_guru, id_kelas, id_mapel, tanggal, count(*) as total_absen')
        ->groupBy("id_guru",'id_kelas', 'id_mapel', 'tanggal')
        ->orderBy('tanggal', 'desc')
        ->where('id_guru', $user->id);  

        $absens = $absens->where(function (Builder $builder) use ($request) {
            $cari = $request->input('cari');
            if($cari){
                // dd($builder);
                $builder->whereHas('mapel', function ($query) use ($cari) {
                    $query->where('mapel', 'like', '%' . $cari . '%');
                });
                $builder->orWhere('tanggal','like', "%".$cari."%");
            }
        });

        $absens = $absens->paginate(perPage:$size,page:$page);

        return AbsenResponse::collection($absens);
    }


}
