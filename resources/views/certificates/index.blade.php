<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-extrabold text-slate-800">{{ __('Certificate Management') }}</h1>
    </x-slot>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="panel-card p-5 lg:col-span-1">
            <h2 class="text-lg font-bold text-slate-800">{{ __('Generate Certificate') }}</h2>
            <p class="mt-1 text-sm text-slate-500">{{ __('Only sessions with participants who passed and have no certificate are listed.') }}</p>

            <form method="POST" action="{{ route('certificates.store') }}" class="mt-4 space-y-3" id="certificateGenerateForm">
                @csrf
                <div>
                    <label class="text-sm font-medium text-slate-700">{{ __('Eligible Session') }}</label>
                    <select class="form-select" name="test_session_id" required>
                        <option value="">{{ __('Choose session') }}</option>
                        @foreach($eligibleSessions as $item)
                            <option value="{{ $item['session']->id }}" data-session-code="{{ $item['session']->session_code }}" data-pending-count="{{ $item['pending_count'] }}">
                                {{ $item['session']->session_code }} - {{ $item['session']->title }} ({{ $item['pending_count'] }} {{ __('eligible participants') }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <button class="btn-primary w-full justify-center" type="submit" data-generate-submit>{{ __('Generate Certificates for Session') }}</button>
            </form>
        </div>

        <div class="panel-card p-5 lg:col-span-2">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-lg font-bold text-slate-800">{{ __('Generated Certificates') }}</h2>
                <form method="POST" action="{{ route('certificates.send-pending-emails') }}" onsubmit="return confirm('{{ __('Send all pending certificate emails now?') }}')">
                    @csrf
                    <button type="submit" class="filter-btn">{{ __('Send All Pending') }} ({{ $pendingEmailCount ?? 0 }})</button>
                </form>
            </div>
            <form method="GET" action="{{ route('certificates.index') }}" class="mt-4 grid gap-3 md:grid-cols-4">
                <input type="text" name="search" value="{{ $search }}" class="form-input md:col-span-3" placeholder="{{ __('Search certificate, participant, session') }}">
                <button class="filter-btn" type="submit">{{ __('Search') }}</button>
            </form>
            <div class="mt-4 overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ __('Certificate No') }}</th>
                            <th>{{ __('Participant') }}</th>
                            <th>{{ __('Session') }}</th>
                            <th>{{ __('Issued At') }}</th>
                            <th>{{ __('Emailed') }}</th>
                            <th class="text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($certificates as $certificate)
                            <tr>
                                <td>{{ $certificate->certificate_no }}</td>
                                <td>{{ $certificate->participant?->full_name }}</td>
                                <td>{{ $certificate->testSession?->session_code }}</td>
                                <td>{{ $certificate->issued_at?->format('d M Y H:i') ?? '-' }}</td>
                                <td>
                                    @if($certificate->emailed_at)
                                        <span class="text-xs font-semibold text-emerald-700">{{ __('Sent') }} ({{ $certificate->emailed_at->format('d M Y H:i') }})</span>
                                    @else
                                        <span class="text-xs font-semibold text-amber-700">{{ __('Pending') }}</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('certificates.preview', $certificate) }}" target="_blank" class="text-sm font-semibold text-sky-700">{{ __('Preview') }}</a>
                                    <span class="mx-2 text-slate-300">|</span>
                                    <a href="{{ route('certificates.download', $certificate) }}" class="text-sm font-semibold text-teal-700">{{ __('Download') }}</a>
                                    <span class="mx-2 text-slate-300">|</span>
                                    @if(!empty($certificate->participant?->email) && filter_var($certificate->participant?->email, FILTER_VALIDATE_EMAIL))
                                        <form method="POST" action="{{ route('certificates.send-email', $certificate) }}" class="inline" onsubmit="return confirm('{{ __('Send certificate email to participant?') }}')">
                                            @csrf
                                            <button type="submit" class="text-sm font-semibold text-indigo-700">{{ __('Send Email') }}</button>
                                        </form>
                                    @else
                                        <span class="text-xs font-semibold text-slate-400" title="Participant email is missing or invalid">{{ __('No Valid Email') }}</span>
                                    @endif
                                    @if(auth()->user()->hasRole('admin'))
                                        <span class="mx-2 text-slate-300">|</span>
                                        <form method="POST" action="{{ route('certificates.destroy', $certificate) }}" class="ml-3 inline" onsubmit="return confirm('{{ __('Delete certificate?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            
                                           <button class="text-sm font-semibold text-rose-700 hover:text-rose-900">{{ __('Delete') }}</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-slate-500">{{ __('No certificates yet.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="certificateGenerateModal" class="fixed inset-0 z-[70] hidden items-center justify-center bg-slate-900/60 px-4" data-processing-prefix="{{ __('Generating certificates for session') }}" data-eligible-label="{{ __('eligible participants') }}">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
            <div class="flex items-start gap-4">
                <div class="mt-1 h-10 w-10 shrink-0 aspect-square animate-spin rounded-full border-4 border-slate-200 border-t-teal-600"></div>
                <div>
                    <h3 class="text-lg font-bold text-slate-800">{{ __('Generating certificates...') }}</h3>
                    <p class="mt-1 text-sm text-slate-600" id="certificateGenerateModalMessage">{{ __('Please wait while certificates are generated for the selected session.') }}</p>
                    <p class="mt-3 text-xs text-slate-500">{{ __('Do not close this tab until the process completes.') }}</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
