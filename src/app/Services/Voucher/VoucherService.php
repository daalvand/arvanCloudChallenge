<?php

namespace App\Services\Voucher;

use App\Events\ChargeVoucher;
use App\Helpers\Str;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;

class VoucherService
{
    /**
     * @param array $inputs
     * @param User  $user
     * @return Voucher
     */
    public function create(array $inputs, User $user): Voucher
    {
        $inputs['code'] = $inputs['code'] ?? $this->createCode();
        $inputs['type'] = $inputs['type'] ?? Voucher::CHARGE_TYPE;
        return $user->madeVouchers()->create($inputs);
    }

    public function createCharge(array $inputs, user $user): Voucher
    {
        $inputs['type'] = Voucher::CHARGE_TYPE;
        return self::create($inputs, $user);
    }

    public function createDiscount(array $inputs, user $user): Voucher
    {
        $inputs['type'] = Voucher::DISCOUNT_TYPE;
        return self::create($inputs, $user);
    }

    /**
     * @return string
     */
    protected function createCode(): string
    {
        $voucher = Str::unique();
        while (Voucher::query()->where('code', $voucher)->exists()) {
            $voucher = Str::unique();
        }
        return $voucher;
    }

    public function update(Voucher $voucher, array $inputs): Voucher
    {
        $voucher->update($inputs);
        return $voucher;
    }

    public function charge(string $code, User $user)
    {
        $voucher = Voucher::query()->where('code', $code)->firstOrFail();
        DB::transaction(function () use ($voucher, $user) {
            $voucher->lockForUpdate()->increment('used_count');
            $user->redeemedVouchers()->attach($voucher->id, ['redeemed_at' => now()]);
        });
        ChargeVoucher::dispatch($user, $voucher);
    }
}
