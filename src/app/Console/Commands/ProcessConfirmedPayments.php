<?php

namespace App\Console\Commands;

use App\Services\TransactionService;
use Illuminate\Console\Command;

/**
 * Class ProcessConfirmedPayments
 * @package App\Console\Commands
 */
class ProcessConfirmedPayments extends Command
{
    /**
     * @var string
     */
    protected $signature = 'processConfirmedPayments';

    private TransactionService $transactionService;

    /**
     * ProcessConfirmedPayments constructor.
     * @param TransactionService $transactionService
     */
    public function __construct(TransactionService $transactionService)
    {
        parent::__construct();

        $this->transactionService = $transactionService;
    }

    public function handle()
    {
        $this->transactionService->processConfirmedTransactions();
    }
}
