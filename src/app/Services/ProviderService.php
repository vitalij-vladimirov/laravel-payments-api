<?php

namespace App\Services;

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

    private ProviderRepository $providerRepository;

    /**
     * ProviderService constructor.
     * @param ProviderRepository $providerRepository
     */
    public function __construct(ProviderRepository $providerRepository)
    {
        $this->providerRepository = $providerRepository;
    }

    /**
     * @param TransactionModel $transaction
     * @return string|null
     */
    public function processTransaction(TransactionModel $transaction): ?string
    {
        /** @var ProviderModel $provider */
        $provider = $this->providerRepository->getProviderById($transaction->provider_id);

        if ($provider->status !== self::STATUS_ACTIVE) {
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

    /**
     * @param TransactionModel $transaction
     * @return string
     */
    private function processTransactionToMegacash(TransactionModel $transaction): string
    {
        /** @var string $trnId */
        $trnId = Str::random(20);

        return $trnId;
    }

    /**
     * @param TransactionModel $transaction
     * @return int
     */
    private function processTransactionToSupermoney(TransactionModel $transaction): int
    {
        /** @var int $currentMaxId */
        $currentMaxId = (int) TransactionModel::max('provider_trn_id') ?? 0;

        /** @var int $trnId */
        $trnId = rand($currentMaxId + 1, $currentMaxId + 100);

        return $trnId;
    }
}
