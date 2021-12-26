<?php

namespace App\Http\Requests\Voucher;

use App\Http\Requests\BaseRequest;
use App\Rules\UsableChargeVoucher;

class ChargeRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', new UsableChargeVoucher()],
        ];
    }
}
