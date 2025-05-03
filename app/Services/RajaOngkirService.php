<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RajaOngkirService
{
    protected $apiKey;
    protected $baseUrl = 'https://pro.rajaongkir.com/api';

    public function __construct()
    {
        $this->apiKey = config('services.rajaongkir.key');
    }

    /**
     * Calculate shipping cost
     *
     * @param array $params
     * @return array
     */
    public function calculateCost(array $params): array
    {
        try {
            $response = Http::withHeaders([
                'key' => $this->apiKey
            ])->post("{$this->baseUrl}/cost", [
                'origin' => $params['origin'],
                'originType' => $params['originType'] ?? 'city',
                'destination' => $params['destination'],
                'destinationType' => $params['destinationType'] ?? 'city',
                'weight' => $params['weight'],
                'courier' => $params['courier'] ?? 'jne',
                'length' => $params['length'] ?? null,
                'width' => $params['width'] ?? null,
                'height' => $params['height'] ?? null,
                'diameter' => $params['diameter'] ?? null,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('RajaOngkir API Error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [
                'status' => 'error',
                'message' => 'Failed to calculate shipping cost'
            ];
        } catch (\Exception $e) {
            Log::error('RajaOngkir Service Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'status' => 'error',
                'message' => 'An error occurred while calculating shipping cost'
            ];
        }
    }
}
