<?php

namespace App\Services;

use App\Models\ErrorCodeModel;
use App\Models\TransactionModel;
use App\Repositories\ErrorCodeRepository;
use App\Repositories\ProviderRepository;
use App\Repositories\TransactionRepository;
use Illuminate\Support\Facades\Validator;

/**
 * Class TransactionService
 * @package App\Services
 */
class TransactionService
{
    const STATUS_RECEIVED   = 'received';   // Transaction received from user
    const STATUS_CONFIRMED  = 'confirmed';  // Transaction confirmed by user
    const STATUS_SUBMITTED  = 'submitted';  // Transaction submitted to provider
    const STATUS_COMPLETED  = 'completed';  // Transaction completed
    const STATUS_ERROR      = 'error';      // Transaction terminate with error message

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

    private ProviderService $providerService;
    private TransactionRepository $transactionRepository;
    private ErrorCodeRepository $errorCodeRepository;
    private ProviderRepository $providerRepository;

    /**
     * TransactionService constructor.
     * @param ProviderService $providerService
     * @param TransactionRepository $transactionRepository
     * @param ErrorCodeRepository $errorCodeRepository
     * @param ProviderRepository $providerRepository
     */
    public function __construct(
        ProviderService $providerService,
        TransactionRepository $transactionRepository,
        ErrorCodeRepository $errorCodeRepository,
        ProviderRepository $providerRepository
    ) {
        $this->providerService = $providerService;
        $this->transactionRepository = $transactionRepository;
        $this->errorCodeRepository = $errorCodeRepository;
        $this->providerRepository = $providerRepository;
    }

    /**
     * @param array $input
     * @return TransactionModel
     */
    public function saveTransaction(array $input): TransactionModel
    {
        /** @var TransactionModel $transaction */
        $transaction = new TransactionModel();

        $transaction->user_id = $input['user_id'];
        $transaction->details = $input['details'];
        $transaction->receiver_account = $input['receiver_account'];
        $transaction->receiver_name = $input['receiver_name'];
        $transaction->amount = $input['amount'];
        $transaction->currency = $input['currency'];
        $transaction->provider_id = $this->findProvider($transaction);
        $transaction->fee = $this->countFee($transaction);
        $transaction->error_code = $this->checkForErrors($transaction);
        $transaction->status = empty($transaction->error_code) ? self::STATUS_RECEIVED : self::STATUS_ERROR;

        $transaction->save();

        return $transaction;
    }

    /**
     * @param array $transaction
     * @return bool
     */
    public function transactionIsValid(array $transaction): bool
    {
        /** @var array $transactionValidation */
        $transactionValidation = [
            'user_id'           => 'required|integer|min:1',
            'details'           => 'required|string|min:1|max:255',
            'receiver_account'  => 'required|alpha_num|min:20|max:34',
            'receiver_name'     => 'required|string|min:1|max:255',
            'amount'            => 'required|numeric|min:0.01',
            'currency'          => 'required|alpha|min:3|max:3',
        ];

        $validate = Validator::make($transaction, $transactionValidation);

        return $validate->passes();
    }

    /**
     * @param TransactionModel $transaction
     * @return array
     */
    public function createTransactionResponse(TransactionModel $transaction): array
    {
        /** @var ErrorCodeModel $error */
        $error = $this->errorCodeRepository->getError($transaction->error_code);

        /** @var array $response */
        $response = [
            'transaction_id'    => $transaction->id,
            'details'           => $transaction->details,
            'receiver_account'  => $transaction->receiver_account,
            'receiver_name'     => $transaction->receiver_name,
            'amount'            => $transaction->amount,
            'fee'               => $transaction->fee,
            'currency'          => $transaction->currency,
            'status'            => $transaction->status,
            'error_code'        => $error->error_code,
            'error_message'     => $error->error_message
        ];

        return $response;
    }

