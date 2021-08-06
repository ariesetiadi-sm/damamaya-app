@extends('layout.main')

@section('content')
    <!-- Page Heading -->
    <h1 class="h3 text-gray-800">{{ isset($data['title']) ? $data['title'] : 'Title' }}</h1>
    <p class="mb-4">Kelengkapan Daftar Informasi Publik</p>

    @if (session('success'))
        <div class="alert alert-primary" role="alert">
            {{ session('success') }}
        </div>
    @endif

    {{-- Report Chart --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between">
            <h6 class="m-0 mt-2 font-weight-bold text-primary">
                Laporan Tahun Update Instansi
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive h-table">
                <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th class="w-nama-instansi">Nama Instansi / Perangkat Daerah</th>
                            @foreach ($data['years'] as $y)
                                <th>{{ $y }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Loop semua  data instansi --}}
                        @foreach ($data['instansi'] as $ins)
                            <tr>
                                <td>{{ $loop->index + 1 }}</td>
                                <td class="w-nama-instansi">{{ $ins->nama_pd }}</td>
                                @foreach ($data['years'] as $y)
                                    @php
                                        //Ambil tahun update instansi dari table layanan informasi
                                        $tahun_update = Illuminate\Support\Facades\DB::table('layanan_informasi')
                                            ->where('nama_pd', "$ins->nama_pd")
                                            ->max('tahun_update');
                                    @endphp

                                    {{-- Cek apakah ada data instansi di Layanan Informasi --}}
                                    @if (isset($tahun_update))

                                        {{-- Tampikan tahun update dari instansi tersebut --}}
                                        {{-- Jika sama dengan tahun berjalan, maka tampilkan warna biru --}}
                                        @if ($y == $tahun_update && $loop->last)
                                            <td>
                                                <span class="badge badge-primary d-block py-2">{{ $y }}</span>
                                            </td>
                                        @elseif ($y == $tahun_update)
                                            <td>
                                                <span class="badge badge-danger d-block py-2">{{ $y }}</span>
                                            </td>
                                        @else
                                            <td></td>
                                        @endif

                                    @else
                                        <td></td>
                                    @endif

                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <!-- Report Table  -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between">
            <div>
                <h6 class="m-0 mt-2 font-weight-bold text-primary">
                    Data Layanan Informasi
                </h6>
            </div>

            <div>
                {{-- <form action="#" method="POST" class="form-inline mr-auto w-100 navbar-search">
                    @csrf
                    <div class="input-group">
                        <input name="keyword" type="text" class="form-control form-control-sm bg-light small"
                            placeholder="Cari user..." aria-label="Search" aria-describedby="basic-addon2">
                        <div class="input-group-append">
                            <button class="btn btn-sm btn-primary" type="submit">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </form> --}}
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive h-table">
                <table class="table" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama Instansi / Perangkat Daerah</th>
                            <th>Tahun Update</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data['informasi'] as $info)
                            <tr>
                                <td>{{ $loop->index + 1 }}</td>
                                <td>{{ $info->nama_pd }}</td>
                                <td>{{ $info->tahun_update }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection