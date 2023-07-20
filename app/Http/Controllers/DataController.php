<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DataController extends Controller
{

    public function index()
    {
        $data = $this->getDataFromFile();
        return view('data.data', compact('data'));
    }

    public function search(Request $request)
    {
        $keyword = strtolower($request->input('keyword')); // Ubah keyword menjadi huruf kecil
        $data = $this->getDataFromFile();

        $filteredData = collect($data)->filter(function ($item) use ($keyword) {
            // Ubah kedua string menjadi huruf kecil sebelum dilakukan pencarian
            return str_contains(strtolower($item['nis']), $keyword) || str_contains(strtolower($item['nama']), $keyword);
        })->values()->all();

        return view('data.data', compact('data', 'filteredData'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'nis' => 'required|unique_nis',
            'nama' => 'required|string|max:255',
            'nilai' => 'nullable',
            'huruf' => 'nullable',
        ]);

        $data = $this->getDataFromFile();

        $data[] = [
            'nis' => $request->input('nis'),
            'nama' => $request->input('nama'),
            'nilai' => $request->input('nilai'),
            'huruf' => $request->input('huruf'),
        ];

        $this->saveDataToFile($data);

        return redirect('/')->with('success', 'Data berhasil disimpan di file txt.');
    }

    public function update(Request $request, $nis)
    {
        $request->validate([
            'nis' => 'sometimes',
            'nama' => 'sometimes|string|max:255',
            'nilai' => 'sometimes|string', // Validasi bahwa nilai harus berupa string
        ]);

        $data = $this->getDataFromFile();
        $foundIndex = collect($data)->search(function ($item) use ($nis) {
            return $item['nis'] === $nis;
        });

        if ($foundIndex !== false) {
            // Jika 'nilai' ada dalam request, ubah penanda desimal sebelum mengubahnya menjadi tipe data float
            if ($request->has('nilai')) {
                $nilaiString = str_replace(',', '.', $request->input('nilai')); // Mengganti koma dengan titik
                $nilai = floatval($nilaiString);
                $huruf = $this->getHurufFromNilai($nilai);

                // Update data dalam array dengan nilai float yang baru
                $data[$foundIndex]['nilai'] = $nilai;
                $data[$foundIndex]['huruf'] = $huruf;
            }

            // Jika 'nis' ada dalam request dan berbeda dengan 'nis' sebelumnya, lakukan validasi unique_nis
            if ($request->has('nis') && $request->input('nis') !== $data[$foundIndex]['nis']) {
                $request->validate([
                    'nis' => 'unique_nis',
                ]);

                // Update data dalam array dengan 'nis' yang baru
                $data[$foundIndex]['nis'] = $request->input('nis');
            }

            // Jika 'nama' ada dalam request, update data dalam array dengan 'nama' yang baru
            if ($request->has('nama')) {
                $data[$foundIndex]['nama'] = $request->input('nama');
            }

            $this->saveDataToFile($data);

            return redirect('/')->with('success', 'Data berhasil diubah.');
        } else {
            return redirect('/')->with('error', 'Data tidak ditemukan.');
        }
    }



    public function destroy($nis)
    {
        $data = $this->getDataFromFile();
        $foundIndex = collect($data)->search(function ($item) use ($nis) {
            return $item['nis'] === $nis;
        });

        if ($foundIndex !== false) {
            unset($data[$foundIndex]);

            $this->saveDataToFile($data);

            return redirect('/')->with('success', 'Data berhasil dihapus.');
        } else {
            return redirect('/')->with('error', 'Data tidak ditemukan.');
        }
    }

    private function getDataFromFile()
    {
        if (Storage::disk('local')->exists('data.txt')) {
            $fileContent = Storage::disk('local')->get('data.txt');
            $data = json_decode($fileContent, true);

            // Menggunakan sortByDesc() untuk mengurutkan data berdasarkan nilai secara descending
            $sortedData = collect($data)->sortByDesc('nilai')->values()->all();

            return $sortedData;
        }

        return [];
    }


    private function saveDataToFile($data)
    {
        Storage::disk('local')->put('data.txt', json_encode($data));
        Storage::disk('local')->put('public/data.txt', json_encode($data));
    }

    private function getHurufFromNilai($nilai)
    {
        if ($nilai >= 80 && $nilai <= 100) {
            return 'A';
        } elseif ($nilai >= 75 && $nilai < 80) {
            return 'B+';
        } elseif ($nilai >= 70 && $nilai < 75) {
            return 'B';
        } elseif ($nilai >= 65 && $nilai < 70) {
            return 'C+';
        } elseif ($nilai >= 60 && $nilai < 65) {
            return 'C';
        } elseif ($nilai >= 55 && $nilai < 60) {
            return 'D+';
        } elseif ($nilai >= 50 && $nilai < 55) {
            return 'D';
        } elseif ($nilai >= 0 && $nilai < 50) {
            return 'E';
        }

        return null;
    }
}
