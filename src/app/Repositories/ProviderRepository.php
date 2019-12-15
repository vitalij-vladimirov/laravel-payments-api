<?php

namespace App\Repositories;

use App\Entities\ProviderEntity;
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
     * @return ProviderEntity|null
     */
    public static function getProviderByKey(string $key, string $status = null): ?ProviderEntity
    {
        $query = ProviderModel::whereProviderKey($key);

        if (!empty($status)) {
            $query->where('status', $status);
        }

        $provider = $query->first();

        if (empty($provider->id)) {
            return null;
        }

        return new ProviderEntity(
            $provider->id,
            $provider->provider_key,
            $provider->title,
            $provider->status
        );
    }


    /**
     * @param int $id
     * @param string|null $status
     * @return ProviderEntity|null
     */
    public static function getProviderById(int $id, string $status = null): ?ProviderEntity
    {
        /** @var ProviderModel $query */
        $query = ProviderModel::whereId($id);

        if (!empty($status)) {
            $query->where('status', $status);
        }

        $provider = $query->first();

        if (empty($provider->id)) {
            return null;
        }

        return new ProviderEntity(
            $provider->id,
            $provider->provider_key,
            $provider->title,
            $provider->status
        );
    }
}
