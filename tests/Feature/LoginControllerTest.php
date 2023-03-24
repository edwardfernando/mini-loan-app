<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test login with correct credentials
     *
     * @return void
     */
    public function testSuccessfulLogin()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password')
        ]);

        $payload = ['email' => $user->email, 'password' => 'password'];

        $response = $this->json('POST', '/login', $payload);

        $response
            ->assertStatus(JsonResponse::HTTP_OK)
            ->assertJson([
                'status' => 'success',
                'message' => 'user login successfully'
            ])
            ->assertJsonStructure([
                'data' => [
                    'token',
                    'name'
                ]
            ]);
    }

    /**
     * Test login with incorrect credentials
     *
     * @return void
     */
    public function testFailedLogin()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password')
        ]);

        $payload = ['email' => $user->email, 'password' => 'wrong_password'];

        $response = $this->json('POST', '/login', $payload);

        $response
            ->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'status' => 'error',
                'message' => 'Unauthorised'
            ]);
    }

    /**
     * Test logout
     *
     * @return void
     */
    public function testLogout()
    {
        $user = User::factory()->create();

        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->json('POST', '/logout');

        $response
            ->assertStatus(JsonResponse::HTTP_OK)
            ->assertJson([
                'message' => 'Logged out successfully'
            ]);
    }
}
