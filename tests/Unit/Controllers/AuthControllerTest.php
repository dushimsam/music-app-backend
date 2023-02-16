<?php

namespace Tests\Unit\Controllers;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;

class AuthControllerTest extends TestCase
{
    use  WithFaker;

    /** @test */
    public function itShouldReturnAuthenticatedUser()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/api/auth/self');
        $response->assertStatus(JsonResponse::HTTP_OK);
        $this->assertEquals($user->toArray(), json_decode($response->getContent(), true));
    }

    /** @test */
    public function shouldRegisterUser()
    {
        $data = [
            'full_name' => 'Samuel Dushimimana',
            'username' => 'dushsam',
            'email' => 'dushsam100@gmail.com',
            'password' => 'password@123'
        ];

        $response = $this->post('/api/auth/register', $data);
        $this->assertNotEmpty($response->getContent());
    }

    /** @test */
    public function shouldNotRegisterUserWithInvalidData()
    {
        $data = [
            'full_name' => '',
            'username' => '',
            'email' => '',
            'password' => '',
        ];

        $response = $this->post('/api/auth/register', $data);

        $response->assertStatus(JsonResponse::HTTP_BAD_REQUEST);
        $this->assertNotEmpty($response->getContent());
    }

    /** @test */
    public function shouldLoginUserWithEmail()
    {

        $password = $this->faker->password();
        $user = User::factory()->create([
            'password' => Hash::make($password),
        ]);

        $response = $this->post('/api/auth/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $this->assertNotEmpty($response->getContent());
    }

    /** @test */
    public function itShouldLoginUserWithUsername()
    {
        $password = $this->faker->password();
        $user = User::factory()->create([
            'password' => Hash::make($password),
        ]);

        $response = $this->post('/api/auth/login', [
            'login' => $user->username,
            'password' => $password,
        ]);

        $this->assertNotEmpty($response->getContent());
    }

    /** @test */
    public function itShouldNotLoginUserWithInvalidCredentials()
    {
        $response = $this->post('/api/auth/login', [
            'login' => 'ivn@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(JsonResponse::HTTP_BAD_REQUEST);
        $this->assertNotEmpty($response->getContent());
    }
}
