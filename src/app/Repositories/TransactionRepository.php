<?php

namespace App\Repositories;

use App\Models\TransactionModel;
use App\Services\TransactionService;

/**
 * Class TransactionRepository
 * @package App\Repositories
 */
class TransactionRepository
{
    const ALLOWED_STATUS_CHANGING = [
        TransactionService::STATUS_CONFIRMED   => [
            TransactionService::STATUS_RECEIVED,
        ],
        TransactionService::STATUS_SUBMITTED  => [
            TransactionService::STATUS_CONFIRMED,
        ],
        TransactionService::STATUS_COMPLETED  => [
            TransactionService::STATUS_SUBMITTED,
        ],
        TransactionService::STATUS_ERROR  => [
            TransactionService::STATUS_RECEIVED,
            TransactionService::STATUS_CONFIRMED,
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

        $totalAmount = $query->selectRaw('sum(amount + fee) as sum')
            ->first();

        return $totalAmount->sum ?? 0;
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
     * @param int $transactionId
     * @param string $status
     * @return bool
     */
    public static function updateTransactionStatus(int $transactionId, string $status): bool
    {
        return TransactionModel::whereId($transactionId)
            ->whereIn('status', self::ALLOWED_STATUS_CHANGING[$status])
            ->update(['status' => $status]);
    }

    /**
     * @param int $transactionId
     * @param array $update
     * @return bool
     */
    public static function updateTransaction(int $transactionId, array $update): bool
    {
        if (isset($update['status'])) {
            return false;
        }

        return TransactionModel::whereId($transactionId)
            ->update($update);
    }

    /**
     * @param $transactionId
     * @return TransactionModel|null
     */
    public static function getTransaction($transactionId): ?TransactionModel
    {
        /** @var TransactionModel $transaction */
        $transaction = TransactionModel::whereId($transactionId)
            ->first();

        return $transaction;
    }

    /**
     * @return TransactionModel[]
     */
    public static function getApprovedTransactions(): array
    {
        /** @var TransactionModel[] $transactions */
        $transactions = TransactionModel::whereStatus(TransactionService::STATUS_CONFIRMED)
            ->get();

        return $transactions;
    }
}
