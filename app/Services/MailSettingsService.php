<?php

namespace App\Services;

use App\Models\MailSetting;
use Throwable;
use Illuminate\Support\Facades\Schema;

class MailSettingsService
{
    /**
     * @return array<string, string>
     */
    public function defaults(): array
    {
        return [
            'active_mailer' => (string) config('mail.default', 'log'),

            'smtp_scheme' => (string) env('MAIL_SCHEME', 'tls'),
            'smtp_host' => (string) env('MAIL_HOST', '127.0.0.1'),
            'smtp_port' => (string) env('MAIL_PORT', '2525'),
            'smtp_username' => (string) env('MAIL_USERNAME', ''),
            'smtp_password' => '',
            'smtp_from_address' => (string) env('MAIL_FROM_ADDRESS', ''),
            'smtp_from_name' => (string) env('MAIL_FROM_NAME', config('app.name', 'JPJFit')),

            'gmail_scheme' => (string) env('GMAIL_SCHEME', 'tls'),
            'gmail_host' => (string) env('GMAIL_HOST', 'smtp.gmail.com'),
            'gmail_port' => (string) env('GMAIL_PORT', '587'),
            'gmail_username' => (string) env('GMAIL_USERNAME', ''),
            'gmail_app_password' => '',
            'gmail_from_address' => (string) env('GMAIL_FROM_ADDRESS', ''),
            'gmail_from_name' => (string) env('GMAIL_FROM_NAME', config('app.name', 'JPJFit')),

            'outlook_scheme' => (string) env('OUTLOOK_SCHEME', 'tls'),
            'outlook_host' => (string) env('OUTLOOK_HOST', 'smtp.office365.com'),
            'outlook_port' => (string) env('OUTLOOK_PORT', '587'),
            'outlook_username' => (string) env('OUTLOOK_USERNAME', ''),
            'outlook_password' => '',
            'outlook_from_address' => (string) env('OUTLOOK_FROM_ADDRESS', ''),
            'outlook_from_name' => (string) env('OUTLOOK_FROM_NAME', config('app.name', 'JPJFit')),

            'mailtrap_scheme' => (string) env('MAILTRAP_SCHEME', 'tls'),
            'mailtrap_host' => (string) env('MAILTRAP_HOST', 'sandbox.smtp.mailtrap.io'),
            'mailtrap_port' => (string) env('MAILTRAP_PORT', '2525'),
            'mailtrap_username' => (string) env('MAILTRAP_USERNAME', ''),
            'mailtrap_password' => '',
            'mailtrap_from_address' => (string) env('MAILTRAP_FROM_ADDRESS', ''),
            'mailtrap_from_name' => (string) env('MAILTRAP_FROM_NAME', config('app.name', 'JPJFit')),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function all(): array
    {
        $settings = $this->defaults();

        if (! $this->mailSettingsTableExists()) {
            return $settings;
        }

        try {
            $stored = MailSetting::query()
                ->get(['key', 'value'])
                ->mapWithKeys(static fn (MailSetting $setting): array => [$setting->key => $setting->value])
                ->all();
        } catch (Throwable) {
            return $settings;
        }

        foreach ($stored as $key => $value) {
            if (! array_key_exists($key, $settings) || $value === null) {
                continue;
            }

            $settings[$key] = (string) $value;
        }

        return $settings;
    }

    /**
     * @param array<string, mixed> $submitted
     */
    public function save(array $submitted): void
    {
        if (! $this->mailSettingsTableExists()) {
            return;
        }

        $allowed = array_keys($this->defaults());
        $existingKeys = [];

        try {
            $existingKeys = MailSetting::query()->pluck('key')->all();
        } catch (Throwable) {
            return;
        }

        $passwordKeys = [
            'smtp_password',
            'gmail_app_password',
            'outlook_password',
            'mailtrap_password',
        ];

        foreach ($allowed as $key) {
            if (! array_key_exists($key, $submitted)) {
                continue;
            }

            $newValue = $submitted[$key];
            $newValue = is_null($newValue) ? '' : trim((string) $newValue);

            if (in_array($key, $passwordKeys, true) && $newValue === '' && in_array($key, $existingKeys, true)) {
                continue;
            }

            MailSetting::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $newValue]
            );
        }
    }

