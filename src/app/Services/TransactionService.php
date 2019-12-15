<?php

namespace App\Services;

/**
 * Class TransactionService
 * @package App\Services
 */
class TransactionService
{
    const STATUS_RECEIVED   = 'RECEIVED';   // Transaction received from user
    const STATUS_APPROVED   = 'APPROVED';   // Transaction approved by user
    const STATUS_SUBMITTED  = 'SUBMITTED';  // Transaction submitted to provider
    const STATUS_COMPLETED  = 'COMPLETED';  // Transaction completed
    const STATUS_ERROR      = 'ERROR';      // Transaction terminate with error message
}
