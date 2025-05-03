<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AgenService;

class AgenController extends Controller
{
    public function nearby(Request $request)
    {
        $district = $request->post('tujuan');

        $agents = AgenService::search($district);

        return response()->json($agents);
    }
}
