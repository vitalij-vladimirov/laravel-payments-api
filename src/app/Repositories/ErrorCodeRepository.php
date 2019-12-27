<?php

namespace App\Repositories;

use App\Entities\ErrorCodeEntity;
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
     * @return ErrorCodeEntity
     */
    public static function getError(?string $code, string $status = null): ErrorCodeEntity
    {
        if (empty($code) && $status !== TransactionService::STATUS_ERROR) {
            return new ErrorCodeEntity();
        }

        /** @var ErrorCodeModel $error */
        $error = ErrorCodeModel::whereCode($code)
            ->first();

        if (empty($error->message)) {
            return new ErrorCodeEntity(
                'unknown',
                'Unknown error'
            );
        }

        return new ErrorCodeEntity(
            $error->code,
            $error->message
        );
    }
}
