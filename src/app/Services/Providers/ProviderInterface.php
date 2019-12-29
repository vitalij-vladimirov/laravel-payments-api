<?php

namespace App\Services\Providers;

use App\Models\TransactionModel;

interface ProviderInterface
{
    const STATUS_ACTIVE     = 'active';
    const STATUS_DISABLED   = 'disabled';

    const EUR_PROVIDER      = 'Megacash';
    const NON_EUR_PROVIDER  = 'Supermoney';

    public function processTransaction(TransactionModel $transaction): string;
}
