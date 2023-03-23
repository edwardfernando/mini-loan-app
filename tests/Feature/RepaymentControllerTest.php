<?php

namespace Tests\Feature;

use App\Models\Loan;
use App\Models\User;
use App\Models\ScheduledRepayment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class RepaymentControllerTest extends TestCase
{
  use RefreshDatabase;
  use WithoutMiddleware;

  public function test_store_success_create_new_repayment_full_paid()
  {
    $loan = Loan::factory()->create([
      "state" => "APPROVED",
    ]);

    $scheduledRepayment = new ScheduledRepayment();
    $scheduledRepayment->loan_id = $loan->id;
    $scheduledRepayment->due_date = Carbon::now()->addDays(30);
    $scheduledRepayment->amount = 10000;
    $scheduledRepayment->state = 'PENDING';
    $scheduledRepayment->save();


    $data = [
      'scheduled_repayment_id' => $scheduledRepayment->id,
      'amount' => 10000
    ];

    $response = $this->json('POST', '/repayments', $data);
    $response->assertStatus(201);

    $updatedScheduledRepayment = ScheduledRepayment::find($scheduledRepayment->id);
    $this->assertEquals($updatedScheduledRepayment->state, 'PAID');
  }

  public function test_store_success_create_new_repayment_partial_paid()
  {
    $loan = Loan::factory()->create([
      "state" => "APPROVED",
    ]);

    $scheduledRepayment = new ScheduledRepayment();
    $scheduledRepayment->loan_id = $loan->id;
    $scheduledRepayment->due_date = Carbon::now()->addDays(30);
    $scheduledRepayment->amount = 10000;
    $scheduledRepayment->state = 'PENDING';
    $scheduledRepayment->save();


    $data = [
      'scheduled_repayment_id' => $scheduledRepayment->id,
      'amount' => 3000
    ];

    $response = $this->json('POST', '/repayments', $data);
    $response->assertStatus(201);

    $updatedScheduledRepayment = ScheduledRepayment::find($scheduledRepayment->id);
    $this->assertEquals($updatedScheduledRepayment->state, 'PARTIAL');
  }

  public function test_store_fail_required_params_not_exist()
  {
    $data = [];
    $response = $this->json('POST', '/repayments', $data);
    $response->assertStatus(422);
  }

  public function test_store_fail_scheduled_payment_is_paid()
  {
    $loan = Loan::factory()->create([
      "state" => "APPROVED",
    ]);

    $scheduledRepayment = new ScheduledRepayment();
    $scheduledRepayment->loan_id = $loan->id;
    $scheduledRepayment->due_date = Carbon::now()->addDays(30);
    $scheduledRepayment->amount = 10000;
    $scheduledRepayment->state = 'PAID';
    $scheduledRepayment->save();
   
    $data = [
      'scheduled_repayment_id' => $scheduledRepayment->id,
      'amount' => 10000
    ];

    $response = $this->json('POST', '/repayments', $data);
    $response->assertStatus(422)
      ->assertJson([
        'status' => 'error',
        'message' => 'The scheduled repayment has been fully paid.'
      ]);
  }

  public function test_store_fail_input_amount_greater_than_scheduled_repayment_amount()
  {
    $loan = Loan::factory()->create([
      "state" => "APPROVED",
    ]);

    $scheduledRepayment = new ScheduledRepayment();
    $scheduledRepayment->loan_id = $loan->id;
    $scheduledRepayment->due_date = Carbon::now()->addDays(30);
    $scheduledRepayment->amount = 10000;
    $scheduledRepayment->state = 'PENDING';
    $scheduledRepayment->save();
   
    $data = [
      'scheduled_repayment_id' => $scheduledRepayment->id,
      'amount' => 999999
    ];

    $response = $this->json('POST', '/repayments', $data);
    $response->assertStatus(422)
      ->assertJson([
        'status' => 'error',
        'message' => 'The repayment amount cannot be greater than the scheduled repayment amount.'
      ]);
  }

  public function test_store_fail_loan_is_not_approved()
  {
    $loan = Loan::factory()->create([
      "state" => "PENDING"
    ]);

    $scheduledRepayment = new ScheduledRepayment();
    $scheduledRepayment->loan_id = $loan->id;
    $scheduledRepayment->due_date = Carbon::now()->addDays(30);
    $scheduledRepayment->amount = 10000;
    $scheduledRepayment->state = 'PENDING';
    $scheduledRepayment->save();
   
    $data = [
      'scheduled_repayment_id' => $scheduledRepayment->id,
      'amount' => 999999
    ];

    $response = $this->json('POST', '/repayments', $data);
    $response->assertStatus(422)
      ->assertJson([
        'status' => 'error',
        'message' => 'Can not make repayment. Your loan has not been approved.'
      ]);
  }
}