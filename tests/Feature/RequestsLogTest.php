<?php

namespace Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RequestsLogTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;

    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'password' => Hash::make($password = $this->faker->password),
        ]);

        $loginResponse = $this->postJson(route('auth.login'), [
            'email' => $this->user->email,
            'password' => $password,
        ]);

        $loginResponse->assertStatus(200);

        $this->token = $loginResponse->json('token');

        $this->assertDatabaseCount('request_logs', 1);
        $this->assertDatabaseHas('request_logs', [
            'user_id' => $this->user->id,
            'method' => 'POST',
            'path' => '/api/auth/login',
            'response_code' => 200,
        ]);
    }

    public function test_it_logs_a_request_in_the_database()
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->getJson(route('gif.search', [
            'query' => 'cats',
        ]));

        $response->assertStatus(200);

        $this->assertDatabaseCount('request_logs', 2);

        $this->assertDatabaseHas('request_logs', [
            'user_id' => $this->user->id,
            'method' => 'GET',
            'path' => '/api/gifs/search',
            'response_code' => 200,
            'request_body' => '{"query":"cats"}',
            'response_body' => $response->getContent(),
            'ip_address' => '127.0.0.1',
        ]);
    }
}
