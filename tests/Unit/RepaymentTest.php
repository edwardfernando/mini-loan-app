<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Repayment;
use App\Models\ScheduledRepayment;
use App\Models\Loan;
use Carbon\Carbon;

class RepaymentTest extends TestCase
{
    use RefreshDatabase;
    
    public function testRepaymentCanBeCreate()
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

        $repayment = new Repayment();
        $repayment->scheduled_repayment_id = $scheduledRepayment->id;
        $repayment->amount = 3333.3;
        $repayment->save();

        $savedRepayment = Repayment::find($repayment->id);
        $this->assertEquals($repayment->scheduled_repayment_id, $savedRepayment->scheduled_repayment_id);
        $this->assertEquals($repayment->amount, $savedRepayment->amount);
    }
}
