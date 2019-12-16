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

    /**
     * TransactionController constructor.
     */
    public function __construct()
    {
        $this->transactionService = new TransactionService();
        $this->providerService = new ProviderService();
    }

    /**
     * @param Request $input
     * @return JsonResponse
     */
    public function setTransaction(Request $input): JsonResponse
    {
        try {
            // Set input data to Entity
            $transactionInput = new TransactionInputEntity($input->post());
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

    public function submitTransaction(Request $input): JsonResponse
    {
        /** @var int $userId */
        $userId = $input->post('user_id');

        /** @var int $transactionId */
        $transactionId = $input->post('transaction_id');

        /** @var int $code */
        $code = $input->post('code');

        if (!AuthenticationService::authenticateTransaction($userId, $code)) {
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
            $userId,
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
