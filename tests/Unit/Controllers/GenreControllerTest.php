<?php

namespace Tests\Unit\Controllers;

use App\Models\Genre;
use App\Models\User;
use Illuminate\Http\Response;
use Tests\TestCase;

class GenreControllerTest extends TestCase
{
    public function testAllGenre()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/api/genre');
        $response->assertStatus(Response::HTTP_OK);
        $this->assertNotEmpty($response->getContent());
    }

    public function testAllGenrePaginated()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/api/genre/paginated');
        $response->assertStatus(Response::HTTP_OK);
        $this->assertNotEmpty($response->getContent());
    }

    public function testGetGenreById()
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        $response = $this->actingAs($user)->get('/api/genre/'.$genre->id);
        $response->assertStatus(Response::HTTP_OK);
        $this->assertNotEmpty($response->getContent());
    }

    public function testGetGenreSongs()
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        $response = $this->actingAs($user)->get('/api/genre/'.$genre->id.'/songs');
        $response->assertStatus(Response::HTTP_OK);
        $this->assertNotEmpty($response->getContent());
    }

    public function testCreateAlbum()
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->make();
        $response = $this->actingAs($user)->post('/api/genre', $genre->toArray());
        $this->assertNotEmpty($response->getContent());
    }

    public function testUpdateGenre()
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        $updatedGenre = Genre::factory()->make();
        $response = $this->actingAs($user)->put('/api/genre/'.$genre->id, $updatedGenre->toArray());
        $response->assertStatus(Response::HTTP_OK);
        $this->assertNotEmpty($response->getContent());
    }

    public function testDeleteAlbum()
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        $response = $this->actingAs($user)->delete('/api/genre/'.$genre->id);
        $response->assertStatus(Response::HTTP_OK);
        $this->assertNotEmpty($response->getContent());
    }
}