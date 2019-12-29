<?php

namespace Tests\Feature;

use App\Models\ProviderModel;
use App\Models\TransactionModel;
use App\Services\Providers\ProviderInterface;
use App\Services\TransactionService;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    private array $newTransaction = [
        'user_id'           =>  1,
        'details'           =>  'Test payment',
        'receiver_account'  =>  'LT186513546853135841',
        'receiver_name'     =>  'John Doe',
        'amount'            =>  110,
        'currency'          =>  'eur',
    ];

    /** @test */
    public function transactionMustBeCreated()
    {
        // Truncate `transaction` table on first test
        TransactionModel::truncate();

        $response = $this->postJson(
            '/api/transaction',
            $this->newTransaction
        );

        $response->assertJson([
            'details'           => $this->newTransaction['details'],
            'receiver_account'  => $this->newTransaction['receiver_account'],
            'receiver_name'     => $this->newTransaction['receiver_name'],
            'amount'            => $this->newTransaction['amount'],
            'currency'          => $this->newTransaction['currency'],
            'error_code'        => null,
        ]);
    }

    /** @test */
    public function transactionValidationFailsBecauseReceiverNameIsMissing()
    {
        unset($this->newTransaction['receiver_name']);

        $response = $this->postJson(
            '/api/transaction',
            $this->newTransaction
        );

        $response->assertJson([
            'error_code' => TransactionService::ERROR_BAD_INPUT,
        ]);
    }

    /** @test */
    public function transactionValidationFailsBecauseReceiverAccountIsTooShort()
    {
        $this->newTransaction['receiver_account'] = '123';

        $response = $this->postJson(
            '/api/transaction',
            $this->newTransaction
        );

        $response->assertJson([
            'error_code' => TransactionService::ERROR_BAD_INPUT,
        ]);
    }

    /** @test */
    public function transactionsTotalLimitExceeded()
    {
        $this->newTransaction['amount'] = 900;

        $response = $this->postJson(
            '/api/transaction',
            $this->newTransaction
        );

        $response->assertJson([
            'error_code' => TransactionService::ERROR_TOTAL_LIMIT,
        ]);
    }

    /** @test */
    public function transactionsOneHourLimitExceeded()
    {
        $this->newTransaction['amount'] = 1;
        $this->newTransaction['currency'] = 'usd';

        // Add extra 8 transactions
        for ($i=1; $i<=9; ++$i) {
            $this->postJson(
                '/api/transaction',
                $this->newTransaction
            );
        }

        $response = $this->postJson(
            '/api/transaction',
            $this->newTransaction
        );

        $response->assertJson([
            'error_code' => TransactionService::ERROR_HOUR_LIMIT,
        ]);
    }

    /** @test */
    public function transactionProviderIsDisabled()
    {
        // Change USD provider status to disabled
        ProviderModel::whereProviderKey(ProviderInterface::NON_EUR_PROVIDER)
            ->update(['status' => ProviderInterface::STATUS_DISABLED]);

        $this->newTransaction['user_id'] = 2;
        $this->newTransaction['currency'] = 'usd';

        $response = $this->postJson(
            '/api/transaction',
            $this->newTransaction
        );

        $response->assertJson([
            'error_code' => TransactionService::ERROR_PROVIDER_NOT_FOUND,
        ]);

        // Change USD provider status back to active
        ProviderModel::whereProviderKey(ProviderInterface::NON_EUR_PROVIDER)
            ->update(['status' => ProviderInterface::STATUS_ACTIVE]);
    }

    /** @test */
    public function transactionConfirmedSuccessfully()
    {
        $response = $this->postJson(
            '/api/transaction/1/confirm',
            [
                'code' => 111
            ]
        );

        $response->assertJson([
            'status' => TransactionService::STATUS_CONFIRMED,
        ]);
    }

    /** @test */
    public function transactionConfirmedBeforeAndNotFoundNow()
    {
        $response = $this->postJson(
            '/api/transaction/1/confirm',
            [
                'code' => 111
            ]
        );

        $response->assertJson([
            'error_code' => TransactionService::ERROR_NOT_FOUND,
        ]);
    }

    /** @test */
    public function transactionNotConfirmedBecauseOfBadCode()
    {
        $response = $this->postJson(
            '/api/transaction/3/confirm',
            [
                'code' => 112
            ]
        );

        $response->assertJson([
            'error_code' => TransactionService::ERROR_BAD_AUTHENTICATION,
        ]);
    }

    /** @test */
    public function transactionConfirmedProcessedAndCheckedToBeCompleted()
    {
        $response = $this->postJson(
            '/api/transaction/3/confirm',
            [
                'code' => 111
            ]
        );

        $response->assertJson([
            'status' => TransactionService::STATUS_CONFIRMED,
        ]);

        $this->artisan('processConfirmedPayments');

        $response = $this->get('/api/transaction/3');

        $response->assertJson([
            'status' => TransactionService::STATUS_COMPLETED,
        ]);
    }

    /** @test */
    public function transactionNotFound()
    {
        $response = $this->get('/api/transaction/100');

        $response->assertJson([
            'error_code' => TransactionService::ERROR_NOT_FOUND,
        ]);
    }
}
