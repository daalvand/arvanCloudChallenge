<?php

namespace App\Listeners;


use App\Events\ChargeVoucher;
use App\Services\Wallet\TransactionService;

class ChargeTransactionListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param ChargeVoucher $event
     * @return void
     */
    public function handle($event)
    {
        $user    = $event->getUser();
        $voucher = $event->getVoucher();
        app(TransactionService::class)
            ->setUser($user)
            ->deposit($voucher->amount, ['description' => "Voucher: $voucher->code"]);
    }
}
