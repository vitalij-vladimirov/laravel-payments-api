<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TransactionModel
 *
 * @package App\Models
 * @property int $id
 * @property int $user_id
 * @property string $details
 * @property string $receiver_account
 * @property string $receiver_name
 * @property float $amount
 * @property float $fee
 * @property string $currency
 * @property int|null $provider_id
 * @property string|null $provider_trn_id Provider responded transaction ID.
 * @property string $status
 * @property string|null $error_code In case of error only.
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TransactionModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TransactionModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TransactionModel query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TransactionModel whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TransactionModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TransactionModel whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TransactionModel whereDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TransactionModel whereErrorCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TransactionModel whereFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TransactionModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TransactionModel whereProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TransactionModel whereProviderTrnId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TransactionModel whereReceiverAccount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TransactionModel whereReceiverName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TransactionModel whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TransactionModel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TransactionModel whereUserId($value)
 * @mixin \Eloquent
 */
class TransactionModel extends Model
{
    /**
     * @var string
     */
    protected $table = 'transaction';

    /**
     * @var bool
     */
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'details',
        'receiver_account',
        'receiver_name',
        'amount',
        'fee',
        'currency',
        'provider_id',
        'provider_trn_id',
        'status',
        'error_code',
    ];
}
