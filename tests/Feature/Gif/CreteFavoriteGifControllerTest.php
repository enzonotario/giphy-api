<?php

namespace Feature\Gif;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CreteFavoriteGifControllerTest extends TestCase
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
    public function it_creates_a_favorite_gif(): void
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->postJson(route('gif.favorite.create'), [
            'gif_id' => Arr::get($this->randomGif, 'id'),
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('favorites', [
            'user_id' => User::first()->id,
            'gif_id' => $response->json('gif_id'),
        ]);
    }

    /** @test */
    public function it_does_not_create_a_favorite_gif_twice(): void
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->postJson(route('gif.favorite.create'), [
            'gif_id' => Arr::get($this->randomGif, 'id'),
        ]);

        $response->assertStatus(201);

        $anotherResponse = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->postJson(route('gif.favorite.create'), [
            'gif_id' => Arr::get($this->randomGif, 'id'),
        ]);

        $anotherResponse->assertStatus(422);

        $this->assertDatabaseCount('favorites', 1);
    }
}
