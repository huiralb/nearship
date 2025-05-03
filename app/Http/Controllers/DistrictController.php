<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DistrictService;

class DistrictController extends Controller
{
    public function search(Request $request)
    {
        $keyword = $request->query('q', '');
        $results = DistrictService::search($keyword);
        return response()->json($results);
    }
}
