<?php

namespace App\Services;

use App\Services\DistrictService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class AgenService
{
    public static function getLocal()
    {
        return Cache::remember('agents-data-2', now()->addHours(12), function () {
            $path = base_path('database/AGEN_kustom.json');
            if (!file_exists($path)) {
                return [];
            }
            $json = file_get_contents($path);
            $items = collect(json_decode($json))->map(function($item) {
                $district = DistrictService::getByAgent($item);
                return array_merge((array)$item, (array)$district);
            });
            $data = $items->values()->all();
            // Simpan ke storage lokal juga
            Storage::disk('local')->put('agents.json', json_encode($data));

            return $data;
        });
    }

    public static function get()
    {
        // Simpan cache selama 12 jam
        return Cache::remember('agents-data', now()->addHours(12), function () {
            $url = 'https://raw.githubusercontent.com/huiralb/wilayah_elixir/refs/heads/master/data/agents.json';

            $response = Http::get($url);

            if ($response->successful()) {
                $jsonData = $response->body();
                // Simpan ke storage lokal juga
                Storage::disk('local')->put('agents.json', $jsonData);
                return json_decode($jsonData, true);
            }

            return [];
        });
    }

    public static function search($district)
    {
        $agents = self::getLocal();
        return collect($agents)->filter(function ($agent) use ($district) {
            return $agent['subdistrict_name'] == $district['subdistrict_name'];
        })
        ->sortBy([
            fn (array $a, array $b) => strtolower($a['subdistrict_name'] == $b['subdistrict_name']),
            fn (array $a, array $b) => strtolower($a['city'] == $b['city']),
            fn (array $a, array $b) => strtolower($a['province'] == $b['province']),
        ])
        ->values()->all();
    }
}
