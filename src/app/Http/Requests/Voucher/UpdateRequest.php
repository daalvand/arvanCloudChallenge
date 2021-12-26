<?php

namespace App\Http\Requests\Voucher;

use App\Http\Requests\BaseRequest;
use App\Models\Voucher;

class UpdateRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'title'      => 'string|min:3|max:255',
            'type'       => 'in:' . implode(',', Voucher::TYPES),
            'expires_at' => 'date|after:now|after:starts_at',
            'starts_at'  => 'date|after:now|before:expires_at',
            'amount'     => 'numeric',
            'max_uses'   => 'numeric|min:1',
        ];
    }
}
