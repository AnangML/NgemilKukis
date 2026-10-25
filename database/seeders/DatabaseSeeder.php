<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ProductSeeder::class,
            GreetingCardTemplateSeeder::class,
            SiteSettingSeeder::class,
            OrderSeeder::class,
            CouponSeeder::class,
        ]);

        // Seed default Admin user
        User::updateOrCreate(
            ['email' => 'admin@ngemilkukis.com'],
            [
                'name' => 'Admin NgemilKukis',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // Seed default Penjual (Seller)
        User::updateOrCreate(
            ['email' => 'penjual@ngemilkukis.com'],
            [
                'name' => 'Siti Nurhaliza (Penjual)',
                'password' => Hash::make('password123'),
                'phone' => '081234567891',
                'role' => 'seller',
                'auth_provider' => 'email',
            ]
        );

        // Seed default Customer Service (CS)
        User::updateOrCreate(
            ['email' => 'cs@ngemilkukis.com'],
            [
                'name' => 'Andi Customer Service',
                'password' => Hash::make('password123'),
                'phone' => '081234567892',
                'role' => 'customer_service',
                'auth_provider' => 'email',
            ]
        );

        // Seed default VIP Customer for demo & testing
        $customer = User::updateOrCreate(
            ['email' => 'customer@ngemilkukis.com'],
            [
                'name' => 'Budi Customer VIP',
                'password' => Hash::make('password123'),
                'phone' => '081298765432',
                'role' => 'customer',
                'auth_provider' => 'email',
                'member_discount_amount' => 10000,
                'points_balance' => 200,
                'saved_city' => 'Jakarta Selatan',
                'saved_address' => 'Jl. Senopati No. 88, Kebayoran Baru',
            ]
        );

        // Seed initial welcome bonus point transaction if customer has no transactions
        if ($customer->pointTransactions()->doesntExist()) {
            $customer->pointTransactions()->create([
                'type' => 'bonus',
                'amount' => 200,
                'balance_after' => 200,
                'description' => 'Bonus Spesial Member Baru & Loyalitas VIP',
                'created_at' => now(),
            ]);
        }
    }
}
