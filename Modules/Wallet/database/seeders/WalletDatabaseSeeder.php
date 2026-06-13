<?php

namespace Modules\Wallet\database\seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Modules\Wallet\Models\Wallet;

class WalletDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('user_type','user')->get();

        foreach ($users as $user) {
            $wallet = [
                'title' => $user->first_name.' '.$user->last_name,
                'user_id' => $user->id,
                'amount' => 0
            ];
            Wallet::create($wallet);
        }
    }
}
