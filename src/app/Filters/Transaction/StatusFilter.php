<?php

namespace App\Filters\Transaction;

class StatusFilter
{
    public function handle($builder, $next)
    {
        // return transactions confirmed or not
        if (request()->has('status')) {
            $builder->where('confirmed', (bool)request('status'));
        }

        return $next($builder);
    }
}
