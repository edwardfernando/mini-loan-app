<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Loan;
use App\Models\ScheduledRepayment;
use Carbon\Carbon;

class ScheduledRepaymentTest extends TestCase
{
    use RefreshDatabase;

    public function testScheduledRepaymentCanBeCreated()
    {
        $loan = new Loan();
        $loan->amount = 10000;
        $loan->term = 3;
        $loan->state = 'PENDING';
        $loan->save();

        $scheduledRepayment = new ScheduledRepayment();
        $scheduledRepayment->loan_id = $loan->id;
        $scheduledRepayment->due_date = Carbon::now()->addDays(30);
        $scheduledRepayment->amount = 10000;
        $scheduledRepayment->state = 'PENDING';
        $scheduledRepayment->save();

        $savedScheduledRepayment = ScheduledRepayment::find($scheduledRepayment->id);
        $this->assertEquals($savedScheduledRepayment->loan_id, $scheduledRepayment->loan_id);
        $this->assertEquals($savedScheduledRepayment->due_date, $scheduledRepayment->due_date);
        $this->assertEquals($savedScheduledRepayment->amount, $scheduledRepayment->amount);
        $this->assertEquals($savedScheduledRepayment->state, $scheduledRepayment->state);
    }
}
