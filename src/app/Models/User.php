<?php

namespace App\Models;

use Database\Factories\UserFactory;
use DateTimeInterface;
use Eloquent;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string|null $name
 * @property string $mobile
 * @property Carbon|null $mobile_verified_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $password
 * @property string $type
 * @property int $balance wallet balance
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection|PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @property-read Collection|Transaction[] $transactions
 * @property-read int|null $transactions_count
 * @property-read Collection|Voucher[] $vouchers
 * @property-read int|null $vouchers_count
 * @method static UserFactory factory(...$parameters)
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereMobile($value)
 * @method static Builder|User whereMobileVerifiedAt($value)
 * @method static Builder|User whereName($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @method static Builder|User whereBalance($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User whereType($value)
 * @mixin Eloquent
 * @property-read Collection|\App\Models\Voucher[] $madeVouchers
 * @property-read int|null $made_vouchers_count
 * @property-read Collection|\App\Models\Voucher[] $redeemedVouchers
 * @property-read int|null $redeemed_vouchers_count
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const TYPES      = [self::ADMIN_TYPE, self::USER_TYPE];
    const ADMIN_TYPE = 'admin';
    const USER_TYPE  = 'user';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'mobile',
    ];

    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'mobile_verified_at' => 'datetime',
    ];

    /**
     * Set the polymorphic relation.
     *
     * @return HasMany
     */
    public function madeVouchers(): HasMany
    {
        return $this->hasMany(Voucher::class, 'user_id');
    }

    /**
     * Set the polymorphic relation.
     *
     * @return BelongsToMany
     */
    public function redeemedVouchers(): BelongsToMany
    {
        return $this->belongsToMany(Voucher::class);
    }

    /**
     * Retrieve all transactions of this user
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class)->latest();
    }

    /**
     * Determine if the user can withdraw the given amount
     * @param integer $amount
     * @return boolean
     */
    public function canWithdraw(int $amount): bool
    {
        return $this->balance >= $amount;
    }

    /**
     * Returns the actual balance for this wallet.
     * Might be different from the balance property if the database is manipulated
     */
    public function actualBalance(): int
    {
        $credits = $this->transactions()->where('type', 'deposit')->where('confirmed', 1)->sum('amount');
        $debits  = $this->transactions()->where('type', 'withdraw')->where('confirmed', 1)->sum('amount');
        return $credits - $debits;
    }

    public function isAdmin(): bool
    {
        return $this->type === self::ADMIN_TYPE;
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
