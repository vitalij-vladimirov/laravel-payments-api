<?php

namespace App\Repositories;

use App\Models\ProviderModel;

/**
 * Class ProviderRepository
 * @package App\Repositories
 */
class ProviderRepository
{
    /**
     * @param string $key
     * @param string|null $status
     * @return ProviderModel
     */
    public function getProviderByKey(string $key, string $status = null): ProviderModel
    {
        $query = ProviderModel::whereProviderKey($key);

        if (!empty($status)) {
            $query->where('status', $status);
        }

        return $query->first();
    }

    /**
     * @param int $id
     * @param string|null $status
     * @return ProviderModel
     */
    public function getProviderById(int $id, string $status = null): ProviderModel
    {
        /** @var ProviderModel $query */
        $query = ProviderModel::whereId($id);

        if (!empty($status)) {
            $query->where('status', $status);
        }

        return $query->first();
    }
}
