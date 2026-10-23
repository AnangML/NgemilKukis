<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General & Brand
            ['key' => 'site_name', 'value' => 'NgemilKukis', 'group' => 'general'],
            ['key' => 'site_tagline', 'value' => 'Artisan Fresh Bake', 'group' => 'general'],
            ['key' => 'about_text', 'value' => 'Toko kukis artisan online dengan mentega Prancis murni & cokelat Belgia terbaik. Dipanggang fresh setiap hari dengan penuh kehangatan.', 'group' => 'general'],

            // Announcement
            ['key' => 'announcement_active', 'value' => '1', 'group' => 'announcement'],
            ['key' => 'announcement_text', 'value' => 'Freshly Baked Today! Gratis Custom Greeting Card untuk setiap pemesanan Box of 4 & 6.', 'group' => 'announcement'],
            ['key' => 'announcement_link', 'value' => '#katalog', 'group' => 'announcement'],
            ['key' => 'announcement_link_text', 'value' => 'Pesan Sekarang →', 'group' => 'announcement'],

            // Hero Section
            ['key' => 'hero_badge', 'value' => 'Dipanggang Segar Setiap Hari • 100% Belgian Chocolate', 'group' => 'hero'],
            ['key' => 'hero_headline', 'value' => 'Kukis Renyah di Luar,', 'group' => 'hero'],
            ['key' => 'hero_subheadline', 'value' => 'Lumer Manis di Dalam!', 'group' => 'hero'],
            ['key' => 'hero_description', 'value' => 'Dibuat tangan dengan mentega Prancis autentik dan lelehan cokelat Belgia murni. Nikmati sensasi kukis hangat untuk teman ngopi, atau kirim sebagai Hampers Kado dengan Custom Kartu Ucapan personal.', 'group' => 'hero'],
            ['key' => 'hero_image_url', 'value' => 'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?auto=format&fit=crop&w=1000&q=80', 'group' => 'hero'],

            // Social & Contact
            ['key' => 'whatsapp_number', 'value' => '6281234567890', 'group' => 'contact'],
            ['key' => 'whatsapp_display', 'value' => '+62 812-3456-7890', 'group' => 'contact'],
            ['key' => 'instagram_username', 'value' => 'ngemilkukis', 'group' => 'contact'],
            ['key' => 'instagram_url', 'value' => 'https://instagram.com', 'group' => 'contact'],

            // Pre-Order Schedule
            ['key' => 'preorder_badge', 'value' => 'Batch Pre-Order Minggu Ini', 'group' => 'preorder'],
            ['key' => 'preorder_headline', 'value' => 'Jadwal Baking & Pengiriman Spesial', 'group' => 'preorder'],
            ['key' => 'preorder_description', 'value' => 'Kukis dipanggang sesuai kuota batch agar kualitas aroma dan kegaringan tetap optimal saat tiba di tanganmu.', 'group' => 'preorder'],
            ['key' => 'preorder_next_batch', 'value' => 'Jumat, 25 Oktober 2026', 'group' => 'preorder'],
            ['key' => 'preorder_cutoff', 'value' => 'Kamis pukul 18:00 WIB', 'group' => 'preorder'],
        ];

        foreach ($settings as $setting) {
            SiteSetting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'group' => $setting['group']]
            );
        }
    }
}
