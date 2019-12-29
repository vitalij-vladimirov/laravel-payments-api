<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ErrorCodeModel
 *
 * @package App\Models
 * @property string $error_code
 * @property string $error_message
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ErrorCodeModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ErrorCodeModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ErrorCodeModel query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ErrorCodeModel whereErrorCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ErrorCodeModel whereErrorMessage($value)
 * @mixin \Eloquent
 */
class ErrorCodeModel extends Model
{
    /**
     * @var string
     */
    protected $table = 'error_code';

    /**
     * @var bool
     */
    public $timestamps = false;
}
