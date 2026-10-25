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
            ['key' => 'site_logo_url', 'value' => '', 'group' => 'general'],
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
            ['key' => 'hero_trust_badge_1', 'value' => '4.95 Rating Kepuasan', 'group' => 'hero'],
            ['key' => 'hero_trust_badge_2', 'value' => 'Sameday & Paxel Pendingin', 'group' => 'hero'],
            ['key' => 'hero_trust_badge_3', 'value' => 'Gratis Greeting Card', 'group' => 'hero'],

            // Shipping Rates
            ['key' => 'shipping_instant_grab', 'value' => '25000', 'group' => 'shipping'],
            ['key' => 'shipping_sameday_gojek', 'value' => '18000', 'group' => 'shipping'],
            ['key' => 'shipping_paxel_food', 'value' => '22000', 'group' => 'shipping'],
            ['key' => 'shipping_regular_jne', 'value' => '12000', 'group' => 'shipping'],

            // Heating & Storage Guide (#panduan)
            ['key' => 'guide_badge', 'value' => 'Tips Baker NgemilKukis', 'group' => 'guide'],
            ['key' => 'guide_headline', 'value' => 'Cara Terbaik Menikmati Kukis Lumer', 'group' => 'guide'],
            ['key' => 'guide_microwave_title', 'value' => 'Microwave (15 - 20 Detik)', 'group' => 'guide'],
            ['key' => 'guide_microwave_desc', 'value' => 'Cokelat dan isian lava di tengahnya akan kembali meleleh hangat dan lembut seketika.', 'group' => 'guide'],
            ['key' => 'guide_oven_title', 'value' => 'Oven / Airfryer (3 Menit)', 'group' => 'guide'],
            ['key' => 'guide_oven_desc', 'value' => 'Suhu 160°C untuk mendapatkan kembali kerenyahan pinggiran kukis yang renyah bermentega.', 'group' => 'guide'],
            ['key' => 'guide_storage_title', 'value' => 'Daya Simpan', 'group' => 'guide'],
            ['key' => 'guide_storage_desc', 'value' => 'Tahan hingga 7 hari di suhu ruang dalam toples/wadah kedap udara, atau 14 hari di kulkas.', 'group' => 'guide'],

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
