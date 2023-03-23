<?php

namespace Tests\Feature;

use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoanControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithoutMiddleware;

    public function test_index_success_returns_all_loans()
    {
      $loans = Loan::factory()->count(3)->create();

      $response = $this->json('GET', 'loans');
      $response->assertStatus(200);
      $response->assertJsonCount(3, 'data');
      $response->assertJsonStructure([
        'status',
        'data' => [
            '*' => [
                'id',
                'amount',
                'term',
                'state',
                'created_at',
                'updated_at',
            ],
          ],
      ]);
    }

    public function test_show_returns_the_specified_loan()
    {
      $loan = Loan::factory()->create();

      $response = $this->json('GET', '/loans/' . $loan->id);
      $response->assertStatus(200);
      $response->assertJson([
        'status' => 'success',
        'data' => [
                'id' => $loan->id,
                'amount' => $loan->amount,
                'term' => $loan->term,
                'state' => $loan->state,
            ],
        ]);
    }

    public function test_show_returns_404_for_non_existance_loan()
    {
      $response = $this->json('GET', '/loans/' . 999);
      $response->assertStatus(404);
      $response->assertJson([
        'status' => 'error',
        'message' => 'Loan not found'
        ]);
    }

    public function test_store_success_create_new_loan()
    {
      $user = User::factory()->create();
      $this->actingAs($user);

      $data = [
        'amount' => 10000,
        'term' => 2,
        'state' => 'PENDING'
      ];

      $response = $this->json('POST','/loans', $data);
      $response->assertStatus(201)
        ->assertJson([
          'status' => 'success',
          'data' => [
            'amount' => 10000,
            'term' => 2,
            'state' => 'PENDING',
            'scheduled_repayments' => array( 
              0 => array ('loan_id' => 1, 'amount' => 5000, 'state' => 'PENDING'),
              1 => array ('loan_id' => 1, 'amount' => 5000, 'state' => 'PENDING')
              )
          ]
          ]);
    }

    public function test_store_failed_amount_field_is_required()
    {
      $data = [
        'term' => 3,
      ];

      $response = $this->json('POST', '/loans', $data);
      $response->assertStatus(422)
        ->assertJson([
          'status' => 'error',
          'message' => array('amount' => array( 0 => 'The amount field is required.'))
          ])
        ->assertJsonStructure([
            'status',
            'message' => [
                'amount'
            ]
        ]);
    }

    public function test_store_failed_term_field_is_required()
    {
      $data = [
        'amount' => 10000,
      ];

      $response = $this->json('POST', '/loans', $data);
      $response->assertStatus(422)
        ->assertJson([
          'status' => 'error',
          'message' => array('term' => array( 0 => 'The term field is required.'))
          ])
        ->assertJsonStructure([
            'status',
            'message' => [
                'term'
            ]
        ]);
    }

    public function test_update_success_update_existing_loan()
    {
      $loan = Loan::factory()->create();
      
      $data = [
        'amount' => 999,
        'term' => 999,
        'state' => 'RANDOM_STATE'
      ];

      $response = $this->json('PUT', '/loans/' . $loan->id, $data);
      $response->assertStatus(200);

      $updatedLoan = Loan::find($loan->id);
      $this->assertEquals($updatedLoan->amount, $data['amount']);
      $this->assertEquals($updatedLoan->term, $data['term']);
      $this->assertEquals($updatedLoan->state, $data['state']);
    }

    public function test_update_returns_404_for_non_existance_loan()
    {
      $data = [
        'amount' => 999,
        'term' => 999,
        'state' => 'RANDOM_STATE'
      ];

      $response = $this->json('PUT', '/loans/' . 999, $data);
      $response->assertStatus(404);
      $response->assertJson([
        'status' => 'error',
        'message' => 'Loan not found'
        ]);
    }

    public function test_destroy_success_deletes_existing_loan()
    {
      $loan = Loan::factory()->create();

      $response = $this->json('DELETE', '/loans/' . $loan->id);
      $response->assertStatus(200);

      $deletedLoan = Loan::find($loan->id);
      $this->assertNull($deletedLoan);
    }

    public function test_destroy_return_404_for_non_existance_loan()
    {
      $response = $this->json('DELETE', '/loans/' . 999);
      $response->assertStatus(404);
      $response->assertJson([
        'status' => 'error',
        'message' => 'Loan not found'
        ]);
    }

    public function test_approve_success_existing_loan()
    {
      $loan = Loan::factory()->create();
      $response = $this->json('PUT', '/loans/' . $loan->id . '/approve');
      $response->assertStatus(200);
    }
}
