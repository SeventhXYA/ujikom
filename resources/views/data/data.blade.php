@extends('layout.master')

@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net/dataTables.bootstrap4.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet" />
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Welcome to Dashboard</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-end">
                        <form action="{{ route('search.data') }}" method="POST">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-lg-7">
                                    <input class="form-control" maxlength="255" name="keyword" id="keyword" type="text"
                                        autocomplete="off" placeholder="nis atau nama">
                                </div>
                                <button type="submit" class="btn btn-primary btn-icon-text" style="width: 6rem"><i
                                        data-feather="search" class="icon-sm"></i></button>
                            </div>
                        </form>
                    </div>
                    <div>
                        <?php
                        try {
                            $fileContent = \Illuminate\Support\Facades\Storage::disk('local')->get('data.txt');
                            $data = json_decode($fileContent, true);
                        } catch (\Illuminate\Contracts\Filesystem\FileNotFoundException $e) {
                            $data = null;
                        }
                        ?>
                        @if (isset($filteredData))
                            @if (count($filteredData) > 0)
                                <p>Hasil Pencarian:</p>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th style="width:1rem;">Nomor Induk</th>
                                            <th>Nama</th>
                                            <th style="width:1rem;">Nilai Angka</th>
                                            <th style="width:1rem;">Nilai Huruf</th>
                                            <th style="width:1rem;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($filteredData as $item)
                                            <tr>
                                                <td>{{ $item['nis'] }}</td>
                                                <td>{{ $item['nama'] }}</td>
                                                @if ($item['nilai'])
                                                    <td class="text-center">{{ $item['nilai'] }}</td>
                                                    <td class="text-center">{{ $item['huruf'] }}</td>
                                                @else
                                                    <td class="text-center">
                                                        <button type="button"
                                                            class="btn btn-outline-primary btn-icon btn-xs"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#addNilai-{{ $item['nis'] }}"><i
                                                                data-feather="plus" class="icon-sm"></i>
                                                        </button>
                                                    </td>
                                                    <div class="modal fade" id="addNilai-{{ $item['nis'] }}" tabindex="-1"
                                                        aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-scrollable">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title"
                                                                        id="exampleModalScrollableTitle">
                                                                    </h5>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal"
                                                                        aria-label="btn-close"></button>
                                                                </div>
                                                                <form
                                                                    action="{{ route('data.update', ['nis' => $item['nis']]) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    <div class="modal-body">
                                                                        <span class="badge bg-primary card-title">
                                                                            <h6>Tambah Nilai</h6>
                                                                        </span>
                                                                        <div class="col mb-3">
                                                                            <label class="form-label fw-bold">NIS</label>
                                                                            <input type="text" class="form-control"
                                                                                name="nis" value="{{ $item['nis'] }}"
                                                                                autocomplete="off" required readonly
                                                                                style="background-color: #f5f5f5" />
                                                                        </div>
                                                                        <div class="col mb-3">
                                                                            <label class="form-label fw-bold">Nama</label>
                                                                            <input type="text" class="form-control"
                                                                                name="nama" value="{{ $item['nama'] }}"
                                                                                autocomplete="off" required readonly
                                                                                style="background-color: #f5f5f5" />
                                                                        </div>
                                                                        <div class="col mb-3">
                                                                            <label class="form-label fw-bold">Nilai
                                                                                Akhir</label>
                                                                            <input type="text" class="form-control"
                                                                                name="nilai" autocomplete="off"
                                                                                required />
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <div class="my-2 d-flex justify-content-end">
                                                                            <button type="button"
                                                                                class="btn btn-secondary me-2"
                                                                                data-bs-dismiss="modal">Close</button>
                                                                            <button type="submit" class="btn btn-primary"
                                                                                style="width: 6rem">Tambah</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <td class="text-center">Tidak Ada Data</td>
                                                @endif
                                                <td class="d-flex inline">
                                                    <button type="button" class="btn btn-outline-warning btn-icon btn-xs"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editData-{{ $item['nis'] }}"><i
                                                            data-feather="edit" class="icon-sm"></i>
                                                    </button>
                                                    <div class="modal fade" id="editData-{{ $item['nis'] }}"
                                                        tabindex="-1" aria-labelledby="exampleModalScrollableTitle"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-scrollable">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title"
                                                                        id="exampleModalScrollableTitle">
                                                                    </h5>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal"
                                                                        aria-label="btn-close"></button>
                                                                </div>
                                                                <form
                                                                    action="{{ route('data.update', ['nis' => $item['nis']]) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    <div class="modal-body">
                                                                        <span class="badge bg-primary card-title">
                                                                            <h6>Ubah Data</h6>
                                                                        </span>
                                                                        <div class="col mb-3">
                                                                            <label class="form-label fw-bold">NIS</label>
                                                                            <input type="text" class="form-control"
                                                                                name="nis"
                                                                                value="{{ $item['nis'] }}"
                                                                                autocomplete="off" required />
                                                                        </div>
                                                                        <div class="col mb-3">
                                                                            <label class="form-label fw-bold">Nama</label>
                                                                            <input type="text" class="form-control"
                                                                                name="nama"
                                                                                value="{{ $item['nama'] }}"
                                                                                autocomplete="off" required />
                                                                        </div>
                                                                        @if ($item['nilai'])
                                                                            <div class="col mb-3">
                                                                                <label class="form-label fw-bold">Nilai
                                                                                    Akhir</label>
                                                                                <input type="text" class="form-control"
                                                                                    name="nilai"
                                                                                    value="{{ $item['nilai'] }}"
                                                                                    autocomplete="off" required />
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <div class="my-2 d-flex justify-content-end">
                                                                            <button type="button"
                                                                                class="btn btn-secondary me-2"
                                                                                data-bs-dismiss="modal">Close</button>
                                                                            <button type="submit" class="btn btn-primary"
                                                                                style="width: 6rem">Simpan</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <form name="delete"
                                                        action="{{ route('data.delete', ['nis' => $item['nis']]) }}"
                                                        method="POST">
                                                        @method('delete')
                                                        @csrf
                                                        <button type="submit"
                                                            class="btn btn-outline-danger btn-xs btn-icon ms-2"><i
                                                                data-feather="trash" class="icon-sm"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p>Tidak ditemukan data dengan kata kunci pencarian yang dimasukkan.</p>
                            @endif
                        @else
                            @if ($data)
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th style="width:1rem;">Nomor Induk</th>
                                            <th>Nama</th>
                                            <th style="width:1rem;">Nilai Angka</th>
                                            <th style="width:1rem;">Nilai Huruf</th>
                                            <th style="width:1rem;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($data)
                                            @foreach ($data as $item)
                                                <tr>
                                                    <td>{{ $item['nis'] }}</td>
                                                    <td>{{ $item['nama'] }}</td>
                                                    @if ($item['nilai'])
                                                        <td class="text-center">{{ $item['nilai'] }}</td>
                                                        <td class="text-center">{{ $item['huruf'] }}</td>
                                                    @else
                                                        <td class="text-center">
                                                            <button type="button"
                                                                class="btn btn-outline-primary btn-icon btn-xs"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#addNilai-{{ $item['nis'] }}"><i
                                                                    data-feather="plus" class="icon-sm"></i>
                                                            </button>
                                                        </td>
                                                        <div class="modal fade" id="addNilai-{{ $item['nis'] }}"
                                                            tabindex="-1" aria-labelledby="exampleModalScrollableTitle"
                                                            aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-scrollable">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title"
                                                                            id="exampleModalScrollableTitle">
                                                                        </h5>
                                                                        <button type="button" class="btn-close"
                                                                            data-bs-dismiss="modal"
                                                                            aria-label="btn-close"></button>
                                                                    </div>
                                                                    <form
                                                                        action="{{ route('data.update', ['nis' => $item['nis']]) }}"
                                                                        method="POST">
                                                                        @csrf
                                                                        <div class="modal-body">
                                                                            <span class="badge bg-primary card-title">
                                                                                <h6>Tambah Nilai</h6>
                                                                            </span>
                                                                            <div class="col mb-3">
                                                                                <label
                                                                                    class="form-label fw-bold">NIS</label>
                                                                                <input type="text" class="form-control"
                                                                                    name="nis"
                                                                                    value="{{ $item['nis'] }}"
                                                                                    autocomplete="off" required readonly
                                                                                    style="background-color: #f5f5f5" />
                                                                            </div>
                                                                            <div class="col mb-3">
                                                                                <label
                                                                                    class="form-label fw-bold">Nama</label>
                                                                                <input type="text" class="form-control"
                                                                                    name="nama"
                                                                                    value="{{ $item['nama'] }}"
                                                                                    autocomplete="off" required readonly
                                                                                    style="background-color: #f5f5f5" />
                                                                            </div>
                                                                            <div class="col mb-3">
                                                                                <label class="form-label fw-bold">Nilai
                                                                                    Akhir</label>
                                                                                <input type="text" class="form-control"
                                                                                    name="nilai" autocomplete="off"
                                                                                    required />
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <div class="my-2 d-flex justify-content-end">
                                                                                <button type="button"
                                                                                    class="btn btn-secondary me-2"
                                                                                    data-bs-dismiss="modal">Close</button>
                                                                                <button type="submit"
                                                                                    class="btn btn-primary"
                                                                                    style="width: 6rem">Tambah</button>
                                                                            </div>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <td class="text-center">Tidak Ada Data</td>
                                                    @endif
                                                    <td class="d-flex inline">
                                                        <button type="button"
                                                            class="btn btn-outline-warning btn-icon btn-xs"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editData-{{ $item['nis'] }}"><i
                                                                data-feather="edit" class="icon-sm"></i>
                                                        </button>
                                                        <div class="modal fade" id="editData-{{ $item['nis'] }}"
                                                            tabindex="-1" aria-labelledby="exampleModalScrollableTitle"
                                                            aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-scrollable">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title"
                                                                            id="exampleModalScrollableTitle">
                                                                        </h5>
                                                                        <button type="button" class="btn-close"
                                                                            data-bs-dismiss="modal"
                                                                            aria-label="btn-close"></button>
                                                                    </div>
                                                                    <form
                                                                        action="{{ route('data.update', ['nis' => $item['nis']]) }}"
                                                                        method="POST">
                                                                        @csrf
                                                                        <div class="modal-body">
                                                                            <span class="badge bg-primary card-title">
                                                                                <h6>Ubah Data</h6>
                                                                            </span>
                                                                            <div class="col mb-3">
                                                                                <label
                                                                                    class="form-label fw-bold">NIS</label>
                                                                                <input type="text" class="form-control"
                                                                                    name="nis"
                                                                                    value="{{ $item['nis'] }}"
                                                                                    autocomplete="off" required />
                                                                            </div>
                                                                            <div class="col mb-3">
                                                                                <label
                                                                                    class="form-label fw-bold">Nama</label>
                                                                                <input type="text" class="form-control"
                                                                                    name="nama"
                                                                                    value="{{ $item['nama'] }}"
                                                                                    autocomplete="off" required />
                                                                            </div>
                                                                            @if ($item['nilai'])
                                                                                <div class="col mb-3">
                                                                                    <label class="form-label fw-bold">Nilai
                                                                                        Akhir</label>
                                                                                    <input type="text"
                                                                                        class="form-control"
                                                                                        name="nilai"
                                                                                        value="{{ $item['nilai'] }}"
                                                                                        autocomplete="off" required />
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <div class="my-2 d-flex justify-content-end">
                                                                                <button type="button"
                                                                                    class="btn btn-secondary me-2"
                                                                                    data-bs-dismiss="modal">Close</button>
                                                                                <button type="submit"
                                                                                    class="btn btn-primary"
                                                                                    style="width: 6rem">Simpan</button>
                                                                            </div>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <form name="delete"
                                                            action="{{ route('data.delete', ['nis' => $item['nis']]) }}"
                                                            method="POST">
                                                            @method('delete')
                                                            @csrf
                                                            <button type="submit"
                                                                class="btn btn-outline-danger btn-xs btn-icon ms-2"><i
                                                                    data-feather="trash" class="icon-sm"></i></button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="4">Tidak ada data</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            @else
                                <p>Tidak ada data</p>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <span class="badge bg-primary card-title">
                        <h6>Tambah Siswa</h6>
                    </span>
                    <form action="{{ route('store.data') }}" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-lg-3">
                                <label for="defaultconfig" class="col-form-label">NIS</label>
                            </div>
                            <div class="col-lg-9">
                                <input class="form-control" maxlength="255" name="nis" id="nis"
                                    type="text" autocomplete="off" value="{{ old('nis') }}">
                                @error('nis')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-3">
                                <label for="defaultconfig" class="col-form-label">Nama</label>
                            </div>
                            <div class="col-lg-9">
                                <input class="form-control" maxlength="255" name="nama" id="nama"
                                    type="text" autocomplete="off">
                            </div>
                        </div>
                        <div class="my-2 d-flex justify-content-end">
                            <input class="form-control" maxlength="255" name="nilai" id="nilai" value=""
                                type="hidden" autocomplete="off">
                            <input class="form-control" maxlength="255" name="huruf" id="huruf" value=""
                                type="hidden" autocomplete="off">
                            <button type="submit" class="btn btn-primary" style="width: 6rem">Tambah</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-baseline">
                        <h6 class="card-title mb-0">Nilai Siswa</h6>
                    </div>
                    <div class="row mt-2">
                        <canvas id="chartjsBar"></canvas>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('plugin-scripts')
    <script src="{{ asset('assets/plugins/chartjs/chart.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery.flot/jquery.flot.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery.flot/jquery.flot.resize.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/apexcharts/apexcharts.min.js') }}"></script>
    {{-- <script src="{{ asset('assets/plugins/progressbar-js/progressbar.min.js') }}"></script> --}}
    <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-net-bs4/dataTables.bootstrap4.js') }}"></script>
