<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateMailSettingsRequest;
use App\Services\MailSettingsService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class MailSettingsController extends Controller
{
    public function edit(MailSettingsService $mailSettingsService): View
    {
        return view('mail-settings.edit', [
            'settings' => $mailSettingsService->all(),
            'mailerOptions' => [
                'log' => 'Log (development only)',
                'smtp' => 'SMTP (custom)',
                'gmail' => 'Gmail',
                'outlook' => 'Outlook / Microsoft 365',
                'mailtrap' => 'Mailtrap',
            ],
            'groups' => [
                'smtp' => ['title' => 'SMTP Settings', 'passwordKey' => 'smtp_password'],
                'gmail' => ['title' => 'Gmail Settings', 'passwordKey' => 'gmail_app_password'],
                'outlook' => ['title' => 'Outlook Settings', 'passwordKey' => 'outlook_password'],
                'mailtrap' => ['title' => 'Mailtrap Settings', 'passwordKey' => 'mailtrap_password'],
            ],
            'fieldMap' => [
                'scheme' => 'Scheme',
                'host' => 'Host',
                'port' => 'Port',
                'username' => 'Username',
                'from_address' => 'From Email',
                'from_name' => 'From Name',
            ],
        ]);
    }

    public function update(UpdateMailSettingsRequest $request, MailSettingsService $mailSettingsService): RedirectResponse
    {
        $validated = $request->validated();

        $mailSettingsService->save($validated['settings'] ?? []);
        $mailSettingsService->applyRuntimeConfig();

        return redirect()
            ->route('mail-settings.edit')
            ->with('success', 'Mail settings updated successfully.');
    }
}
