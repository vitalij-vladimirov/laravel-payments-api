<?php

namespace App\Services;

/**
 * Class AuthenticationService
 * @package App\Services
 */
class AuthenticationService
{
    const AUTH_CODE = 111;

    /**
     * This method is imitating 2FA check
     *
     * @param int $code
     * @return bool
     */
    public function authenticateTransaction(int $code): bool
    {
        if ($code === self::AUTH_CODE) {
            return true;
        }

        return false;
    }
}
