<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::create([
            'site_name' => [
                'en' => 'Zahin-Oxus',
                'fa' => 'ذهین اکسوس',
                'ps' => 'ذهین اکسوس',
            ],
            'site_logo' => 'default-user.png',
            'site_favicon' => 'favicon.ico',
            'email' => 'info@zahinoxus.com',
            'phone' => '+93 783226633',
            'address' => [
                'en' => 'Karte Se, Kabul, Afghanistan',
                'fa' => 'کارته سه، کابل، افغانستان',
                'ps' => 'کارته ۳، کابل، افغانستان',
            ],
            'about' => [
                'en' => 'Welcome to Zahin Oxus, where expert financial advice meets personalized service...',
                'fa' => 'به ذهین اکسوس خوش آمدید، جایی که مشوره‌های مالی...',
                'ps' => 'ذهین اکسوس ته ښه راغلاست، هغه ځای چې دلته مسلکي مالي مشورې...',
            ],
        ]);
    }
}
