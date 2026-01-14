<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Hero Section
            ['key' => 'hero_title', 'value' => 'Kesehatan Mental Adalah Prioritas', 'type' => 'text', 'group' => 'hero'],
            ['key' => 'hero_subtitle', 'value' => 'Terhubung dengan psikiater, psikolog, dan conversationalist profesional untuk konsultasi kesehatan mental Anda kapan saja, di mana saja.', 'type' => 'textarea', 'group' => 'hero'],

            // Stats Section
            ['key' => 'stats_professionals', 'value' => '100+', 'type' => 'text', 'group' => 'stats'],
            ['key' => 'stats_professionals_label', 'value' => 'Profesional Berpengalaman', 'type' => 'text', 'group' => 'stats'],
            ['key' => 'stats_consultations', 'value' => '10,000+', 'type' => 'text', 'group' => 'stats'],
            ['key' => 'stats_consultations_label', 'value' => 'Konsultasi Sukses', 'type' => 'text', 'group' => 'stats'],
            ['key' => 'stats_rating', 'value' => '4.9/5', 'type' => 'text', 'group' => 'stats'],
            ['key' => 'stats_rating_label', 'value' => 'Rating Kepuasan', 'type' => 'text', 'group' => 'stats'],

            // Features Section
            ['key' => 'features_title', 'value' => 'Mengapa Memilih Teman Bicara?', 'type' => 'text', 'group' => 'features'],
            ['key' => 'features_subtitle', 'value' => 'Platform terpercaya untuk kesehatan mental Anda', 'type' => 'text', 'group' => 'features'],

            // CTA Section
            ['key' => 'cta_title', 'value' => 'Siap Memulai Perjalanan Kesehatan Mental Anda?', 'type' => 'text', 'group' => 'cta'],
            ['key' => 'cta_subtitle', 'value' => 'Bergabunglah dengan ribuan orang yang telah merasakan manfaatnya', 'type' => 'text', 'group' => 'cta'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
