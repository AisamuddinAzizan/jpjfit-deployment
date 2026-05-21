<?php

namespace App\Services;

use App\Models\LandingPageContent;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class LandingPageContentService
{
    private const CACHE_KEY = 'home:landing-content:matrix';
    private const DEFAULT_LOCALE = 'en';

    /** @var array<int, string> */
    private const SUPPORTED_LOCALES = ['en', 'ms'];

    /**
     * Returns all supported landing-page keys with the current default values.
     */
    public function defaults(string $locale = self::DEFAULT_LOCALE): array
    {
        $locale = $this->normalizeLocale($locale);

        if ($locale === 'ms') {
            return [
                'meta_title' => 'JPJFit - Sistem Pemantauan Kecergasan',
                'meta_description' => 'JPJFit membantu pasukan JPJ dan KKM memantau prestasi kecergasan, sesi, dan pensijilan dalam satu platform moden.',
                'brand_name' => 'JPJFit',
                'brand_subtitle' => 'Sistem Pemantauan Kecergasan',
                'nav_overview_label' => 'Gambaran Keseluruhan',
                'nav_features_label' => 'Ciri-ciri',
                'nav_workflow_label' => 'Cara Ia Berfungsi',
                'nav_faq_label' => 'Soalan Lazim',
                'nav_login_button' => 'Log Masuk',
                'nav_register_button' => 'Daftar',
                'hero_chip' => 'Kesiapsiagaan Operasi',
                'hero_title' => 'JPJFit - Sistem Pemantauan Kecergasan',
                'hero_description' => 'Jejak perjalanan setiap peserta daripada pendaftaran, kehadiran sesi, saringan kesihatan, dan pensijilan keputusan dalam satu aliran digital.',
                'hero_primary_button' => 'Mula Pantau',
                'hero_secondary_button' => 'Teroka Ciri-ciri',
                'hero_register_button' => 'Cipta Akaun',
                'overview_overline' => 'Gambaran Sistem',
                'overview_title' => 'Pusat Kawalan Untuk Pemantauan Kecergasan',
                'overview_description' => 'JPJFit dibina khas untuk kesiapsiagaan operasi. Pasukan boleh mengurus peserta, sesi ujian, semakan kesihatan, dan keputusan daripada satu platform yang selamat dan mudah dicari.',
                'overview_chip_one_title' => 'Akses Berdasarkan Peranan',
                'overview_chip_one_text' => 'Aliran kerja Admin, JPJ, dan KKM dengan pengasingan yang selamat.',
                'overview_chip_two_title' => 'Rekod Sedia Audit',
                'overview_chip_two_text' => 'Rekod konsisten untuk setiap acara ujian dan pensijilan.',
                'overview_kpi_participants' => 'Peserta',
                'overview_kpi_sessions' => 'Sesi',
                'overview_kpi_pass_rate' => 'Kadar Lulus',
                'overview_kpi_live_workouts' => 'Latihan Langsung',
                'features_overline' => 'Ciri Utama',
                'features_title' => 'Direka Untuk Pantas Dan Tepat',
                'feature_1_title' => 'Pengurusan Peserta',
                'feature_1_text' => 'Simpan butiran lengkap peserta dan status di satu tempat.',
                'feature_2_title' => 'Penjadualan Sesi',
                'feature_2_text' => 'Rancang sesi dan pantau kehadiran dengan cekap.',
                'feature_3_title' => 'Skor Automatik',
                'feature_3_text' => 'Pengelasan automatik dan keputusan lulus/gagal.',
                'feature_4_title' => 'Laporan & Sijil',
                'feature_4_text' => 'Eksport dan jana output dalam beberapa minit.',
                'workflow_overline' => 'Cara Ia Berfungsi',
                'workflow_title' => 'Empat Langkah Kejelasan Operasi',
                'workflow_1_title' => 'Daftar Peserta',
                'workflow_1_text' => 'Simpan profil rasmi dan tetapkan ke sesi.',
                'workflow_2_title' => 'Laksanakan Ujian',
                'workflow_2_text' => 'Rekod saringan kesihatan dan metrik kecergasan.',
                'workflow_3_title' => 'Nilai Secara Automatik',
                'workflow_3_text' => 'Sistem mengira skor dan status keputusan.',
                'workflow_4_title' => 'Jana Output',
                'workflow_4_text' => 'Keluarkan laporan dan sijil dengan cepat.',
                'preview_fitness_title' => 'Pratonton Ujian Kecergasan',
                'preview_fitness_badge' => 'Sedia UKJK',
                'preview_fitness_description' => 'Slider perbandingan interaktif yang menunjukkan profil kesiapsiagaan sebelum dan selepas.',
                'preview_countdown_title' => 'Kiraan Detik Ujian Seterusnya',
                'preview_countdown_description' => 'Kiraan detik dikemas kini secara langsung ke sesi seterusnya dan menyokong semakan BMI pantas.',
                'open_bmi_button' => 'Buka Kalkulator BMI',
                'fitness_level_beginner' => 'Permulaan',
                'fitness_level_intermediate' => 'Pertengahan',
                'fitness_level_advanced' => 'Lanjutan',
                'stats_overline' => 'Statistik Langsung',
                'stats_title' => 'Prestasi Terukur, Nombor Sebenar',
                'stats_pass_rate_label' => 'Kadar Lulus',
                'stats_total_sessions_label' => 'Jumlah Sesi',
                'stats_total_participants_label' => 'Jumlah Peserta',
                'stats_live_workout_label' => 'Kaunter Latihan Langsung',
                'dashboard_preview_overline' => 'Pratonton Papan Pemuka',
                'dashboard_preview_title' => 'Pengalaman Pemantauan Interaktif',
                'testimonials_overline' => 'Testimoni',
                'testimonials_title' => 'Dipercayai Oleh Pasukan Operasi',
                'faq_overline' => 'Soalan Lazim',
                'faq_title' => 'Soalan Yang Mungkin Anda Tanya',
                'faq_search_placeholder' => 'Cari soalan lazim...',
                'faq_1_question' => 'Siapa yang boleh mengakses JPJFit?',
                'faq_1_answer' => 'Pentadbir Sistem, Pegawai JPJ dan Pegawai Kesihatan boleh mengakses JPJFit berdasarkan peranan yang ditetapkan.',
                'faq_2_question' => 'Bolehkah saya mengeksport laporan?',
                'faq_2_answer' => 'Ya. Laporan boleh dieksport dalam format CSV dan PDF untuk perkongsian serta arkib.',
                'faq_3_question' => 'Bagaimana kadar lulus dikira?',
                'faq_3_answer' => 'Kadar lulus ialah jumlah keputusan berstatus Lulus dibahagikan dengan semua keputusan kecergasan yang direkodkan.',
                'faq_4_question' => 'Adakah JPJFit menyokong peranti berbeza?',
                'faq_4_answer' => 'Ya. Antara muka responsif untuk aliran kerja mudah alih, tablet dan desktop.',
                'cta_overline' => 'Mulakan Transformasi Digital Anda',
                'cta_title' => 'Bersedia Meningkatkan Pemantauan Kecergasan?',
                'cta_description' => 'Lancarkan kitaran laporan lebih cepat, pengumpulan data lebih kemas, dan kesiapsiagaan lebih kukuh dengan JPJFit.',
                'cta_login_button' => 'Log Masuk Sekarang',
                'cta_register_button' => 'Daftar Hari Ini',
                'cta_offer_prefix' => 'Tawaran tamat dalam:',
                'footer_brand_description' => 'Pemantauan kecergasan yang bertenaga, moden dan boleh dipercayai untuk operasi sektor awam.',
                'footer_quick_links_title' => 'Pautan Pantas',
                'footer_support_title' => 'Sokongan',
                'footer_newsletter_title' => 'Buletin',
                'footer_newsletter_description' => 'Dapatkan nota keluaran dan kemas kini pemantauan.',
                'footer_newsletter_name_placeholder' => 'Nama anda',
                'footer_newsletter_email_placeholder' => 'anda@contoh.com',
                'footer_newsletter_button' => 'Langgan',
                'footer_copy_text' => 'JPJFit - Sistem Pemantauan Kecergasan',
                'footer_bottom_note' => 'Jabatan Pengangkutan Jalan & Operasi Pemantauan Kesihatan',
                'fab_top_button' => 'Atas',
                'fab_cta_button' => 'CTA',
                'fab_hc_button' => 'HC',
                'bmi_modal_title' => 'Kalkulator BMI',
                'bmi_height_label' => 'Tinggi (cm)',
                'bmi_weight_label' => 'Berat (kg)',
                'bmi_calculate_button' => 'Kira',
                'bmi_result_default' => 'Masukkan nilai dan kira BMI anda.',
            ];
        }

        return [
            'meta_title' => 'JPJFit - Fitness Monitoring System',
            'meta_description' => 'JPJFit helps JPJ and KKM teams monitor fitness performance, sessions, and certifications in one modern platform.',
            'brand_name' => 'JPJFit',
            'brand_subtitle' => 'Fitness Monitoring System',
            'nav_overview_label' => 'Overview',
            'nav_features_label' => 'Features',
            'nav_workflow_label' => 'How It Works',
            'nav_faq_label' => 'FAQ',
            'nav_login_button' => 'Login',
            'nav_register_button' => 'Register',
            'hero_chip' => 'Operational Readiness',
            'hero_title' => 'JPJFit - Fitness Monitoring System',
            'hero_description' => 'Track every participant journey from registration, session attendance, health screening, and result certification in one digital flow.',
            'hero_primary_button' => 'Start Monitoring',
            'hero_secondary_button' => 'Explore Features',
            'hero_register_button' => 'Create Account',
            'overview_overline' => 'System Overview',
            'overview_title' => 'A Command Center For Fitness Monitoring',
            'overview_description' => 'JPJFit is purpose-built for operational readiness. Teams can manage participants, test sessions, health checks, and outcomes from one secure, searchable platform.',
            'overview_chip_one_title' => 'Role-Based Access',
            'overview_chip_one_text' => 'Admin, JPJ, and KKM workflows with secure separation.',
            'overview_chip_two_title' => 'Audit-Ready Records',
            'overview_chip_two_text' => 'Consistent records for every test and certification event.',
            'overview_kpi_participants' => 'Participants',
            'overview_kpi_sessions' => 'Sessions',
            'overview_kpi_pass_rate' => 'Pass Rate',
            'overview_kpi_live_workouts' => 'Live Workouts',
            'features_overline' => 'Key Features',
            'features_title' => 'Built To Move Fast And Stay Accurate',
            'feature_1_title' => 'Participant Management',
            'feature_1_text' => 'Capture full participant details and status in one place.',
            'feature_2_title' => 'Session Scheduling',
            'feature_2_text' => 'Plan sessions and monitor attendance efficiently.',
            'feature_3_title' => 'Auto Scoring',
            'feature_3_text' => 'Automatic classification and pass/fail outcomes.',
            'feature_4_title' => 'Reports & Certificates',
            'feature_4_text' => 'Export and generate outputs within minutes.',
            'workflow_overline' => 'How It Works',
            'workflow_title' => 'Four Steps To Operational Clarity',
            'workflow_1_title' => 'Register Participant',
            'workflow_1_text' => 'Capture official profile and assign to a session.',
            'workflow_2_title' => 'Conduct Tests',
            'workflow_2_text' => 'Record health screening and fitness metrics.',
            'workflow_3_title' => 'Auto Evaluate',
            'workflow_3_text' => 'System calculates score and result status.',
            'workflow_4_title' => 'Generate Outputs',
            'workflow_4_text' => 'Issue reports and certificates quickly.',
            'preview_fitness_title' => 'Fitness Test Preview',
            'preview_fitness_badge' => 'UKJK Ready',
            'preview_fitness_description' => 'Interactive comparison slider showing baseline and improved readiness profile.',
            'preview_countdown_title' => 'Next Test Countdown',
            'preview_countdown_description' => 'Countdown updates live to the next scheduled session and supports quick BMI checks.',
            'open_bmi_button' => 'Open BMI Calculator',
            'fitness_level_beginner' => 'Beginner',
            'fitness_level_intermediate' => 'Intermediate',
            'fitness_level_advanced' => 'Advanced',
            'stats_overline' => 'Live Statistics',
            'stats_title' => 'Measured Performance, Real Numbers',
            'stats_pass_rate_label' => 'Pass Rate',
            'stats_total_sessions_label' => 'Total Sessions',
            'stats_total_participants_label' => 'Total Participants',
            'stats_live_workout_label' => 'Live Workout Counter',
            'dashboard_preview_overline' => 'Dashboard Preview',
            'dashboard_preview_title' => 'Interactive Monitoring Experience',
            'testimonials_overline' => 'Testimonials',
            'testimonials_title' => 'Trusted By Operations Teams',
            'faq_overline' => 'FAQ',
            'faq_title' => 'Questions You Might Ask',
            'faq_search_placeholder' => 'Search FAQs...',
            'faq_1_question' => 'Who can access JPJFit?',
            'faq_1_answer' => 'System Admin, JPJ Officers, and Health Officers can access JPJFit based on assigned roles.',
            'faq_2_question' => 'Can I export reports?',
            'faq_2_answer' => 'Yes. Reports can be exported in CSV and PDF formats for sharing and archiving.',
            'faq_3_question' => 'How is pass rate calculated?',
            'faq_3_answer' => 'Pass rate equals total results with status Pass divided by all recorded fitness results.',
            'faq_4_question' => 'Does JPJFit support different devices?',
            'faq_4_answer' => 'Yes. The interface is responsive for mobile, tablet and desktop workflows.',
            'cta_overline' => 'Start Your Digital Transformation',
            'cta_title' => 'Ready To Elevate Fitness Monitoring?',
            'cta_description' => 'Launch faster reporting cycles, cleaner data collection, and stronger readiness with JPJFit.',
            'cta_login_button' => 'Login Now',
            'cta_register_button' => 'Register Today',
            'cta_offer_prefix' => 'Offer ends in:',
            'footer_brand_description' => 'Energetic, modern and reliable fitness monitoring for public-sector operations.',
            'footer_quick_links_title' => 'Quick Links',
            'footer_support_title' => 'Support',
            'footer_newsletter_title' => 'Newsletter',
            'footer_newsletter_description' => 'Get release notes and monitoring updates.',
            'footer_newsletter_name_placeholder' => 'Your name',
            'footer_newsletter_email_placeholder' => 'you@example.com',
            'footer_newsletter_button' => 'Subscribe',
            'footer_copy_text' => 'JPJFit - Fitness Monitoring System',
            'footer_bottom_note' => 'Road Transport Department & Health Monitoring Operations',
            'fab_top_button' => 'Top',
            'fab_cta_button' => 'CTA',
            'fab_hc_button' => 'HC',
            'bmi_modal_title' => 'BMI Calculator',
            'bmi_height_label' => 'Height (cm)',
            'bmi_weight_label' => 'Weight (kg)',
            'bmi_calculate_button' => 'Calculate',
            'bmi_result_default' => 'Enter values and calculate your BMI.',
        ];
    }

    /**
     * Return all supported locales for tab rendering.
     *
     * @return array<int, string>
     */
    public function supportedLocales(): array
    {
        return self::SUPPORTED_LOCALES;
    }

    /**
     * Returns section/field metadata used to render the admin form.
     */
    public function fieldGroups(): array
    {
        return [
            [
                'title' => 'Meta & Navigation',
                'fields' => [
                    ['key' => 'meta_title', 'label' => 'Meta Title', 'type' => 'text'],
                    ['key' => 'meta_description', 'label' => 'Meta Description', 'type' => 'textarea'],
                    ['key' => 'brand_name', 'label' => 'Brand Name', 'type' => 'text'],
                    ['key' => 'brand_subtitle', 'label' => 'Brand Subtitle', 'type' => 'text'],
                    ['key' => 'nav_overview_label', 'label' => 'Navigation Overview Label', 'type' => 'text'],
                    ['key' => 'nav_features_label', 'label' => 'Navigation Features Label', 'type' => 'text'],
                    ['key' => 'nav_workflow_label', 'label' => 'Navigation Workflow Label', 'type' => 'text'],
                    ['key' => 'nav_faq_label', 'label' => 'Navigation FAQ Label', 'type' => 'text'],
                    ['key' => 'nav_login_button', 'label' => 'Navigation Login Button', 'type' => 'text'],
                    ['key' => 'nav_register_button', 'label' => 'Navigation Register Button', 'type' => 'text'],
                ],
            ],
            [
                'title' => 'Hero Section',
                'fields' => [
                    ['key' => 'hero_chip', 'label' => 'Hero Chip', 'type' => 'text'],
                    ['key' => 'hero_title', 'label' => 'Hero Title', 'type' => 'text'],
                    ['key' => 'hero_description', 'label' => 'Hero Description', 'type' => 'textarea'],
                    ['key' => 'hero_primary_button', 'label' => 'Hero Primary Button', 'type' => 'text'],
                    ['key' => 'hero_secondary_button', 'label' => 'Hero Secondary Button', 'type' => 'text'],
                    ['key' => 'hero_register_button', 'label' => 'Hero Register Button', 'type' => 'text'],
                ],
            ],
            [
                'title' => 'Overview Section',
                'fields' => [
                    ['key' => 'overview_overline', 'label' => 'Overview Overline', 'type' => 'text'],
                    ['key' => 'overview_title', 'label' => 'Overview Title', 'type' => 'text'],
                    ['key' => 'overview_description', 'label' => 'Overview Description', 'type' => 'textarea'],
                    ['key' => 'overview_chip_one_title', 'label' => 'Overview Chip 1 Title', 'type' => 'text'],
                    ['key' => 'overview_chip_one_text', 'label' => 'Overview Chip 1 Text', 'type' => 'textarea'],
                    ['key' => 'overview_chip_two_title', 'label' => 'Overview Chip 2 Title', 'type' => 'text'],
                    ['key' => 'overview_chip_two_text', 'label' => 'Overview Chip 2 Text', 'type' => 'textarea'],
                    ['key' => 'overview_kpi_participants', 'label' => 'Overview KPI Participants Label', 'type' => 'text'],
                    ['key' => 'overview_kpi_sessions', 'label' => 'Overview KPI Sessions Label', 'type' => 'text'],
                    ['key' => 'overview_kpi_pass_rate', 'label' => 'Overview KPI Pass Rate Label', 'type' => 'text'],
                    ['key' => 'overview_kpi_live_workouts', 'label' => 'Overview KPI Live Workouts Label', 'type' => 'text'],
                ],
            ],
            [
                'title' => 'Features Section',
                'fields' => [
                    ['key' => 'features_overline', 'label' => 'Features Overline', 'type' => 'text'],
                    ['key' => 'features_title', 'label' => 'Features Title', 'type' => 'text'],
                    ['key' => 'feature_1_title', 'label' => 'Feature 1 Title', 'type' => 'text'],
                    ['key' => 'feature_1_text', 'label' => 'Feature 1 Description', 'type' => 'textarea'],
                    ['key' => 'feature_2_title', 'label' => 'Feature 2 Title', 'type' => 'text'],
                    ['key' => 'feature_2_text', 'label' => 'Feature 2 Description', 'type' => 'textarea'],
                    ['key' => 'feature_3_title', 'label' => 'Feature 3 Title', 'type' => 'text'],
                    ['key' => 'feature_3_text', 'label' => 'Feature 3 Description', 'type' => 'textarea'],
                    ['key' => 'feature_4_title', 'label' => 'Feature 4 Title', 'type' => 'text'],
                    ['key' => 'feature_4_text', 'label' => 'Feature 4 Description', 'type' => 'textarea'],
                ],
            ],
            [
                'title' => 'Workflow Section',
                'fields' => [
                    ['key' => 'workflow_overline', 'label' => 'Workflow Overline', 'type' => 'text'],
                    ['key' => 'workflow_title', 'label' => 'Workflow Title', 'type' => 'text'],
                    ['key' => 'workflow_1_title', 'label' => 'Workflow Step 1 Title', 'type' => 'text'],
                    ['key' => 'workflow_1_text', 'label' => 'Workflow Step 1 Description', 'type' => 'textarea'],
                    ['key' => 'workflow_2_title', 'label' => 'Workflow Step 2 Title', 'type' => 'text'],
                    ['key' => 'workflow_2_text', 'label' => 'Workflow Step 2 Description', 'type' => 'textarea'],
                    ['key' => 'workflow_3_title', 'label' => 'Workflow Step 3 Title', 'type' => 'text'],
                    ['key' => 'workflow_3_text', 'label' => 'Workflow Step 3 Description', 'type' => 'textarea'],
                    ['key' => 'workflow_4_title', 'label' => 'Workflow Step 4 Title', 'type' => 'text'],
                    ['key' => 'workflow_4_text', 'label' => 'Workflow Step 4 Description', 'type' => 'textarea'],
                ],
            ],
            [
                'title' => 'Preview & Stats',
                'fields' => [
                    ['key' => 'preview_fitness_title', 'label' => 'Fitness Preview Title', 'type' => 'text'],
                    ['key' => 'preview_fitness_badge', 'label' => 'Fitness Preview Badge', 'type' => 'text'],
                    ['key' => 'preview_fitness_description', 'label' => 'Fitness Preview Description', 'type' => 'textarea'],
                    ['key' => 'preview_countdown_title', 'label' => 'Countdown Card Title', 'type' => 'text'],
                    ['key' => 'preview_countdown_description', 'label' => 'Countdown Card Description', 'type' => 'textarea'],
                    ['key' => 'open_bmi_button', 'label' => 'Open BMI Button Label', 'type' => 'text'],
                    ['key' => 'fitness_level_beginner', 'label' => 'Fitness Level 1 Label', 'type' => 'text'],
                    ['key' => 'fitness_level_intermediate', 'label' => 'Fitness Level 2 Label', 'type' => 'text'],
                    ['key' => 'fitness_level_advanced', 'label' => 'Fitness Level 3 Label', 'type' => 'text'],
                    ['key' => 'stats_overline', 'label' => 'Stats Overline', 'type' => 'text'],
                    ['key' => 'stats_title', 'label' => 'Stats Title', 'type' => 'text'],
                    ['key' => 'stats_pass_rate_label', 'label' => 'Stats Pass Rate Label', 'type' => 'text'],
                    ['key' => 'stats_total_sessions_label', 'label' => 'Stats Total Sessions Label', 'type' => 'text'],
                    ['key' => 'stats_total_participants_label', 'label' => 'Stats Total Participants Label', 'type' => 'text'],
                    ['key' => 'stats_live_workout_label', 'label' => 'Stats Live Workout Label', 'type' => 'text'],
                    ['key' => 'dashboard_preview_overline', 'label' => 'Dashboard Preview Overline', 'type' => 'text'],
                    ['key' => 'dashboard_preview_title', 'label' => 'Dashboard Preview Title', 'type' => 'text'],
                ],
            ],
            [
                'title' => 'Testimonials, FAQ, CTA',
                'fields' => [
                    ['key' => 'testimonials_overline', 'label' => 'Testimonials Overline', 'type' => 'text'],
                    ['key' => 'testimonials_title', 'label' => 'Testimonials Title', 'type' => 'text'],
                    ['key' => 'faq_overline', 'label' => 'FAQ Overline', 'type' => 'text'],
                    ['key' => 'faq_title', 'label' => 'FAQ Title', 'type' => 'text'],
                    ['key' => 'faq_search_placeholder', 'label' => 'FAQ Search Placeholder', 'type' => 'text'],
                    ['key' => 'faq_1_question', 'label' => 'FAQ 1 Question', 'type' => 'text'],
                    ['key' => 'faq_1_answer', 'label' => 'FAQ 1 Answer', 'type' => 'textarea'],
                    ['key' => 'faq_2_question', 'label' => 'FAQ 2 Question', 'type' => 'text'],
                    ['key' => 'faq_2_answer', 'label' => 'FAQ 2 Answer', 'type' => 'textarea'],
                    ['key' => 'faq_3_question', 'label' => 'FAQ 3 Question', 'type' => 'text'],
                    ['key' => 'faq_3_answer', 'label' => 'FAQ 3 Answer', 'type' => 'textarea'],
                    ['key' => 'faq_4_question', 'label' => 'FAQ 4 Question', 'type' => 'text'],
                    ['key' => 'faq_4_answer', 'label' => 'FAQ 4 Answer', 'type' => 'textarea'],
                    ['key' => 'cta_overline', 'label' => 'CTA Overline', 'type' => 'text'],
                    ['key' => 'cta_title', 'label' => 'CTA Title', 'type' => 'text'],
                    ['key' => 'cta_description', 'label' => 'CTA Description', 'type' => 'textarea'],
                    ['key' => 'cta_login_button', 'label' => 'CTA Login Button Label', 'type' => 'text'],
                    ['key' => 'cta_register_button', 'label' => 'CTA Register Button Label', 'type' => 'text'],
                    ['key' => 'cta_offer_prefix', 'label' => 'CTA Offer Countdown Prefix', 'type' => 'text'],
                ],
            ],
            [
                'title' => 'Footer & BMI Modal',
                'fields' => [
                    ['key' => 'footer_brand_description', 'label' => 'Footer Brand Description', 'type' => 'textarea'],
                    ['key' => 'footer_quick_links_title', 'label' => 'Footer Quick Links Title', 'type' => 'text'],
                    ['key' => 'footer_support_title', 'label' => 'Footer Support Title', 'type' => 'text'],
                    ['key' => 'footer_newsletter_title', 'label' => 'Footer Newsletter Title', 'type' => 'text'],
                    ['key' => 'footer_newsletter_description', 'label' => 'Footer Newsletter Description', 'type' => 'textarea'],
                    ['key' => 'footer_newsletter_name_placeholder', 'label' => 'Newsletter Name Placeholder', 'type' => 'text'],
                    ['key' => 'footer_newsletter_email_placeholder', 'label' => 'Newsletter Email Placeholder', 'type' => 'text'],
                    ['key' => 'footer_newsletter_button', 'label' => 'Newsletter Button Label', 'type' => 'text'],
                    ['key' => 'footer_copy_text', 'label' => 'Footer Copyright Text', 'type' => 'text'],
                    ['key' => 'footer_bottom_note', 'label' => 'Footer Bottom Note', 'type' => 'text'],
                    ['key' => 'fab_top_button', 'label' => 'Floating Top Button Label', 'type' => 'text'],
                    ['key' => 'fab_cta_button', 'label' => 'Floating CTA Button Label', 'type' => 'text'],
                    ['key' => 'fab_hc_button', 'label' => 'Floating High Contrast Button Label', 'type' => 'text'],
                    ['key' => 'bmi_modal_title', 'label' => 'BMI Modal Title', 'type' => 'text'],
                    ['key' => 'bmi_height_label', 'label' => 'BMI Height Label', 'type' => 'text'],
                    ['key' => 'bmi_weight_label', 'label' => 'BMI Weight Label', 'type' => 'text'],
                    ['key' => 'bmi_calculate_button', 'label' => 'BMI Calculate Button Label', 'type' => 'text'],
                    ['key' => 'bmi_result_default', 'label' => 'BMI Result Default Text', 'type' => 'textarea'],
                ],
            ],
        ];
    }

    /**
     * Returns landing content merged with defaults when DB values are missing.
     */
    public function all(?string $locale = null): array
    {
        $locale = $this->normalizeLocale($locale ?? app()->getLocale());
        $defaults = $this->defaults($locale);
        $fallbackDefaults = $this->defaults(self::DEFAULT_LOCALE);
        $content = $defaults;

        if (! Schema::hasTable('landing_page_contents')) {
            return $content;
        }

        $storedByLocale = $this->storedByLocale();
        $stored = $storedByLocale[$locale] ?? [];
        $fallbackStored = $storedByLocale[self::DEFAULT_LOCALE] ?? [];

        foreach ($content as $key => $defaultValue) {
            $storedValue = $stored[$key] ?? null;
            $fallbackStoredValue = $fallbackStored[$key] ?? null;
            $fallbackValue = $fallbackDefaults[$key] ?? $defaultValue;

            if ($storedValue !== null && $storedValue !== '') {
                $content[$key] = $storedValue;
                continue;
            }

            if ($fallbackStoredValue !== null && $fallbackStoredValue !== '') {
                $content[$key] = $fallbackStoredValue;
                continue;
            }

            $content[$key] = $fallbackValue;
        }

        return $content;
    }

    /**
     * Returns localized landing content keyed by locale.
     *
     * @return array<string, array<string, string>>
     */
    public function allLocales(): array
    {
        $content = [];

        foreach (self::SUPPORTED_LOCALES as $locale) {
            $content[$locale] = $this->all($locale);
        }

        return $content;
    }

    /**
     * Save admin-submitted values for allowed keys.
     */
    public function save(array $submittedContent): void
    {
        if (! Schema::hasTable('landing_page_contents')) {
            return;
        }

        $allowed = array_keys($this->defaults(self::DEFAULT_LOCALE));
        $now = now();
        $rows = [];

        foreach (self::SUPPORTED_LOCALES as $locale) {
            $localeContent = $submittedContent[$locale] ?? [];

            if (! is_array($localeContent)) {
                continue;
            }

            foreach ($allowed as $key) {
                if (! array_key_exists($key, $localeContent)) {
                    continue;
                }

                $rows[] = [
                    'locale' => $locale,
                    'key' => $key,
                    'value' => $localeContent[$key],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if ($rows !== []) {
            LandingPageContent::query()->upsert($rows, ['locale', 'key'], ['value', 'updated_at']);
        }

        Cache::forget(self::CACHE_KEY);
    }

    /**
     * @return array<string, array<string, string>>
     */
    private function storedByLocale(): array
    {
        return Cache::remember(self::CACHE_KEY, now()->addMinutes(15), function (): array {
            if (! Schema::hasColumn('landing_page_contents', 'locale')) {
                $legacy = LandingPageContent::query()->pluck('value', 'key')->all();

                return [self::DEFAULT_LOCALE => $legacy];
            }

            $grouped = [];
            $records = LandingPageContent::query()->get(['locale', 'key', 'value']);

            foreach ($records as $record) {
                $locale = $this->normalizeLocale((string) $record->locale);
                $grouped[$locale][$record->key] = $record->value;
            }

            return $grouped;
        });
    }

    private function normalizeLocale(string $locale): string
    {
        return in_array($locale, self::SUPPORTED_LOCALES, true)
            ? $locale
            : self::DEFAULT_LOCALE;
    }
}
