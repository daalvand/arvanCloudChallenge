<?php

namespace App\Filters\Transaction;

class TypeFilter
{
    public function handle($builder, $next)
    {
        //deposit or withdraw
        if (request()->has('type')) {
            $builder->where('type', request('type'));
        }
        return $next($builder);
    }
}
