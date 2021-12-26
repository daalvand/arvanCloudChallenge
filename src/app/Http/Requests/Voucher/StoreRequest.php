<?php

namespace App\Http\Requests\Voucher;

use App\Http\Requests\BaseRequest;
use App\Models\Voucher;

class StoreRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'title'      => 'required|string|min:3|max:255',
            'type'       => 'required|in:' . implode(',', Voucher::TYPES),
            'expires_at' => 'required|after:now|after:starts_at',
            'starts_at'  => 'date|after:now|before:expires_at',
            'amount'     => 'required|numeric',
            'max_uses'   => 'required|numeric|min:1',
        ];
    }
}
