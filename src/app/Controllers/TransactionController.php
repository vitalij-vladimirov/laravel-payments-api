<?php

namespace App\Controllers;

use App\Models\TransactionModel;
use App\Repositories\ErrorCodeRepository;
use App\Repositories\TransactionRepository;
use App\Services\AuthenticationService;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Class TransactionController
 * @package App\Controllers
 */
class TransactionController extends Controller
{
    private Request $request;
    private TransactionService $transactionService;
    private AuthenticationService $authenticationService;
    private TransactionRepository $transactionRepository;
    private ErrorCodeRepository $errorCodeRepository;

    /**
     * TransactionController constructor.
     * @param Request $request
     * @param TransactionService $transactionService
     * @param AuthenticationService $authenticationService
     * @param TransactionRepository $transactionRepository
     * @param ErrorCodeRepository $errorCodeRepository
     */
    public function __construct(
        Request $request,
        TransactionService $transactionService,
        AuthenticationService $authenticationService,
        TransactionRepository $transactionRepository,
        ErrorCodeRepository $errorCodeRepository
    ) {
        $this->request = $request;
        $this->transactionService = $transactionService;
        $this->authenticationService = $authenticationService;
        $this->transactionRepository = $transactionRepository;
        $this->errorCodeRepository = $errorCodeRepository;
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
            return response()->json($this->errorCodeRepository->getError(TransactionService::ERROR_BAD_INPUT));
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
    public function confirmTransaction(int $transactionId): JsonResponse
    {
        /** @var int $code */
        $code = $this->request->post('code');

        if (!$this->authenticationService->authenticateTransaction($code)) {
            // Return error on bad authentication
            return response()->json($this->errorCodeRepository->getError(TransactionService::ERROR_BAD_AUTHENTICATION));
        }

        /** @var bool $update */
        $update = $this->transactionRepository->updateTransactionStatus(
            $transactionId,
            TransactionService::STATUS_CONFIRMED
        );

        if (!$update) {
            // Return error if transaction status was not updated
            return response()->json($this->errorCodeRepository->getError(TransactionService::ERROR_NOT_FOUND));
        }

        /** @var TransactionModel $transaction */
        $transaction = $this->transactionRepository->getTransaction($transactionId);

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
        $transaction = $this->transactionRepository->getTransaction($transactionId);

        if (empty($transaction)) {
            return response()->json($this->errorCodeRepository->getError(TransactionService::ERROR_NOT_FOUND));
        }

        /** @var array $output */
        $output = $this->transactionService->createTransactionResponse($transaction);

        return response()->json($output);
    }
}
