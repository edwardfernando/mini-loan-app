<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoanControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testLoanCanBeCreated()
    {
      $data = [
        'amount' => 10000,
        'term' => 3,
        'state' => 'PENDING'
      ];

      $response = $this->json('POST','/loans', $data);
      $response->assertStatus(201)
        ->assertJson([
          'status' => 'success',
          'data' => [
            'amount' => 10000,
            'term' => 3,
            'state' => 'PENDING'
          ]
          ]);
    }

    public function testAmountIsRequired()
    {
      $data = [
        'term' => 3,
      ];

      $response = $this->json('POST', '/loans', $data);
      $response->assertStatus(422)
        ->assertJsonStructure([
            'status',
            'message' => [
                'amount'
            ]
        ]);
    }

    public function testTermIsRequired()
    {
      $data = [
        'amount' => 10000,
      ];

      $response = $this->json('POST', '/loans', $data);
      $response->assertStatus(422)
        ->assertJsonStructure([
            'status',
            'message' => [
                'term'
            ]
        ]);
    }
}
