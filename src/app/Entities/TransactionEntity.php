<?php

namespace App\Entities;

/**
 * Class TransactionEntity
 * @package App\Entities
 */
class TransactionEntity
{
    public ?int $id;
    public int $userId;
    public string $details;
    public string $receiverAccount;
    public string $receiverName;
    public float $amount;
    public float $fee;
    public string $currency;
    public ?int $providerId;
    public ?string $providerTrnId;
    public string $status;
    public ?string $errorCode;
    public ?string $errorMessage;

    /**
     * TransactionEntity constructor.
     * @param int|null $id
     * @param int $userId
     * @param string $details
     * @param string $receiverAccount
     * @param string $receiverName
     * @param float $amount
     * @param float $fee
     * @param string $currency
     * @param string $status
     * @param int|null $providerId
     * @param string|null $providerTrnId
     * @param string|null $errorCode
     * @param string|null $errorMessage
     */
    public function __construct(
        ?int $id,
        int $userId,
        string $details,
        string $receiverAccount,
        string $receiverName,
        float $amount,
        float $fee,
        string $currency,
        string $status,
        int $providerId = null,
        string $providerTrnId = null,
        string $errorCode = null,
        string $errorMessage = null
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
        $this->providerId = $providerId;
        $this->providerTrnId = $providerTrnId;
        $this->errorCode = $errorCode;
        $this->errorMessage = $errorMessage;
    }
}
