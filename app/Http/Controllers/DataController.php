<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
class DataController extends Controller
{

    //Untuk menampilkan halaman utama dan mengambil data dari file data.txt yang tersimpan pada disk local (Storage/App/data.txt)
    public function index()
    {
        $data = $this->getDataFromFile(); //Memanggil function getDataFromFile yang fungsinya untuk mengelola data file data.txt
        return view('data.data', compact('data'));
    }

    //Untuk melakukan proses pencarian berdasarkan nipt dan nama pt
    public function search(Request $request)
    {
        $keyword = strtolower($request->input('keyword')); //Keyword yang dimasukan akan diproses oleh sistem menjadi huruf kecil semua
        $data = $this->getDataFromFile(); //Memanggil function getDataFromFile yang fungsinya untuk mengelola data file data.txt

        $filteredData = collect($data)->filter(function ($item) use ($keyword) { //Melakukan filter data dari variabel $item (nipt dan pt) menggunakan keyword yang telah dimasukan
            return str_contains(strtolower($item['nipt']), $keyword) || str_contains(strtolower($item['pt']), $keyword); //Mengubah kedua item string menjadi huruf kecil agar dapat sesuai dengan keyword yang diinputkan
        })->values()->all();

        return view('data.data', compact('data', 'filteredData'));
    }

    //Untuk melakukan penyimpanan data kedalam data.txt
    public function store(Request $request)
    {
        //Validasi data inputan
        $request->validate([
            'nipt' => 'required|unique_nipt', //nipt harus diisi dan sifatnya unik, untuk perintah agar nipt unik ada pada folder Provider/AppServiceProvider
            'pt' => 'required|string|max:255', //pt harus diisi, tipe data string dan nilai maksimal nya adalah 255 kata/karakter
            'akreditasi' => 'nullable', //akreditasi dijadikan null terlebih dahulu agar seolah-olah melakukan proses pengelolaan terhadap 2 buah file .txt
            'peringkat' => 'nullable', //peringkat dijadikan null terlebih dahulu agar seolah-olah melakukan proses pengelolaan terhadap 2 buah file .txt
            'created_at' => 'nullable', //created_at dijadikan null terlebih dahulu agar seolah-olah melakukan proses pengelolaan terhadap 2 buah file .txt
        ]);

        $data = $this->getDataFromFile(); //Memanggil function getDataFromFile yang fungsinya untuk mengelola data file data.txt

        //Data diambil dari request pada saat input data (telah melewati validasi data sebelumnya)
        $data[] = [
            'nipt' => $request->input('nipt'),
            'pt' => $request->input('pt'),
            'akreditasi' => $request->input('akreditasi'),
            'peringkat' => $request->input('peringkat'),
            'created_at' => $request->input('created_at'),
        ];

        $this->saveDataToFile($data); //Menyimpan datas

        return redirect('/')->with('success', 'Data berhasil disimpan!');
    }

