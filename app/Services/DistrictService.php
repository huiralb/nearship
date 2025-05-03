<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\artisan;

class DistrictService
{
    /**
     * Data:
     * --
     *      address: "Arongan Lambalek, Aceh Barat, Nanggroe Aceh Darussalam (NAD)",
     *      city: "Aceh Barat",
     *      city_id: "1",
     *      postal_code: "23659",
     *      province: "Nanggroe Aceh Darussalam (NAD)",
     *      province_id: "21",
     *      subdistrict_id: "1",
     *      subdistrict_name: "Arongan Lambalek",
     *      type: "Kabupaten"
     * --
     */
    public static function get()
    {
        // Simpan cache selama 12 jam
        return Cache::remember('districts-data', now()->addHours(12), function () {
            $url = 'https://raw.githubusercontent.com/huiralb/wilayah_elixir/refs/heads/master/data/subdistricts.json';

            $response = Http::get($url);

            if ($response->successful()) {
                $jsonData = $response->body();
                // Simpan ke storage lokal juga
                Storage::disk('local')->put('districts.json', $jsonData);
                return json_decode($jsonData, true);
            }

            return [];
        });
    }

    public static function search($keyword)
    {
        // Ambil dari cache, jika tidak ada ambil dari file lokal
        $districts = Cache::remember('districts-data', now()->addHours(12), function () {
            if (Storage::disk('local')->exists('districts.json')) {
                $json = Storage::disk('local')->get('districts.json');
                return json_decode($json, true);
            }
            return [];
        });

        return collect($districts)->filter(function ($village) use ($keyword) {
            return str_contains(strtolower($village['province']), strtolower($keyword))
                || str_contains(strtolower($village['city']), strtolower($keyword))
                || str_contains(strtolower($village['subdistrict_name']), strtolower($keyword))
                ;
        })
        ->values()->all();
    }

    public static function seed()
    {

        if (Storage::disk('local')->exists('districts.json')) {
            $json = Storage::disk('local')->get('districts.json');
            $data =  json_decode($json, true);

            foreach($data as $row) {

            }
        }
    }
}