@endpush

@push('custom-scripts')
    <script src="{{ asset('assets/js/dashboard.js') }}"></script>
    <script src="{{ asset('assets/js/data-table.js') }}"></script>
    <script src="{{ asset('assets/js/sweet-alert.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4"></script>
    {{-- <script src="{{ asset('assets/js/chartjs.js') }}"></script> --}}
    <script src="{{ asset('assets/js/datepicker.js') }}"></script>
    <script>
        @if (Session::has('success'))
            window.onload = () => showSwal('mixin', '{{ Session::get('success') }}')
        @endif

        document.querySelectorAll('form[name="delete"]').forEach(form => {
            form.addEventListener('submit', (e) => {
                e.preventDefault()
                Swal.fire({
                    title: 'Apa anda yakin?',
                    text: "Setelah data dihapus, data tidak bisa dikembalikan",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-primary me-3',
                        cancelButton: 'btn btn-label-secondary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit()
                    }
                });
            });
        });
    </script>
    <script>
        var colors = {
            primary: "#6571ff",
            secondary: "#7987a1",
            success: "#05a34a",
            info: "#66d1d1",
            warning: "#fbbc06",
            danger: "#ff3366",
            light: "#e9ecef",
            dark: "#060c17",
            muted: "#7987a1",
            gridBorder: "rgba(77, 138, 240, .15)",
            bodyColor: "#000",
            cardBg: "#fff",
        };

        var fontFamily = "'Roboto', Helvetica, sans-serif";
        var ctx = document.getElementById('chartjsBar').getContext('2d');

        // Fetch data from data.txt
        fetch('/storage/data.txt') // Assuming data.txt is in the storage/app/public directory
            .then(response => response.json())
            .then(data => {
                var chartLabels = [
                    "A",
                    "B+",
                    "B",
                    "C+",
                    "C",
                    "D+",
                    "D",
                    "E",
                ];

                var siswaPerLabel = new Array(chartLabels.length).fill(0);

                // Process the fetched data
                data.forEach(item => {
                    var nilai = parseFloat(item.nilai);
                    if (80 <= nilai && nilai <= 100) {
                        siswaPerLabel[0]++;
                    } else if (75 <= nilai && nilai <= 80) {
                        siswaPerLabel[1]++;
                    } else if (70 <= nilai && nilai <= 75) {
                        siswaPerLabel[2]++;
                    } else if (65 <= nilai && nilai <= 70) {
                        siswaPerLabel[3]++;
                    } else if (60 <= nilai && nilai <= 65) {
                        siswaPerLabel[4]++;
                    } else if (55 <= nilai && nilai <= 60) {
                        siswaPerLabel[5]++;
                    } else if (50 <= nilai && nilai <= 55) {
                        siswaPerLabel[6]++;
                    } else if (0 <= nilai && nilai <= 50) {
                        siswaPerLabel[7]++;
                    }
                });

                var chart = new Chart(ctx, {
                    type: "bar",
                    data: {
                        labels: chartLabels,
                        datasets: [{
                            label: "Total",
                            backgroundColor: [
                                colors.primary,
                                colors.secondary,
                                colors.success,
                                colors.info,
                                colors.warning,
                                colors.danger,
                                colors.light,
                                colors.dark,
                            ],
                            data: siswaPerLabel,
                        }],
                    },
                    options: {
                        aspectRatio: 2,
                        plugins: {
                            legend: {
                                display: false
                            },
                        },
                        scales: {
                            x: {
                                display: true,
                                grid: {
                                    display: true,
                                    color: colors.gridBorder,
                                    borderColor: colors.gridBorder,
                                },
                                ticks: {
                                    color: colors.bodyColor,
                                    font: {
                                        size: 12,
                                    },
                                },
                            },
                            y: {
                                grid: {
                                    display: true,
                                    color: colors.gridBorder,
                                    borderColor: colors.gridBorder,
                                },
                                ticks: {
                                    color: colors.bodyColor,
                                    font: {
                                        size: 12,
                                    },
                                },
                            },
                        },
                    },
                });
            })
            .catch(error => {
                console.error('Error fetching data:', error);
            });
    </script>
@endpush
