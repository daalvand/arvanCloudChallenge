<?php

namespace App\Models;

use Database\Factories\TransactionFactory;
use DateTimeInterface;
use Eloquent;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Carbon;

/**
 * App\Models\Transaction
 *
 * @property User        $user
 * @property int         $id
 * @property int         $user_id
 * @property int         $amount
 * @property string      $type
 * @property bool        $confirmed
 * @property array|null  $meta
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string $amount_with_sign
 * @method static TransactionFactory factory(...$parameters)
 * @method static Builder|Transaction newModelQuery()
 * @method static Builder|Transaction newQuery()
 * @method static Builder|Transaction query()
 * @method static Builder|Transaction whereAmount($value)
 * @method static Builder|Transaction whereConfirmed($value)
 * @method static Builder|Transaction whereCreatedAt($value)
 * @method static Builder|Transaction whereId($value)
 * @method static Builder|Transaction whereMeta($value)
 * @method static Builder|Transaction whereType($value)
 * @method static Builder|Transaction whereUpdatedAt($value)
 * @method static Builder|Transaction whereUuid($value)
 * @method static Builder|Transaction whereWalletId($value)
 * @method static Builder|Transaction whereUserId($value)
 * @method static Builder|Transaction visible(User $user)
 * @mixin Eloquent
 */
class Transaction extends Model
{
    use HasFactory;

    const TYPES = [self::DEPOSIT_TYPE, self::WITHDRAW_TYPE];

    const DEPOSIT_TYPE  = 'deposit';
    const WITHDRAW_TYPE = 'withdraw';

    protected $fillable = [
        'user_id', 'amount', 'type', 'confirmed', 'meta'
    ];

    protected $casts = [
        'meta'      => 'json',
        'confirmed' => 'boolean',
        'user_id'   => 'integer',
    ];

    /**
     * Retrieve the user from this transaction
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Retrieve the amount with the positive or negative sign
     */
    public function getAmountWithSignAttribute(): string
    {
        return ($this->type === 'deposit' ? '+' : '-') . $this->amount;
    }

    public function scopeVisible(Builder|QueryBuilder $query, User $user)
    {
        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
