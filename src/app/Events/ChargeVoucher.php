<?php

namespace App\Events;

use App\Models\User;
use App\Models\Voucher;
use Carbon\CarbonInterface;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChargeVoucher
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected CarbonInterface $time;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(protected User $user,protected Voucher $voucher)
    {
        $this->time = now();
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return \App\Models\Voucher
     */
    public function getVoucher(): Voucher
    {
        return $this->voucher;
    }

    /**
     * @return CarbonInterface
     */
    public function getTime(): CarbonInterface
    {
        return $this->time;
    }
}
