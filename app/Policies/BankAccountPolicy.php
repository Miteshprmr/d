<?php

namespace App\Policies;

use App\Models\User;
use App\Models\BankAccount;
use Illuminate\Auth\Access\HandlesAuthorization;

class BankAccountPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the BankAccount.
     *
     * @param \App\Models\User $user
     * @param \App\Models\BankAccount $bankAccount
     * @return bool
     */
    public function view(User $user, BankAccount $bankAccount)
    {
        return $user->id === $bankAccount->user_id;
    }

    /**
     * Determine whether the user can update the BankAccount.
     *
     * @param \App\Models\User $user
     * @param \App\Models\BankAccount $bankAccount
     * @return bool
     */
    public function update(User $user, BankAccount $bankAccount)
    {
        return $user->id === $bankAccount->user_id;
    }

    /**
     * Determine whether the user can delete the BankAccount.
     *
     * @param \App\Models\User $user
     * @param  \App\Models\BankAccount $bankAccount
     * @return bool
     */
    public function delete(User $user, BankAccount $bankAccount)
    {
        return $user->id === $bankAccount->user_id;
    }
}
