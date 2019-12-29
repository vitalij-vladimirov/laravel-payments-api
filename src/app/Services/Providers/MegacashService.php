<?php

namespace App\Services\Providers;

use App\Models\TransactionModel;
use Illuminate\Support\Str;

/**
 * Class MegacashService
 * @package App\Services\Providers
 */
class MegacashService implements ProviderInterface
{
    /**
     * @param TransactionModel $transaction
     * @return string
     */
    public function processTransaction(TransactionModel $transaction): string
    {
        /** @var string $trnId */
        $trnId = Str::random(20);

        return $trnId;
    }
}
