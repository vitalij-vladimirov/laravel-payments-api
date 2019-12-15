<?php

namespace App\Entities;

/**
 * Class ProviderEntity
 * @package App\Entities
 */
class ProviderEntity
{
    public int $id;
    public string $providerKey;
    public string $title;
    public string $status;

    /**
     * ProviderEntity constructor.
     * @param int $id
     * @param string $providerKey
     * @param string $title
     * @param string $status
     */
    public function __construct(int $id, string $providerKey, string $title, string $status)
    {
        $this->id = $id;
        $this->providerKey = $providerKey;
        $this->title = $title;
        $this->status = $status;
    }
}
