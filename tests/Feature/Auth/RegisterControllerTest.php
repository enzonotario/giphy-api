<?php

namespace Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase,
        WithFaker;

    /** @test */
    public function it_registers_a_new_user(): void
    {
        $response = $this->postJson(route('auth.register'), [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'name',
                'email',
            ]);
    }

    /** @test */
    public function it_returns_validation_error_when_registering_a_new_user_with_invalid_data(): void
    {
        $response = $this->postJson(route('auth.register'), [
            'name' => '',
            'email' => 'invalid-email',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'name',
                'email',
            ]);
    }

    /** @test */
    public function it_returns_validation_error_when_registering_a_new_user_with_existing_email(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson(route('auth.register'), [
            'name' => $this->faker->name,
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'email',
            ]);
    }
}
