<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\AgenService;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Facades\Cache;

class AgenServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    #[Test]
    public function it_returns_agents_array_with_expected_keys()
    {
        $agents = AgenService::getAll();
        $this->assertIsArray($agents);
        $this->assertNotEmpty($agents, 'AgenService::getAll() should return at least one agent.');
        $agent = $agents[0];
        $this->assertArrayHasKey('nama', $agent);
        $this->assertArrayHasKey('kecamatan', $agent);
        $this->assertArrayHasKey('kota', $agent);
        $this->assertArrayHasKey('provinsi', $agent);
    }
}
