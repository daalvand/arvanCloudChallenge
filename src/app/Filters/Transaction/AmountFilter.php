<?php

namespace App\Filters\Transaction;

class AmountFilter
{
    public function handle($query, $next)
    {
        if (request()->has('amount')) {
            $query->where('amount', '>=',request('amount'));
        }
        return $next($query);
    }
}
