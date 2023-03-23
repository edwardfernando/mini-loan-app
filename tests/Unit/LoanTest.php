<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Loan;
use App\Models\User;

class LoanTest extends TestCase
{
    use RefreshDatabase;

    public function testLoanCanBeCreated()
    {
        $loan = new Loan();
        $loan->user_id = User::factory()->create()->id;
        $loan->amount = 10000;
        $loan->term = 3;
        $loan->state = 'PENDING';

        $loan->save();

        $savedLoad = Loan::find($loan->id);

        $this->assertEquals($loan->amount, $savedLoad->amount);
        $this->assertEquals($loan->term, $savedLoad->term);
        $this->assertEquals($loan->state, $savedLoad->state);
    }
}
