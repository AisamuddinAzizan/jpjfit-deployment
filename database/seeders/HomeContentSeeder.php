<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class HomeContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->testimonials() as $row) {
            Testimonial::query()->updateOrCreate(
                ['name' => $row['name'], 'content' => $row['content']],
                $row,
            );
        }

        foreach ($this->faqs() as $row) {
            Faq::query()->updateOrCreate(
                ['question' => $row['question']],
                $row,
            );
        }
    }

    private function testimonials(): array
    {
        return [
            [
                'name' => 'A. Rahman - JPJ Operations',
                'avatar' => null,
                'content' => 'JPJFit gives us one clean flow from registration to certificate issuance.',
                'rating' => 5,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'N. Syafiqah - Health Officer',
                'avatar' => null,
                'content' => 'Health screening records are easier to track and review during every test cycle.',
                'rating' => 5,
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'M. Daniel - System Admin',
                'avatar' => null,
                'content' => 'The dashboard helps management monitor pass rate and readiness in near real time.',
                'rating' => 4,
                'sort_order' => 3,
                'is_active' => true,
            ],
        ];
    }

    private function faqs(): array
    {
        return [
            [
                'question' => 'Who can access JPJFit?',
                'question_ms' => 'Siapa yang boleh mengakses JPJFit?',
                'answer' => 'System Admin, JPJ Officers, and Health Officers can access JPJFit based on assigned roles.',
                'answer_ms' => 'Pentadbir Sistem, Pegawai JPJ dan Pegawai Kesihatan boleh mengakses JPJFit berdasarkan peranan yang ditetapkan.',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'question' => 'Can I export reports?',
                'question_ms' => 'Bolehkah saya mengeksport laporan?',
                'answer' => 'Yes. Reports can be exported in CSV and PDF formats for sharing and archiving.',
                'answer_ms' => 'Ya. Laporan boleh dieksport dalam format CSV dan PDF untuk perkongsian serta arkib.',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'question' => 'How is pass rate calculated?',
                'question_ms' => 'Bagaimana kadar lulus dikira?',
                'answer' => 'Pass rate equals total results with status Pass divided by all recorded fitness results.',
                'answer_ms' => 'Kadar lulus ialah jumlah keputusan berstatus Lulus dibahagikan dengan semua keputusan kecergasan yang direkodkan.',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'question' => 'Does JPJFit support different devices?',
                'question_ms' => 'Adakah JPJFit menyokong peranti berbeza?',
                'answer' => 'Yes. The interface is responsive for mobile, tablet and desktop workflows.',
                'answer_ms' => 'Ya. Antara muka responsif untuk aliran kerja mudah alih, tablet dan desktop.',
                'sort_order' => 4,
                'is_active' => true,
            ],
        ];
    }
}
