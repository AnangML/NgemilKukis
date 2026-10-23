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
    }
}
