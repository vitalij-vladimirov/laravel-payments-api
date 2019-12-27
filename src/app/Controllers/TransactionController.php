<?php

namespace App\Controllers;

use App\Entities\TransactionInputEntity;
use App\Entities\TransactionEntity;
use App\Helpers\TransactionHelper;
use App\Models\TransactionModel;
use App\Repositories\ErrorCodeRepository;
use App\Repositories\TransactionRepository;
use App\Services\AuthenticationService;
use App\Services\ProviderService;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

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
            return response()->json([
                    'error' => ErrorCodeRepository::getError(TransactionService::ERROR_BAD_INPUT)
                        ->message
                ])
                ->setStatusCode(400);
        }

        /** @var TransactionModel $transaction */
        $transaction = $this->transactionService->fillModel($input);

        $transaction->provider_id = $this->providerService->findProvider($transaction);
        $transaction->fee = $this->transactionService->getFee($transaction->user_id, $transaction->amount);
        $transaction->amount += $transaction->fee;
        $transaction->error_code = $this->transactionService->checkForErrors($transaction);

        $transaction->save();

        /** @var array $output */
        $output = TransactionHelper::convertEntityToResponse($transaction);

        return response()->json($transaction);
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
            return response()->json([
                    'error' => ErrorCodeRepository::getError(TransactionService::ERROR_BAD_AUTHENTICATION)
                        ->message
                ])
                ->setStatusCode(400);
        }

        /** @var bool $update */
        $update = TransactionRepository::updateTransactionStatus(
            $transactionId,
            TransactionService::STATUS_APPROVED
        );

        if (!$update) {
            // Return error if transaction status was not updated
            return response()->json([
                    'error' => ErrorCodeRepository::getError(TransactionService::ERROR_NOT_FOUND)
                        ->message
                ])
                ->setStatusCode(404);
        }

        /** @var TransactionEntity $transaction */
        $transaction = TransactionRepository::getTransaction($transactionId);

        /** @var array $output */
        $output = TransactionHelper::convertEntityToResponse($transaction);

        return response()->json($output);
    }

    /**
     * @param int $transactionId
     * @return JsonResponse
     */
    public function getTransaction(int $transactionId): JsonResponse
    {
        try {
            /** @var TransactionEntity $transaction */
            $transaction = TransactionRepository::getTransaction($transactionId);
        } catch (Exception $exception) {
            // Return error if transaction status was not updated
            return response()->json([
                    'error' => ErrorCodeRepository::getError(TransactionService::ERROR_NOT_FOUND)
                        ->message
                ])
                ->setStatusCode(404);
        }

        /** @var array $output */
        $output = TransactionHelper::convertEntityToResponse($transaction);

        return response()->json($output);
    }
}
