<?php

namespace App\Filters\Transaction;

class OrderByFilter
{
    public function handle($builder, $next)
    {
        // Order if request has orders parameter
        // example: ?orders[field]=id&orders[direction]=desc
        if (request()->has('orders')) {
            $orders = request()->get('orders');
            foreach ($orders as $order) {
                $builder->orderBy($order['field'], $order['direction']);
            }
        }else{
            $builder->orderBy('created_at', 'desc');
        }
        return $next($builder);
    }
}
