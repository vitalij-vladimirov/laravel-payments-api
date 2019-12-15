<?php

namespace App\Helpers;

use App\Entities\TransactionEntity;
use Illuminate\Support\Str;

/**
 * Class Converters
 * @package App\Helpers
 */
class Converters
{
    /**
     * @param TransactionEntity $input
     * @param array $ignore - remove $ignored fields
     * @return array
     */
    public static function convertEntityToArray(TransactionEntity $input, array $ignore = []): array
    {
        /** @var array $output */
        $output = [];

        foreach ($input as $key => $value) {
            /** @var string $newKey */
            $newKey = Str::snake($key);

            if (!in_array($key, $ignore) && !in_array($newKey, $ignore)) {
                $output[$newKey] = $value;
            }
        }

        return $output;
    }
}