    public function processConfirmedTransactions()
    {
        /** @var TransactionModel[] $approvedTransactions */
        $approvedTransactions = $this->transactionRepository->getConfirmedTransactions();

        if(count($approvedTransactions) === 0) {
            return;
        }

        foreach ($approvedTransactions as $transaction) {
            $this->transactionRepository->updateTransactionStatus(
                $transaction->id,
                self::STATUS_SUBMITTED
            );

            /** @var string|null $providerTransactionId */
            $providerTransactionId = $this->providerService->processTransaction($transaction);

            if (empty($providerTransactionId)) {
                $this->transactionRepository->updateTransaction($transaction->id, [
                    'error_code' => self::ERROR_PROVIDER_NOT_FOUND,
                    'status' => self::STATUS_ERROR
                ]);

                continue;
            }

            $this->transactionRepository->updateTransaction($transaction->id, [
                'provider_trn_id' => $providerTransactionId,
                'status' => self::STATUS_COMPLETED
            ]);
        }
    }

    /**
     * @param TransactionModel $transaction
     * @return int|null
     */
    private function findProvider(TransactionModel $transaction): ?int
    {
        switch (strtolower($transaction->currency)) {
            case 'eur':
                $provider = $this->providerRepository->getProviderByKey(
                    ProviderService::EUR_PROVIDER,
                    ProviderService::STATUS_ACTIVE
                );
                break;
            default:
                $provider = $this->providerRepository->getProviderByKey(
                    ProviderService::NON_EUR_PROVIDER,
                    ProviderService::STATUS_ACTIVE
                );
        }

        return $provider->id ?? null;
    }

    /**
     * @param TransactionModel $transaction
     * @return float
     */
    private function countFee(TransactionModel $transaction): float
    {
        /** @var float $dailyAmountSum */
        $dailyAmountSum = $this->transactionRepository->getTransactionsAmountPerUser(
            $transaction->user_id,
            self::TRN_FEE_PERIOD
        );

        /** @var float $feePercentage */
        $feePercentage = ($dailyAmountSum < self::TRN_FEE_1_SWITCH) ? self::TRN_FEE_1 : self::TRN_FEE_2;

        /** @var float $fee */
        $fee = (float) number_format($transaction->amount * $feePercentage, 2, '.', ',');

        return $fee;
    }

    /**
     * @param TransactionModel $transaction
     * @return string|null
     */
    private function checkForErrors(TransactionModel $transaction): ?string
    {
        if (empty($transaction->provider_id)) {
            return $this->errorCodeRepository->getError(self::ERROR_PROVIDER_NOT_FOUND)->error_code;
        }

        if ($this->totalAmountLimitReached($transaction)) {
            return $this->errorCodeRepository->getError(self::ERROR_TOTAL_LIMIT)->error_code;
        }

        if ($this->hourlyLimitReached($transaction->user_id)) {
            return $this->errorCodeRepository->getError(self::ERROR_HOUR_LIMIT)->error_code;
        }

        return null;
    }

    /**
     * @param TransactionModel $transaction
     * @return bool
     */
    private function totalAmountLimitReached(TransactionModel $transaction): bool
    {
        /** @var float $totalAmount */
        $totalAmount = $this->transactionRepository->getTransactionsAmountPerUser(
            $transaction->user_id,
            null,
            $transaction->currency
        );

        /** @var float $totalAmountSum */
        $totalAmountSum = $totalAmount + $transaction->amount + $transaction->fee;

        return $totalAmountSum > self::TRN_TOTAL_LIMIT;
    }

    /**
     * @param int $userId
     * @return bool
     */
    private function hourlyLimitReached(int $userId): bool
    {
        /** @var float $totalTransactionsCount */
        $lastHourTransactionsCount = $this->transactionRepository->getTransactionsCountPerUser(
            $userId,
            self::TRN_TIME_LIMIT
        );

        return $lastHourTransactionsCount >= self::TRN_MAX;
    }
}
