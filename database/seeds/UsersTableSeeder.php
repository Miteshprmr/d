<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Throwable
     */
    public function run()
    {
        DB::transaction(function () {
            factory(App\Models\User::class, 5)
            ->create()
            ->each(function ($user) {
                $user->bankAccounts()->save(factory(\App\Models\BankAccount::class)->make());
            });;
        });
    }
}
