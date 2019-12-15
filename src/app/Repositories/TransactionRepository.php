<?php

namespace App\Repositories;

use App\Entities\TransactionEntity;
use App\Models\TransactionModel;
use App\Services\TransactionService;
use Exception;

/**
 * Class TransactionRepository
 * @package App\Repositories
 */
class TransactionRepository
{
    const ALLOWED_STATUS_CHANGING = [
        TransactionService::STATUS_APPROVED   => [
            TransactionService::STATUS_RECEIVED,
        ],
        TransactionService::STATUS_SUBMITTED  => [
            TransactionService::STATUS_APPROVED,
        ],
        TransactionService::STATUS_COMPLETED  => [
            TransactionService::STATUS_SUBMITTED,
        ],
        TransactionService::STATUS_ERROR  => [
            TransactionService::STATUS_RECEIVED,
            TransactionService::STATUS_APPROVED,
            TransactionService::STATUS_SUBMITTED,
        ]
    ];

    /**
     * @param int $userId
     * @param int|null $interval
     * @param string|null $currency
     * @return float
     */
    public static function getTransactionsAmountPerUser(
        int $userId,
        ?int $interval,
        string $currency = null
    ): float {
        $query = TransactionModel::whereUserId($userId)
            ->where('status', '<>', TransactionService::STATUS_ERROR);

        if (!empty($interval)) {
            $query->whereRaw('created_at >= DATE_SUB(NOW(), INTERVAL ' . $interval . ' MINUTE)');
        }

        if (!empty($currency)) {
            $query->where('currency', $currency);
        }

        $sum = $query->selectRaw('sum(amount + fee) as sum')
            ->first();

        return $sum->sum ?? 0;
    }

    /**
     * @param int $userId
     * @param int|null $interval
     * @return int
     */
    public static function getTransactionsCountPerUser(int $userId, int $interval = null): int
    {
        $query = TransactionModel::whereUserId($userId)
            ->where('status', '<>', TransactionService::STATUS_ERROR);

        if (!empty($interval)) {
            $query->whereRaw('created_at >= DATE_SUB(NOW(), INTERVAL ' . $interval . ' MINUTE)');
        }

        return $query->count();
    }

    /**
     * @param array $input
     * @return int|null
     */
    public static function insertTransaction(array $input): ?int
    {
        try {
            return TransactionModel::insertGetId($input);
        } catch (Exception $exception) {
            return null;
        }
    }

    /**
     * @param int $transactionId
     * @param int $userId
     * @param string $status
     * @return bool
     */
    public static function updateTransactionStatus(
        int $transactionId,
        int $userId,
        string $status
    ): bool {
        return TransactionModel::whereId($transactionId)
            ->where('user_id', $userId)
            ->whereIn('status', self::ALLOWED_STATUS_CHANGING[$status])
            ->limit(1)
            ->update(['status' => $status]);
    }

    /**
     * @param int $transactionId
     * @param array $update
     */
    public static function updateTransaction(int $transactionId, array $update)
    {
        TransactionModel::whereId($transactionId)
            ->update($update);
    }

    public static function getTransaction($transactionId): TransactionEntity
    {
        /** @var TransactionModel $transaction */
        $transaction = TransactionModel::whereId($transactionId)
            ->first();

        return new TransactionEntity(
            $transaction->id,
            $transaction->user_id,
            $transaction->details,
            $transaction->receiver_account,
            $transaction->receiver_name,
            $transaction->amount,
            $transaction->fee,
            $transaction->currency,
            $transaction->status,
            $transaction->provider_id,
            $transaction->provider_trn_id,
            ErrorCodeRepository::getError($transaction->error_code)->code,
            ErrorCodeRepository::getError($transaction->error_code)->message
        );
    }

    /**
     * @return TransactionEntity[]
     */
    public static function getApprovedTransactions(): array
    {
        /** @var TransactionModel[] $transaction */
        $query = TransactionModel::whereStatus(TransactionService::STATUS_APPROVED)
            ->get();

        if (count($query) === 0) {
            return [];
        }

        /** @var TransactionEntity[] $transactions */
        $transactions = [];

        foreach ($query as $transaction) {
            $transactions[] = new TransactionEntity(
                $transaction->id,
                $transaction->user_id,
                $transaction->details,
                $transaction->receiver_account,
                $transaction->receiver_name,
                $transaction->amount,
                $transaction->fee,
                $transaction->currency,
                $transaction->status,
                $transaction->provider_id,
                $transaction->provider_trn_id,
                ErrorCodeRepository::getError($transaction->error_code)->code,
                ErrorCodeRepository::getError($transaction->error_code)->message
            );
        }

        return $transactions;
    }
}