    public function applyRuntimeConfig(): void
    {
        $settings = $this->all();
        $settings = $this->normalizeProviderSettings($settings);
        $mailer = $settings['active_mailer'] ?? (string) config('mail.default', 'log');

        config(['mail.default' => $mailer]);

        $this->applyMailer('smtp', $settings, [
            'scheme' => 'smtp_scheme',
            'host' => 'smtp_host',
            'port' => 'smtp_port',
            'username' => 'smtp_username',
            'password' => 'smtp_password',
        ]);

        $this->applyMailer('gmail', $settings, [
            'scheme' => 'gmail_scheme',
            'host' => 'gmail_host',
            'port' => 'gmail_port',
            'username' => 'gmail_username',
            'password' => 'gmail_app_password',
        ]);

        $this->applyMailer('outlook', $settings, [
            'scheme' => 'outlook_scheme',
            'host' => 'outlook_host',
            'port' => 'outlook_port',
            'username' => 'outlook_username',
            'password' => 'outlook_password',
        ]);

        $this->applyMailer('mailtrap', $settings, [
            'scheme' => 'mailtrap_scheme',
            'host' => 'mailtrap_host',
            'port' => 'mailtrap_port',
            'username' => 'mailtrap_username',
            'password' => 'mailtrap_password',
        ]);

        $fromAddress = $this->resolveFromAddress($mailer, $settings);
        $fromName = $this->resolveFromName($mailer, $settings);

        if ($fromAddress !== '') {
            config(['mail.from.address' => $fromAddress]);
        }

        if ($fromName !== '') {
            config(['mail.from.name' => $fromName]);
        }
    }

    /**
     * @param array<string, string> $settings
     * @param array<string, string> $keys
     */
    private function applyMailer(string $mailer, array $settings, array $keys): void
    {
        foreach ($keys as $mailKey => $settingKey) {
            $value = $settings[$settingKey] ?? null;
            if ($value === null || $value === '') {
                continue;
            }

            if ($mailKey === 'scheme') {
                $value = $this->normalizeScheme((string) $value);
            }

            if ($settingKey === 'gmail_app_password') {
                $value = preg_replace('/\s+/', '', (string) $value) ?? (string) $value;
            }

            if ($mailKey === 'port') {
                config(["mail.mailers.{$mailer}.{$mailKey}" => (int) $value]);
                continue;
            }

            config(["mail.mailers.{$mailer}.{$mailKey}" => $value]);
        }
    }

    /**
     * @param array<string, string> $settings
     */
    private function resolveFromAddress(string $activeMailer, array $settings): string
    {
        return match ($activeMailer) {
            'smtp' => $settings['smtp_from_address'] ?? '',
            'gmail' => $settings['gmail_from_address'] ?? '',
            'outlook' => $settings['outlook_from_address'] ?? '',
            'mailtrap' => $settings['mailtrap_from_address'] ?? '',
            default => $settings['smtp_from_address'] ?? '',
        };
    }

    /**
     * @param array<string, string> $settings
     */
    private function resolveFromName(string $activeMailer, array $settings): string
    {
        return match ($activeMailer) {
            'smtp' => $settings['smtp_from_name'] ?? '',
            'gmail' => $settings['gmail_from_name'] ?? '',
            'outlook' => $settings['outlook_from_name'] ?? '',
            'mailtrap' => $settings['mailtrap_from_name'] ?? '',
            default => $settings['smtp_from_name'] ?? '',
        };
    }

    private function mailSettingsTableExists(): bool
    {
        try {
            return Schema::hasTable('mail_settings');
        } catch (Throwable) {
            return false;
        }
    }

    private function normalizeScheme(string $scheme): string
    {
        $normalized = strtolower(trim($scheme));

        return match ($normalized) {
            'tls' => 'smtp',
            'ssl' => 'smtps',
            default => $normalized,
        };
    }

    /**
     * @param array<string, string> $settings
     * @return array<string, string>
     */
    private function normalizeProviderSettings(array $settings): array
    {
        $gmailUsername = $settings['gmail_username'] ?? '';
        $gmailFromAddress = $settings['gmail_from_address'] ?? '';

        if ((! filter_var($gmailUsername, FILTER_VALIDATE_EMAIL)) && filter_var($gmailFromAddress, FILTER_VALIDATE_EMAIL)) {
            $settings['gmail_username'] = $gmailFromAddress;
        }

        if (isset($settings['gmail_app_password'])) {
            $settings['gmail_app_password'] = preg_replace('/\s+/', '', $settings['gmail_app_password']) ?? $settings['gmail_app_password'];
        }

        return $settings;
    }
}
