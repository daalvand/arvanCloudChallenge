<?php

namespace App\Models;

use Database\Factories\VoucherFactory;
use DateTimeInterface;
use Eloquent;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Voucher
 *
 * @property int         $id
 * @property string      $code
 * @property string      $title
 * @property int         $user_id
 * @property int         $max_uses
 * @property int         $used_count
 * @property string      $type
 * @property int         $amount
 * @property string|null $starts_at
 * @property string|null $expires_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static VoucherFactory factory(...$parameters)
 * @method static Builder|Voucher newModelQuery()
 * @method static Builder|Voucher newQuery()
 * @method static Builder|Voucher query()
 * @method static Builder|Voucher whereCode($value)
 * @method static Builder|Voucher whereCreatedAt($value)
 * @method static Builder|Voucher whereExpiresAt($value)
 * @method static Builder|Voucher whereId($value)
 * @method static Builder|Voucher whereStartsAt($value)
 * @method static Builder|Voucher whereUpdatedAt($value)
 * @method static Builder|Voucher whereAmount($value)
 * @method static Builder|Voucher whereMaxUses($value)
 * @method static Builder|Voucher whereTitle($value)
 * @method static Builder|Voucher whereType($value)
 * @method static Builder|Voucher whereUserId($value)
 * @method static Builder|Voucher whereUses($value)
 * @method static Builder|Transaction visible(User $user)
 * @property-read \App\Models\User $owner
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $redeemedUsers
 * @property-read int|null $redeemed_users_count
 * @method static Builder|Voucher usable()
 * @method static Builder|Voucher usableCharge()
 * @method static Builder|Voucher usableDiscount()
 * @method static Builder|Voucher whereUsedCount($value)
 * @method static Builder|Voucher doesntRelationWith(int $userId)
 * @mixin Eloquent
 */
class Voucher extends Model
{
    use HasFactory, Cachable;

    const TYPES         = [self::CHARGE_TYPE, self::DISCOUNT_TYPE,];
    const CHARGE_TYPE   = 'charge';
    const DISCOUNT_TYPE = 'discount';

    protected $fillable = ['title', 'code', 'type', 'expires_at', 'starts_at', 'amount', 'max_uses',];


    public function scopeVisible(Builder|QueryBuilder $query, User $user)
    {
        if (!$user->isAdmin()) {
            $query->whereNull('id');
        }
    }

    // scope Doesn't Relation With user
    public function scopeDoesntRelationWith(Builder|QueryBuilder $query, int $userId)
    {
        $query->whereDoesntHave('redeemedUsers', function (Builder $query) use ($userId) {
            $query->where('user_id', $userId);
        });
    }

    public function scopeUsable(Builder|QueryBuilder $query): Builder|QueryBuilder
    {
        $now = now()->format("Y-m-d H:i:00");
        return $query
            ->whereColumn('used_count', '<', 'max_uses')
            ->where('starts_at', '<=', $now)
            ->where('expires_at', '>', $now);
    }

    public function scopeUsableCharge(Builder|QueryBuilder $query)
    {
        $this->scopeUsable($query)->where('type', Voucher::CHARGE_TYPE);
    }

    public function scopeUsableDiscount(Builder|QueryBuilder $query)
    {
        $this->scopeUsable($query)->where('type', Voucher::DISCOUNT_TYPE);
    }


    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function redeemedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