    //Untuk proses update data
    public function update(Request $request, $nipt)
    {
        $request->validate([
            'nipt' => 'sometimes', //nipt tidak harus dilakukan perubahan
            'pt' => 'sometimes|string|max:255', //pt tidak harus dilakukan perubahan
            'akreditasi' => 'sometimes|string', //akreditasi tidak harus dilakukan perubahan dan bertipe data string //akreditasi baru dimasukan pada proses update agar seolah olah telah mengelola 2 buah file .txt yang berbeda
        ]);

        $data = $this->getDataFromFile(); //Memanggil function getDataFromFile yang fungsinya untuk mengelola data file data.txt

        //Mencari data yang memiliki nipt yang sama ketika ingin melakukan proses perubahan data
        $foundIndex = collect($data)->search(function ($item) use ($nipt) {
            return $item['nipt'] === $nipt;
        });

        //Jika data berhasil ditemukan
        if ($foundIndex !== false) {
            //Untuk akreditasi yang ada dalam request diubah dari string menjadi tipe data float
            if ($request->has('akreditasi')) {
                $akreditasiString = str_replace(',', '.', $request->input('akreditasi')); //Mengganti pemisah koma menjadi titik
                $akreditasi = floatval($akreditasiString);
                $peringkat = $this->getperingkatFromakreditasi($akreditasi);

                //Update data dalam array dengan akreditasi float yang baru
                $data[$foundIndex]['akreditasi'] = $akreditasi;
                $data[$foundIndex]['peringkat'] = $peringkat;
            }

            //Jika pt ada dalam request, update data dalam array dengan pt yang baru
            if ($request->has('pt')) {
                $data[$foundIndex]['pt'] = $request->input('pt');
            }

             //Jika created_at masih bernilai null, tambahkan nilai created_at dengan tanggal dan waktu saat ini
             //Tetapi jika tidak null, tidak akan merubah nilai created_at yang sudah ada
            if ($data[$foundIndex]['created_at'] === null) {
                $data[$foundIndex]['created_at'] = Carbon::now()->format('d-M-Y H:i');
            }
            $this->saveDataToFile($data); //Memanggil function getDataFromFile yang fungsinya untuk mengelola data file data.txt

            return redirect('/')->with('success', 'Data berhasil diubah.');
        } else {
            return redirect('/')->with('error', 'Data tidak ditemukan.');
        }
    }

    //Untuk menghapus array data
    public function destroy($nipt)
    {
        $data = $this->getDataFromFile(); //Memanggil function getDataFromFile yang fungsinya untuk mengelola data file data.txt
        //Mencari data yang memiliki nipt yang sama ketika ingin melakukan proses hapus data
        $foundIndex = collect($data)->search(function ($item) use ($nipt) {
            return $item['nipt'] === $nipt;
        });

        //Jika ditemukan, maka menghapus data dalam array yang memiliki nipt yang dimaksud dan menyimpan perubahan data pada file data.txt
        if ($foundIndex !== false) {
            unset($data[$foundIndex]);

            $this->saveDataToFile($data);

            return redirect('/')->with('success', 'Data berhasil dihapus.');
        } else {
            return redirect('/')->with('error', 'Data tidak ditemukan.');
        }
    }

    //Function getDataFromFile yang fungsinya untuk mengelola data file data.txt
    private function getDataFromFile()
    {
        if (Storage::disk('local')->exists('data.txt')) { //Cek apakah data.txt sudah tersedia
            $fileContent = Storage::disk('local')->get('data.txt'); //Jika sudah ambil datanya
            $data = json_decode($fileContent, true);

            // Menggunakan sortByDesc() untuk mengurutkan data berdasarkan akreditasi secara descending
            $sortedData = collect($data)->sortByDesc('akreditasi')->values()->all();

            return $sortedData;
        }

        return []; //Jika belum ada, akan menampilkan pesan yang telah diatur pada halaman view
    }

    //Function untuk menyimpan data kedalam file data.txt
    private function saveDataToFile($data)
    {
        Storage::disk('local')->put('data.txt', json_encode($data)); //Membuat data.txt dan menyimpan data ke dalamnya 
        Storage::disk('local')->put('public/data.txt', json_encode($data)); //Membuat data.txt dan menyimpan data ke dalamnya, namun pada folder Storage/App/Public, agar nantinya dapat dilakukan Storage:Link untuk menampilkan chart
    }

    //Function untuk menentukan nilai dari peringkat berdasarkan nilai akreditasi
    private function getperingkatFromakreditasi($akreditasi)
    {
        if ($akreditasi >= 361 && $akreditasi <= 400) {
            return 'A';
        } elseif ($akreditasi >= 301 && $akreditasi <= 360) {
            return 'B';
        } elseif ($akreditasi >= 200 && $akreditasi <= 300) {
            return 'C';
        } elseif ($akreditasi < 200) {
            return 'Tidak Terakreditasi';
        } 

        return null;
    }
}
