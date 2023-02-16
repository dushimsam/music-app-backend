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
    public function it_should_return_authenticated_user()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/api/auth/self');
        $response->assertStatus(JsonResponse::HTTP_OK);
        $this->assertEquals($user->toArray(), json_decode($response->getContent(), true));
    }

    /** @test */
    public function it_should_register_user()
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
    public function it_should_not_register_user_with_invalid_data()
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
    public function it_should_login_user_with_email()
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
    public function it_should_login_user_with_username()
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
    public function it_should_not_login_user_with_invalid_credentials()
    {
        $response = $this->post('/api/auth/login', [
            'login' => 'ivn@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(JsonResponse::HTTP_BAD_REQUEST);
        $this->assertNotEmpty($response->getContent());
    }
}
