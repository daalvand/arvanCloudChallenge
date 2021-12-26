<?php

namespace App\Filters\Transaction;

class DateFilter
{
    public function handle($builder, $next)
    {
        if(request()->has('start_date') && request()->has('end_date')) {
            $builder->whereBetween('created_at', [request('start'), request('end')]);
        }
        return $next($builder);
    }
}
