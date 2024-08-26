<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ApiController extends Controller
{
    public function getProvinces()
    {
        // Cache data provinsi selama 1 jam untuk mengurangi request ke API eksternal
        $provinces = Cache::remember('provinces', 3600, function () {
            return Http::get('https://faisal-ltf.github.io/api-wilayah-indonesia/api/provinces.json')->json();
        });

        return response()->json($provinces);
    }

    public function getRegencies($provinceId)
    {
        $regencies = Cache::remember("regencies_{$provinceId}", 3600, function () use ($provinceId) {
            return Http::get("https://faisal-ltf.github.io/api-wilayah-indonesia/api/regencies/{$provinceId}.json")->json();
        });

        return response()->json($regencies);
    }
}
