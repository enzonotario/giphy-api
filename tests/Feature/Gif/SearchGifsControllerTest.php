<?php

namespace Feature\Gif;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SearchGifsControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create([
            'password' => Hash::make($password = $this->faker->password),
        ]);

        $loginResponse = $this->postJson(route('auth.login'), [
            'email' => $user->email,
            'password' => $password,
        ]);

        $loginResponse->assertStatus(200);

        $this->token = $loginResponse->json('token');
    }

    /** @test */
    public function it_searches_gifs(): void
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->getJson(route('gif.search', [
            'query' => 'cats',
            'offset' => 1,
            'limit' => 1,
        ]));

        $response->assertStatus(200)->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'url',
                    'title',
                    'images' => [
                        'original' => [
                            'url',
                        ],
                    ],
                ],
            ],
        ]);
    }

    /** @test */
    public function it_requires_a_query_parameter(): void
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->getJson(route('gif.search'));

        $response->assertStatus(422)->assertJsonValidationErrors('query');
    }

    /** @test */
    public function it_requires_a_valid_limit_parameter(): void
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->getJson(route('gif.search', [
            'query' => 'cats',
            'limit' => $this->faker->numberBetween(101, 1000),
        ]));

        $response->assertStatus(422)->assertJsonValidationErrors('limit');
    }

    /** @test */
    public function it_requires_a_valid_offset_parameter(): void
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->getJson(route('gif.search', [
            'query' => 'cats',
            'offset' => $this->faker->numberBetween(-1000, -1),
        ]));

        $response->assertStatus(422)->assertJsonValidationErrors('offset');
    }

    /** @test */
    public function it_requires_a_valid_query_parameter(): void
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->getJson(route('gif.search', [
            'query' => '  ',
        ]));

        $response->assertStatus(422)->assertJsonValidationErrors('query');
    }
}
