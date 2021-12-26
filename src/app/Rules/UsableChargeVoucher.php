<?php

namespace App\Rules;

use App\Models\Voucher;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UsableChargeVoucher implements Rule
{
    protected string $attribute;

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $this->attribute = $attribute;
        if(!is_string($value)){
            return false;
        }
        return Voucher::usableCharge()
            ->doesntRelationWith(Auth::id())
            ->where('code', $value)
            ->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return __('validation.in', ['attribute' => $this->attribute]);
    }
}
