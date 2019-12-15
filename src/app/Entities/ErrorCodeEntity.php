<?php

namespace App\Entities;

/**
 * Class ErrorCodeEntity
 * @package App\Entities
 */
class ErrorCodeEntity
{
    public ?string $code;
    public ?string $message;

    /**
     * ErrorCodeEntity constructor.
     * @param string|null $code
     * @param string|null $message
     */
    public function __construct(string $code = null, string $message = null)
    {
        $this->code = $code;
        $this->message = $message;
    }
}
