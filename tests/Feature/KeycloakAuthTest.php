<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class KeycloakAuthTest extends TestCase
{
    /** @test */
    public function it_denies_access_without_token()
    {
        $response = $this->getJson('/api/profile');

        $response->assertStatus(401);
    }

    /** @test */
    public function it_denies_access_with_invalid_token()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer invalid.token.here',
        ])->getJson('/api/profile');

        $response->assertStatus(401);
    }

    // For testing with a valid token you would need to mock JWT decode
    // or use a real valid token if you have one available
}
