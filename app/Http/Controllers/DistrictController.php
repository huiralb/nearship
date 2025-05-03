<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AgenService;
use App\Services\DistrictService;
use Illuminate\Support\Facades\Redis;

class DistrictController extends Controller
{
    public function search(Request $request)
    {
        $keyword = $request->query('q', '');
        $results = DistrictService::search($keyword);
        return response()->json($results);
    }

    public function searchByAgent(Request $request)
    {
        $data = DistrictService::getByAgent($request->agent);

        return response()->json($data);
    }
}
