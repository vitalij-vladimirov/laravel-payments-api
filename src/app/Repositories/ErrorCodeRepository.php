<?php

namespace App\Repositories;

use App\Models\ErrorCodeModel;
use App\Services\TransactionService;

/**
 * Class ErrorCodeRepository
 * @package App\Repositories
 */
class ErrorCodeRepository
{
    /**
     * @param string|null $code
     * @param string|null $status
     * @return ErrorCodeModel
     */
    public function getError(?string $code, string $status = null): ErrorCodeModel
    {
        if (empty($code) && $status !== TransactionService::STATUS_ERROR) {
            return new ErrorCodeModel();
        }

        /** @var ErrorCodeModel $error */
        $error = ErrorCodeModel::whereErrorCode($code)
            ->first();

        if (empty($error->error_message)) {
            $error->error_code = 'unknown';
            $error->error_message = 'Unknown error';
        }

        return $error;
    }
}
