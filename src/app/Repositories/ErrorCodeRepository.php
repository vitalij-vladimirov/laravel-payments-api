<?php

namespace App\Repositories;

use App\Entities\ErrorCodeEntity;
use App\Models\ErrorCodeModel;

/**
 * Class ErrorCodeRepository
 * @package App\Repositories
 */
class ErrorCodeRepository
{
    /**
     * @param string|null $code
     * @return ErrorCodeEntity
     */
    public static function getError(?string $code): ErrorCodeEntity
    {
        if (empty($code)) {
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
