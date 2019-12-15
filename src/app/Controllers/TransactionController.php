<?php

namespace App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Class TransactionController
 * @package App\Controllers
 */
class TransactionController extends Controller
{
    /**
     * @param Request $input
     * @return JsonResponse
     */
    public function setTransaction(Request $input): JsonResponse
    {
        return response()->json([]);
    }

    public function submitTransaction(): JsonResponse
    {
        return response()->json([]);
    }

    /**
     * @param Request $input
     * @return JsonResponse
     */
    public function getTransaction(Request $input): JsonResponse
    {
        return response()->json([]);
    }
}
