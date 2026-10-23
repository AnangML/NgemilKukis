<?php

namespace Database\Seeders;

use App\Models\GreetingCardTemplate;
use Illuminate\Database\Seeder;

class GreetingCardTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Happy Birthday Sweet Moments',
                'slug' => 'happy-birthday',
                'theme_color' => 'amber',
                'icon' => '🎂',
                'design_style' => 'birthday_confetti',
                'default_message' => 'Selamat ulang tahun! Semoga harimu semanis dan sehangat kukis ini, selalu dilimpahi kesehatan, kebahagiaan, dan tercapai semua impianmu.',
                'price' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Happy Graduation & Success',
                'slug' => 'happy-graduation',
                'theme_color' => 'indigo',
                'icon' => '🎓',
                'design_style' => 'graduation_hat',
                'default_message' => 'Selamat atas kelulusan dan pencapaian hebatmu! Semua kerja kerasmu terbayar tuntas. Semoga langkah selanjutnya semakin gemilang!',
                'price' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Warm Thank You',
                'slug' => 'warm-thank-you',
                'theme_color' => 'rose',
                'icon' => '💐',
                'design_style' => 'floral_thankyou',
                'default_message' => 'Terima kasih banyak atas segala perhatian dan kebaikanmu yang begitu berharga. Nikmati kukis hangat ini sebagai rasa terima kasihku.',
                'price' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Thinking of You & Cheer Up',
                'slug' => 'thinking-of-you',
                'theme_color' => 'emerald',
                'icon' => '✨',
                'design_style' => 'cozy_cookie',
                'default_message' => 'Mengirimkan sedikit kehangatan dan rasa manis untuk menemani harimu. Tetap semangat, rehatlah sejenak dan nikmati gigitan penuh cinta ini!',
                'price' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Love & Romance (Untuk Tersayang)',
                'slug' => 'love-romance',
                'theme_color' => 'pink',
                'icon' => '💖',
                'design_style' => 'romantic_love',
                'default_message' => 'Kukis ini dibuat dengan penuh cinta, khusus untuk kamu yang paling istimewa di hatiku. Terima kasih sudah selalu ada dan membuat hariku lebih manis.',
                'price' => 0,
                'is_active' => true,
            ],
        ];

        foreach ($templates as $template) {
            GreetingCardTemplate::updateOrCreate(
                ['slug' => $template['slug']],
                $template
            );
        }
    }
}
