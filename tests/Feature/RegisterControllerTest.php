<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testCanRegisterUser()
    {
        $name = $this->faker->name;
        $email = $this->faker->safeEmail;
        $password = $this->faker->password(8);


        $payload = [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'c_password' => $password,
        ];

        $response = $this->json('POST', '/register', $payload);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'user register successfully',
            ]);

        $this->assertDatabaseHas('users', [
            'name' => $name,
            'email' => $email,
        ]);
    }

    public function testRegisterFailsWithExistingEmail()
    {
        $existingUser = User::factory()->create();

        $payload = [
          'name' => $this->faker->name,
          'email' => $existingUser->email,
          'password' => $this->faker->password(8),
          'c_password' => $this->faker->password(8),
        ];

        $response = $this->json('POST', '/register', $payload);

        $response->assertStatus(422)
          ->assertJson([
            'status' => 'error',
            'message' => array('email' => array( 0 => 'The email has already been taken.'))
            ])
          ->assertJsonStructure([
              'status',
              'message' => [
                  'email'
              ]
          ]);
    }

    public function testRegisterFailsWithInvalidData()
    {
        $response = $this->json('POST', '/register', [
            'name' => '',
            'email' => 'not_a_valid_email',
            'password' => '',
            'c_password' => 'not_matching',
        ]);

        $response->assertStatus(422)
          ->assertJson([
            'status' => 'error',
            'message' => array(
              'email' => array( 0 => 'The email field must be a valid email address.'),
              'name' => array( 0 => 'The name field is required.'),
              'password' => array( 0 => 'The password field is required.'),
              'c_password' => array( 0 => 'The c password field must match password.'),
              )
            ])
          ->assertJsonStructure([
              'status',
              'message' => [
                  'name',
                  'email',
                  'password',
                  'c_password'
              ]
          ]);
    }
}
