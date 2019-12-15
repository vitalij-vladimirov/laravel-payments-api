<?php

namespace App\Helpers;

use App\Entities\TransactionEntity;

/**
 * Class TransactionHelper
 * @package App\Helpers
 */
class TransactionHelper
{
    /**
     * @param TransactionEntity $transaction
     * @return array
     */
    public static function convertEntityToInsert(TransactionEntity $transaction): array
    {
        /** @var array $ignore */
        $ignore = ['id', 'error_message'];

        return Converters::convertEntityToArray($transaction, $ignore);
    }
    /**
     * @param TransactionEntity $transaction
     * @return array
     */
    public static function convertEntityToResponse(TransactionEntity $transaction): array
    {
        /** @var array $ignore */
        $ignore = ['user_id', 'provider_id', 'provider_trn_id'];

        /** @var array $convertedTransaction */
        $convertedTransaction = Converters::convertEntityToArray($transaction, $ignore);

        /** @var array $output */
        $output = [];

        foreach ($convertedTransaction as $key => $value) {
            if ($key === 'id') {
                $key = 'transaction_id';
            }

            if ($key === 'status') {
                $value = strtolower($value);
            }

            $output[$key] = $value;
        }

        return $output;
    }
}
