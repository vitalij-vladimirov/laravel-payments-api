<?php

namespace App\Services;

use App\Entities\TransactionEntity;
use App\Entities\TransactionInputEntity;
use App\Models\ProviderModel;
use App\Models\TransactionModel;
use App\Repositories\ProviderRepository;
use Illuminate\Support\Str;

/**
 * Class ProviderService
 * @package App\Services
 */
class ProviderService
{
    const STATUS_ACTIVE     = 'ACTIVE';
    const STATUS_DISABLED   = 'DISABLED';

    const EUR_PROVIDER      = 'megacash';
    const NON_EUR_PROVIDER  = 'supermoney';

    /**
     * @param TransactionModel $transaction
     * @return int|null
     */
    public function findProvider(TransactionModel $transaction): ?int
    {
        switch (strtolower($transaction->currency)) {
            case 'eur':
                $provider = ProviderRepository::getProviderByKey(
                    self::EUR_PROVIDER,
                    self::STATUS_ACTIVE
                );
                break;
            default:
                $provider = ProviderRepository::getProviderByKey(
                    self::NON_EUR_PROVIDER,
                    self::STATUS_ACTIVE
                );
        }

        return $provider->id ?? null;
    }

    /**
     * @param TransactionEntity $transaction
     * @return string|null
     */
    public function processTransaction(TransactionEntity $transaction): ?string
    {
        /** @var ProviderModel $provider */
        $provider = ProviderRepository::getProviderById($transaction->providerId);

        if ($provider->status !== ProviderService::STATUS_ACTIVE) {
            return null;
        }

        /** @var string|null $providerResponse */
        $providerResponse = null;

        switch ($provider->provider_key) {
            case self::EUR_PROVIDER:
                $providerResponse = $this->processTransactionToMegacash($transaction);
                break;
            case self::NON_EUR_PROVIDER:
                $providerResponse = $this->processTransactionToSupermoney($transaction);
                break;
        }

        return (string) $providerResponse;
    }

    private function processTransactionToMegacash(TransactionEntity $transaction): string
    {
        /** @var string $trnId */
        $trnId = Str::random(20);

        return $trnId;
    }

    private function processTransactionToSupermoney(TransactionEntity $transaction): int
    {
        /** @var int $currentMaxId */
        $currentMaxId = (int) TransactionModel::max('provider_trn_id') ?? 0;

        /** @var int $trnId */
        $trnId = rand($currentMaxId + 1, $currentMaxId + 100);

        return $trnId;
    }
}
