<?php

namespace App\Entities;

/**
 * Class TransactionInputEntity
 * @package App\Entities
 */
class TransactionInputEntity
{
    public int $userId;
    public string $details;
    public string $receiverAccount;
    public string $receiverName;
    public float $amount;
    public string $currency;

    /**
     * TransactionInputEntity constructor.
     * @param array $input
     */
    public function __construct(array $input) {
        $this->userId = $input['user_id'];
        $this->details = $input['details'];
        $this->receiverAccount = $input['receiver_account'];
        $this->receiverName = $input['receiver_name'];
        $this->amount = $input['amount'];
        $this->currency = $input['currency'];
    }
}
