<?php

namespace App\Http\Controllers;

use App\Services\RajaOngkirService;
use Illuminate\Http\Request;

class CekOngkirController extends Controller
{
    protected $rajaOngkirService;

    public function __construct(RajaOngkirService $rajaOngkirService)
    {
        $this->rajaOngkirService = $rajaOngkirService;
    }

    public function handle(Request $request)
    {
        $validated = $request->validate([
            'asal.subdistrict_id' => 'required|string',
            'tujuan.subdistrict_id' => 'required|string',
            'agen.subdistrict_id' => 'required|string',
            'berat' => 'required|integer|min:1',
            'satuan' => 'nullable|string'
        ], [
            'asal.subdistrict_id.required' => 'The field is required',
            'tujuan.subdistrict_id.required' => 'The field is required',
            'agen.subdistrict_id.required' => 'The field is required',
        ]);

        $result = $this->rajaOngkirService->calculateCost([
            'originType' => 'subdistrict',
            'destinationType' => 'subdistrict',
            'origin' => $validated['asal']['subdistrict_id'],
            'destination' => $validated['agen']['subdistrict_id'],
            'weight' => $validated['satuan'] == 'kg' ? (int)$validated['berat'] * 1000 : $validated['berat']
        ]);

        return back()->with('success', true)->with('data', $result);
    }
}
