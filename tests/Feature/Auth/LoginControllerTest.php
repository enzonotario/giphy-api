<?php

namespace Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase,
        WithFaker;

    /** @test */
    public function it_authenticate_an_user(): void
    {
        $this->freezeTime();

        $user = User::factory()->create([
            'password' => Hash::make($password = $this->faker->password),
        ]);

        $response = $this->postJson(route('auth.login'), [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'token',
                'expires_at',
            ]);

        $this->assertEquals(
            now()->addMinutes(config('auth.expiration_minutes'))->toIso8601String(),
            Date::parse($response->json('expires_at'))->toIso8601String()
        );
    }

    /** @test */
    public function it_returns_validation_error_when_logging_in_with_invalid_data(): void
    {
        $response = $this->postJson(route('auth.login'), [
            'email' => $this->faker->name,
            'password' => $this->faker->password,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'email',
            ]);
    }

    /** @test */
    public function it_returns_unauthorized_error_when_logging_in_with_wrong_credentials(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $response = $this->postJson(route('auth.login'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'error' => 'Unauthorized',
            ]);
    }

    /** @test */
    public function it_returns_unauthorized_when_token_is_expired(): void
    {
        $this->freezeTime();

        $user = User::factory()->create([
            'password' => Hash::make($password = $this->faker->password),
        ]);

        $response = $this->postJson(route('auth.login'), [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'token',
                'expires_at',
            ]);

        $this->assertEquals(
            now()->addMinutes(config('auth.expiration_minutes'))->toIso8601String(),
            Date::parse($response->json('expires_at'))->toIso8601String()
        );

        $this->travelTo(now()->addMinutes(config('auth.expiration_minutes'))->addSecond());

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$response->json('token'),
        ])->getJson(route('auth.user'));

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }
}
