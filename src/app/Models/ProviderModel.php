<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ProviderModel
 *
 * @package App\Models
 * @property int $id
 * @property string $provider_key
 * @property string $title
 * @property string $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProviderModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProviderModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProviderModel query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProviderModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProviderModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProviderModel whereProviderKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProviderModel whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProviderModel whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProviderModel whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ProviderModel extends Model
{
    /**
     * @var string
     */
    protected $table = 'provider';

    /**
     * @var bool
     */
    public $timestamps = true;
}
