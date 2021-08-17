<?php

namespace App\Http\Controllers\Layanan;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

use App\Models\Layanan\PengaduanAnggaran;

class PengaduanAnggaranController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Kirim data yang dibutuhkan ke halaman Report Pengaduan Anggaran
        $data = [
            'title' => 'Pengaduan Anggaran',
            'kategori' => DB::table('kategori_pengaduan')->get(),
            'chart_period' => [
                'start' => Carbon::now()->subDay('6')->toDateString(),
                'end' => Carbon::now()->toDateString()
            ]
        ];

        return view('pengaduan_anggaran.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Kirim data yang dibutuhkan ke halaman Tambah Pengaduan PRO
        $data = [
            'title' => 'Tambah Pengaduan Anggaran',
            'kategori' => DB::table('kategori_pengaduan')->get(),
            'now' => Carbon::now()->toDateString()
        ];

        return view('pengaduan_anggaran.create', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validasi data dari form input
        $request->validate([
            'tanggal' => 'required',
            'nama_pelapor' => 'required|max:255',
            'topik' => 'required|max:255',
            'kategori' => 'required',
            'nama_pd' => 'required'
        ]);

        // Insert data pengaduan anggaran dengan Model
        PengaduanAnggaran::create([
            'tanggal' => $request->tanggal,
            'nama_pelapor' => $request->nama_pelapor,
            'topik' => $request->topik,
            'kategori' => $request->kategori,
            'nama_pd' => $request->nama_pd
        ]);

        return redirect()->route('pengaduan-anggaran.index')->with('success', 'Berhasil Menambah Pengaduan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function report(Request $request)
    {
        $report = [];

        // Ambil tanggal Start dan End untuk menentukan periode Chart
        $start = Carbon::createFromFormat('Y-m-d', $request->start_date);
        $end = Carbon::createFromFormat('Y-m-d', $request->end_date);
        $periods = CarbonPeriod::create($start, $end);

        foreach ($periods as $period) {
            // Jika kategori == null, ambil semua data
            if (is_null($request->kategori)) {
                // Data count untuk Chart
                $report['counts'][] = PengaduanAnggaran
                    ::whereDate('tanggal', $period->toDateString())
                    ->count();

                // Data untuk table
                $report['data'] = PengaduanAnggaran
                    ::whereDate('tanggal', '>=', $start)
                    ->whereDate('tanggal', '<=', $end)
                    ->orderBy('tanggal', 'DESC')
                    ->get();
            } else {
                // Data count untuk Chart berdasarkan Kategori
                $report['counts'][] = PengaduanAnggaran
                    ::whereDate('tanggal', $period->toDateString())
                    ->where('kategori', $request->kategori)
                    ->count();

                // Data untuk table berdasarkan Kategori
                $report['data'] = PengaduanAnggaran
                    ::whereDate('tanggal', '>=', $start)
                    ->whereDate('tanggal', '<=', $end)
                    ->where('kategori', $request->kategori)
                    ->orderBy('tanggal', 'DESC')
                    ->get();
            }


            // Ambil tanggal di looping saat ini
            $report['dates'][] = $period->isoFormat('dddd - D/M');
        }

        return response()->json($report);
    }
}
