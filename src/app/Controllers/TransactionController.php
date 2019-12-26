<?php

namespace App\Controllers;

use App\Entities\TransactionInputEntity;
use App\Entities\TransactionEntity;
use App\Helpers\TransactionHelper;
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
    public function setTransaction(): JsonResponse
    {
        try {
            // Set input data to Entity
            $transactionInput = new TransactionInputEntity($this->request->post());
        } catch (Exception $exception) {
            // Return error if input data is bad
            return response()->json([
                    'error' => ErrorCodeRepository::getError(TransactionService::ERROR_BAD_INPUT)
                        ->message
                ])
                ->setStatusCode(400);
        }

        /** @var int|null $provider */
        $provider = $this->providerService->findProvider($transactionInput);

        /** @var TransactionEntity $transaction */
        $transaction = $this->transactionService->setTransaction($transactionInput, $provider);

        /** @var array $insert */
        $insert = TransactionHelper::convertEntityToInsert($transaction);

        $transaction->id = TransactionRepository::insertTransaction($insert);

        if (empty($transaction->id)) {
            // Return error if data could not be saved to DB
            return response()->json([
                    'error' => ErrorCodeRepository::getError(TransactionService::ERROR_BAD_INPUT)
                        ->message
                ])
                ->setStatusCode(400);
        }

        /** @var array $output */
        $output = TransactionHelper::convertEntityToResponse($transaction);

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
