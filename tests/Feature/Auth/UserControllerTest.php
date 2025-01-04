<?php

namespace Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase,
        WithFaker;

    /** @test */
    public function it_returns_authenticated_user(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make($password = $this->faker->password),
        ]);

        $loginResponse = $this->postJson(route('auth.login'), [
            'email' => $user->email,
            'password' => $password,
        ]);

        $loginResponse->assertStatus(200);

        $token = $loginResponse->json('token');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->getJson(route('auth.user'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'name',
                'email',
            ]);
    }

    /** @test */
    public function it_returns_unauthorized_error_when_token_is_invalid(): void
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->faker->password}",
        ])->getJson(route('auth.user'));

        $response->assertStatus(401);
    }

    /** @test */
    public function it_returns_unauthorized_error_when_token_is_expired(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make($password = $this->faker->password),
        ]);

        $loginResponse = $this->postJson(route('auth.login'), [
            'email' => $user->email,
            'password' => $password,
        ]);

        $loginResponse->assertStatus(200);

        $token = $loginResponse->json('token');

        $this->travel(config('auth.expiration_minutes') + 1)->minutes();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->getJson(route('auth.user'));

        $response->assertStatus(401);
    }
}
