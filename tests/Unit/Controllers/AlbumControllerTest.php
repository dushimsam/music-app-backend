<?php

namespace Tests\Unit\Controllers;


use App\Models\Album;
use App\Models\Song;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AlbumControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    // Test that the "all" method returns a list of all albums
    public function testAll()
    {
        Album::factory()->count(5)->create();

        $response = $this->getJson('/api/album');

        $response->assertOk()
            ->assertJsonCount(5);
    }

    // Test that the "allPaginated" method returns a paginated list of albums
    public function testAllPaginated()
    {
        $album = Album::factory()->create();
        $response = $this->get('/album/paginated');
        $response->assertStatus(200);
        $response->assertJsonFragment(['title' => $album->title]);
    }

    // Test that the "show" method returns the specified album
    public function testShow()
    {
        $album = Album::factory()->create();

        $response = $this->getJson('/api/album/' . $album->id);

        $response->assertOk()
            ->assertJsonFragment(['title' => $album->title]);
    }

    // Test that the "songs" method returns a paginated list of songs associated with the specified album
    public function testSongs()
    {
        $album = Album::factory()->create();
        Song::factory()->count(15)->create(['album_id' => $album->id, 'genre_id' => 2, 'title' => 'song title', 'length' => 34]);

        $response = $this->getJson('/api/album/' . $album->id . '/songs');

        $response->assertOk()
            ->assertJsonCount(10, 'data')
            ->assertJsonStructure(['data', 'links']);
    }

    // Test that the "create" method creates a new album
    public function testCreate()
    {
        // Define some test data for the new album
        $albumData = [
            'title' => 'New Album',
            'description' => 'A new album',
            'release_date' => '2023-02-15',
        ];

        // Make the request to the API endpoint for creating a new album
        $response = $this->post('/api/album', $albumData);

        // Assert that the response has a 201 status code
        $response->assertStatus(201);

        // Assert that the response has the correct album data
        $response->assertJsonFragment([
            'title' => $albumData['title'],
            'description' => $albumData['description'],
            'release_date' => $albumData['release_date'],
        ]);

    }

    /**
     * Test updating an existing album.
     */
    public function testUpdateAlbum()
    {
        // Create a test album
        $album = Album::factory()->create();

        // Define some test data for the updated album
        $albumData = [
            'title' => 'Updated Album',
            'description' => 'An updated album',
            'release_date' => '2023-02-15',
        ];

        // Make the request to the API endpoint for updating the album
        $response = $this->put('/api/album/' . $album->id, $albumData);

        // Assert that the response has a 201 status code
        $response->assertStatus(200);

        // Assert that the response has the correct album data
        $response->assertJsonFragment([
            'title' => $albumData['title'],
            'description' => $albumData['description'],
            'release_date' => $albumData['release_date'],
        ]);
    }

    public function test_destroy()
    {
        $id = 1;
        $album = Album::factory()->create(['id' => $id, 'title' => 'album title',
            'description' => 'description',
            'release_date' => '2023-02-15']);
        $song = Song::factory()->create(['album_id' => $id, 'genre_id' => 2, 'title' => 'song title', 'length' => 34]);
        $response = $this->delete("/album/{$id}");
        $response->assertStatus(200);
        $this->assertDeleted($album);
        $this->assertDeleted($song);
    }
}
