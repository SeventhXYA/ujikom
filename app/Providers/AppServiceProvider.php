<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //Memvalidasi bahwa validasi unique_nipt memiliki tugas untuk mengecek apakah nipt pada data.txt sudah ada atau belum (nipt tidak bileh sama)
        Validator::extend('unique_nipt', function ($attribute, $value, $parameters, $validator) {
            $fileContent = Storage::disk('local')->exists('data.txt') ? Storage::disk('local')->get('data.txt') : '[]'; //Cek apakah data.txt ada atau belum
            $data = json_decode($fileContent, true);

            foreach ($data as $item) {
                if ($item['nipt'] === $value) {
                    return false;
                }
            }

            return true;
        });
    }
}
