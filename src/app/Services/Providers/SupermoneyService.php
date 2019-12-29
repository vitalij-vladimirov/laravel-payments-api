<?php

namespace App\Services\Providers;

use App\Models\TransactionModel;

/**
 * Class SupermoneyService
 * @package App\Services\Providers
 */
class SupermoneyService implements ProviderInterface
{
    /**
     * @param TransactionModel $transaction
     * @return string
     */
    public function processTransaction(TransactionModel $transaction): string
    {
        /** @var int $currentMaxId */
        $currentMaxId = (int) TransactionModel::max('provider_trn_id') ?? 0;

        /** @var int $trnId */
        $trnId = (string) rand($currentMaxId + 1, $currentMaxId + 100);

        return $trnId;
    }
}
