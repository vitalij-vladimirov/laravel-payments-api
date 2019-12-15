<?php

namespace App\Entities;

/**
 * Class TransactionEntity
 * @package App\Entities
 */
class TransactionEntity
{
    public int $id;
    public int $userId;
    public string $details;
    public string $receiverAccount;
    public string $receiverName;
    public float $amount;
    public float $fee;
    public string $currency;
    public string $status;
    public ?string $errorCode;
    public ?string $errorMessage;

    /**
     * TransactionEntity constructor.
     * @param int $id
     * @param int $userId
     * @param string $details
     * @param string $receiverAccount
     * @param string $receiverName
     * @param float $amount
     * @param float $fee
     * @param string $currency
     * @param string $status
     * @param string|null $errorCode
     * @param string|null $errorMessage
     */
    public function __construct(
        int $id,
        int $userId,
        string $details,
        string $receiverAccount,
        string $receiverName,
        float $amount,
        float $fee,
        string $currency,
        string $status,
        ?string $errorCode,
        ?string $errorMessage
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->details = $details;
        $this->receiverAccount = $receiverAccount;
        $this->receiverName = $receiverName;
        $this->amount = $amount;
        $this->fee = $fee;
        $this->currency = $currency;
        $this->status = $status;
        $this->errorCode = $errorCode;
        $this->errorMessage = $errorMessage;
    }
}
