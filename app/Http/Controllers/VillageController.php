<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\VilageService;

class VillageController extends Controller
{
    public function search(Request $request)
    {
        $keyword = $request->query('q', '');
        $results = VilageService::search($keyword);
        return response()->json($results);
    }
}
