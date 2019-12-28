<?php

namespace App\Controllers;

use App\Models\TransactionModel;
use App\Repositories\ErrorCodeRepository;
use App\Repositories\TransactionRepository;
use App\Services\AuthenticationService;
use App\Services\ProviderService;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Class TransactionController
 * @package App\Controllers
 */
class TransactionController extends Controller
{
    private TransactionService $transactionService;
    private ProviderService $providerService;
    private Request $request;

    /**
     * TransactionController constructor.
     * @param Request $request
     * @param TransactionService $transactionService
     * @param ProviderService $providerService
     */
    public function __construct(
        Request $request,
        TransactionService $transactionService,
        ProviderService $providerService
    ) {
        $this->transactionService = $transactionService;
        $this->providerService = $providerService;
        $this->request = $request;
    }

    /**
     * @return JsonResponse
     */
    public function createTransaction(): JsonResponse
    {
        /** @var array $input */
        $input = $this->request->post();

        // Check if transaction data is correct
        if(!$this->transactionService->transactionIsValid($input)) {
            return response()->json(ErrorCodeRepository::getError(TransactionService::ERROR_BAD_INPUT))
                ->setStatusCode(400);
        }

        /** @var TransactionModel $transaction */
        $transaction = $this->transactionService->saveTransaction($input);

        /** @var array $output */
        $output = $this->transactionService->createTransactionResponse($transaction);

        return response()->json($output);
    }

    /**
     * @param int $transactionId
     * @return JsonResponse
     */
    public function submitTransaction(int $transactionId): JsonResponse
    {
        /** @var int $code */
        $code = $this->request->post('code');

        if (!AuthenticationService::authenticateTransaction($code)) {
            // Return error on bad authentication
            return response()->json(ErrorCodeRepository::getError(TransactionService::ERROR_BAD_AUTHENTICATION))
                ->setStatusCode(400);
        }

        /** @var bool $update */
        $update = TransactionRepository::updateTransactionStatus(
            $transactionId,
            TransactionService::STATUS_CONFIRMED
        );

        if (!$update) {
            // Return error if transaction status was not updated
            return response()->json(ErrorCodeRepository::getError(TransactionService::ERROR_NOT_FOUND))
                ->setStatusCode(404);
        }

        /** @var TransactionModel $transaction */
        $transaction = TransactionRepository::getTransaction($transactionId);

        /** @var array $output */
        $output = $this->transactionService->createTransactionResponse($transaction);

        return response()->json($output);
    }

    /**
     * @param int $transactionId
     * @return JsonResponse
     */
    public function getTransaction(int $transactionId): JsonResponse
    {
        /** @var TransactionModel $transaction */
        $transaction = TransactionRepository::getTransaction($transactionId);

        if (empty($transaction)) {
            return response()->json(ErrorCodeRepository::getError(TransactionService::ERROR_NOT_FOUND))
                ->setStatusCode(404);
        }

        /** @var array $output */
        $output = $this->transactionService->createTransactionResponse($transaction);

        return response()->json($output);
    }
}
