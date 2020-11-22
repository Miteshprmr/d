<?php

namespace App\Api\V1\Http\Controllers;

use App\Models\User;
use App\Models\BankAccount;
use App\Api\V1\Http\Requests\AccountRequest;
use App\Api\V1\Http\Resources\AccountDetail;
use App\Api\V1\Http\Resources\AccountCollection;
use App\Api\V1\Http\Requests\AccountUpdateRequest;

class BankAccountController extends ApiController
{
    /**
     * BankAccountController constructor.
     */
    public function __construct()
    {
        $this->user = $this->getLoggedInUser();
    }

    /**
     * Get users bank accounts.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $accountQuery = BankAccount::with('user')
                    ->where('user_id', $this->user->id)->orderBy('created_at', 'desc');

        $accounts = new AccountCollection($accountQuery->paginate());

        return $this->respondOk($accounts);
    }

    /**
     * Get users bank account.
     *
     * @param BankAccount $bankAccount
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(BankAccount $bankAccount)
    {
        $bankAccount = new AccountDetail($bankAccount);

        return $this->respondOk($bankAccount);
    }

    /**
     * Create users bank account.
     *
     * @param AccountRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(AccountRequest $request)
    {
        $this->user->bankAccounts()->create([
            'balance' => $request->input('balance'),
            'account_number' => $request->input('account_number'),
        ]);

        return $this->respondSuccess();
    }

    /**
     * Update users bank account.
     *
     * @param AccountUpdateRequest $request
     * @param BankAccount $bankAccount
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(AccountUpdateRequest $request, BankAccount $bankAccount)
    {
        $bankAccount->update([
            'balance' => $request->input('balance'),
            'account_number' => $request->input('account_number'),
        ]);
        return $this->respondSuccess();
    }

    /**
     * Delete users bank account.
     *
     * @param BankAccount $bankAccount
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(BankAccount $bankAccount)
    {
        $bankAccount->delete();

        return $this->respondSuccess();
    }
}
