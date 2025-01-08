<?php

namespace Feature\Gif;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class GetGifControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $token;

    protected $randomGif;

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

        $searchResponse = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->getJson(route('gif.search', [
            'query' => 'dogs',
            'offset' => 1,
            'limit' => 1,
        ]));

        $searchResponse->assertStatus(200);

        $this->randomGif = $this->faker->randomElement($searchResponse->json('data'));
    }

    /** @test */
    public function it_gets_a_gif(): void
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->getJson(route('gif.get', [
            'id' => Arr::get($this->randomGif, 'id'),
        ]));

        $response->assertStatus(200)->assertJsonStructure([
            'data' => [
                'id',
                'url',
                'title',
                'images' => [
                    'original' => [
                        'url',
                    ],
                ],
            ],
        ]);

        $response->assertJsonFragment(
            Arr::only($this->randomGif, [
                'type',
                'id',
                'url',
                'slug',
                'title',
            ])
        );
    }

    /** @test */
    public function it_handles_not_found_gif(): void
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->getJson(route('gif.get', [
            'id' => Str::random(18),
        ]));

        $response->assertStatus(404);
    }

    /** @test */
    public function it_handles_invalid_gif_id(): void
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->getJson(route('gif.get', [
            'id' => $this->faker->uuid,
        ]));

        $response->assertStatus(422);
    }
}
