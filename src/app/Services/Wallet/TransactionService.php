<?php

namespace App\Services\Wallet;

use App\Models\Transaction;
use App\Models\User;
use Auth;
use RuntimeException;

class TransactionService
{
    protected null|User $user = null;


    /**
     * deposit the given amount to the user
     * @param integer $amount
     * @param array   $meta
     * @param bool    $confirmed
     * @return Transaction
     */
    public function deposit(int $amount, array $meta = [], bool $confirmed = true): Transaction
    {
        $user = $this->getUser();
        if ($confirmed) {
            $user->balance += $amount;
            $user->save();
        }
        return $user->transactions()
            ->create([
                'amount'    => $amount,
                'type'      => 'deposit',
                'confirmed' => $confirmed,
                'meta'      => $meta
            ]);
    }

    /**
     * Fail to deposit the given amount to the user
     * @param integer $amount
     * @param array   $meta
     */
    public function failDeposit(int $amount, array $meta = [])
    {
        $this->deposit($amount, $meta, false);
    }

    /**
     * withdraw the given amount from the user
     * @param integer $amount
     * @param array   $meta
     * @param boolean $shouldConfirmed
     * @return Transaction
     */
    public function withdraw(int $amount, array $meta = [], bool $shouldConfirmed = false): Transaction
    {
        $user      = $this->getUser();
        $confirmed = $shouldConfirmed || $user->canWithdraw($amount);
        if ($confirmed) {
            $user->balance -= $amount;
            $user->save();
        }
        return $user->transactions()
            ->create([
                'amount'    => $amount,
                'type'      => 'withdraw',
                'confirmed' => $confirmed,
                'meta'      => $meta
            ]);
    }

    /**
     * Force to withdraw the given amount from the user
     * @param integer $amount
     * @param array   $meta
     */
    public function forceWithdraw(int $amount, array $meta = [])
    {
        $this->withdraw($amount, $meta, true);
    }

    /**
     * @param User $user
     * @return $this
     */
    public function setUser(User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getUser(): User
    {
        $this->user = $this->user ?? Auth::user();
        if (!$this->user) {
            throw new RuntimeException('User not found');
        }
        return $this->user;
    }
}
