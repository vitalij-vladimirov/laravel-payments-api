<?php

namespace App\Services;

use App\Entities\ErrorCodeEntity;
use App\Entities\TransactionInputEntity;
use App\Entities\TransactionEntity;
use App\Repositories\ErrorCodeRepository;
use App\Repositories\TransactionRepository;

/**
 * Class TransactionService
 * @package App\Services
 */
class TransactionService
{
    const STATUS_RECEIVED   = 'RECEIVED';   // Transaction received from user
    const STATUS_APPROVED   = 'APPROVED';   // Transaction approved by user
    const STATUS_SUBMITTED  = 'SUBMITTED';  // Transaction submitted to provider
    const STATUS_COMPLETED  = 'COMPLETED';  // Transaction completed
    const STATUS_ERROR      = 'ERROR';      // Transaction terminate with error message

    const TRN_MAX           = 10;           // Max transactions per TRN_TIME_LIMIT
    const TRN_TIME_LIMIT    = 60;           // Time to limit TRN_MAX (minutes)
    const TRN_TOTAL_LIMIT   = 1000;         // Total maximum transaction limit in any currency

    const TRN_FEE_1         = 0.1;          // Fee #1 - 10%
    const TRN_FEE_2         = 0.05;         // Fee #2 - 5%
    const TRN_FEE_PERIOD    = 24 * 60;      // Period when amount sum is checked to switch fees
    const TRN_FEE_1_SWITCH  = 100;          // Total amount when fee #1 is switched to fee #2

    const ERROR_BAD_INPUT           = 'bad_input';
    const ERROR_PROVIDER_NOT_FOUND  = 'provider_not_found';
    const ERROR_TOTAL_LIMIT         = 'total_limit_reached';
    const ERROR_HOUR_LIMIT          = 'hour_limit_reached';
    const ERROR_BAD_AUTHENTICATION  = 'bad_authentication';
    const ERROR_NOT_FOUND           = 'not_found';

    /**
     * @param TransactionInputEntity $transactionInput
     * @param int|null $provider
     * @return TransactionEntity
     */
    public function setTransaction(TransactionInputEntity $transactionInput, ?int $provider): TransactionEntity
    {
        /** @var float $fee */
        $fee = (float) number_format($transactionInput->amount * $this->getFee($transactionInput->userId), 2, '.', ',');

        /** @var float $totalTransactionAmount */
        $totalTransactionAmount = $transactionInput->amount + $fee;

        /** @var TransactionEntity $transaction */
        $transaction = new TransactionEntity(
            null,
            $transactionInput->userId,
            $transactionInput->details,
            $transactionInput->receiverAccount,
            $transactionInput->receiverName,
            $transactionInput->amount,
            $fee,
            strtolower($transactionInput->currency),
            self::STATUS_RECEIVED,
            $provider
        );

        /** @var ErrorCodeEntity|null $error */
        $error = null;

        if (empty($provider)) {
            $error = ErrorCodeRepository::getError(self::ERROR_PROVIDER_NOT_FOUND);
        } elseif (
            $this->totalAmountLimitReached(
                $transaction->userId,
                $totalTransactionAmount,
                $transactionInput->currency
            )
        ) {
            $error = ErrorCodeRepository::getError(self::ERROR_TOTAL_LIMIT);
        } elseif ($this->hourlyLimitReached($transaction->userId)) {
            $error = ErrorCodeRepository::getError(self::ERROR_HOUR_LIMIT);
        }

        if (!empty($error)) {
            $transaction->status = self::STATUS_ERROR;
            $transaction->errorCode = $error->code;
            $transaction->errorMessage = $error->message;
        }

        return $transaction;
    }

    public function processApprovedTransactions()
    {
        /** @var TransactionEntity[] $approvedTransactions */
        $approvedTransactions = TransactionRepository::getApprovedTransactions();

        if(count($approvedTransactions) === 0) {
            return;
        }

        /** @var ProviderService $providerService */
        $providerService = new ProviderService();

        foreach ($approvedTransactions as $transaction) {
            TransactionRepository::updateTransactionStatus(
                $transaction->id,
                self::STATUS_SUBMITTED
            );

            /** @var string|null $providerTransactionId */
            $providerTransactionId = $providerService->processTransaction($transaction);

            if (empty($providerTransactionId)) {
                TransactionRepository::updateTransaction($transaction->id, [
                    'error_code' => self::ERROR_PROVIDER_NOT_FOUND,
                    'status' => self::STATUS_ERROR
                ]);

                continue;
            }

            TransactionRepository::updateTransaction($transaction->id, [
                'provider_trn_id' => $providerTransactionId,
                'status' => self::STATUS_COMPLETED
            ]);
        }
    }

    /**
     * @param int $userId
     * @return float
     */
    private function getFee(int $userId): float
    {
        /** @var float $dailyAmountSum */
        $dailyAmountSum = TransactionRepository::getTransactionsAmountPerUser(
            $userId,
            self::TRN_FEE_PERIOD
        );

        /** @var float $fee */
        $fee = ($dailyAmountSum < self::TRN_FEE_1_SWITCH) ? self::TRN_FEE_1 : self::TRN_FEE_2;

        return $fee;
    }

    /**
     * @param int $userId
     * @param float $transactionAmount
     * @param string $currency
     * @return bool
     */
    private function totalAmountLimitReached(int $userId, float $transactionAmount, string $currency): bool
    {
        /** @var float $totalAmountSum */
        $totalAmountSum = TransactionRepository::getTransactionsAmountPerUser(
            $userId,
            null,
            $currency
        );

        return ($totalAmountSum + $transactionAmount) > self::TRN_TOTAL_LIMIT;
    }

    /**
     * @param int $userId
     * @return bool
     */
    private function hourlyLimitReached(int $userId): bool
    {
        /** @var float $totalTransactionsCount */
        $lastHourTransactionsCount = TransactionRepository::getTransactionsCountPerUser(
            $userId,
            self::TRN_TIME_LIMIT
        );

        return $lastHourTransactionsCount >= self::TRN_MAX;
    }
}
