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
        Validator::extend('unique_nipt', function ($attribute, $value, $parameters, $validator) {
            $fileContent = Storage::disk('local')->exists('data.txt') ? Storage::disk('local')->get('data.txt') : '[]';
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
