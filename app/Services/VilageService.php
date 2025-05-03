<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class VilageService
{
    public static function getVillages()
    {
        // Simpan cache selama 12 jam
        return Cache::remember('villages-data', now()->addHours(12), function () {
            $url = 'https://raw.githubusercontent.com/yusufsyaifudin/wilayah-indonesia/refs/heads/master/data/list_of_area/villages.json';

            $response = Http::get($url);

            if ($response->successful()) {
                $jsonData = $response->body();
                // Simpan ke storage lokal juga
                Storage::disk('local')->put('villages.json', $jsonData);
                return json_decode($jsonData, true);
            }

            return [];
        });
    }

    public static function search($keyword)
    {
        // Ambil dari cache, jika tidak ada ambil dari file lokal
        $villages = Cache::remember('villages-data', now()->addHours(12), function () {
            if (Storage::disk('local')->exists('villages.json')) {
                $json = Storage::disk('local')->get('villages.json');
                return json_decode($json, true);
            }
            return [];
        });

        return collect($villages)->filter(function ($village) use ($keyword) {
            return str_contains(strtolower($village['name']), strtolower($keyword));
        })->values()->all();
    }
}
