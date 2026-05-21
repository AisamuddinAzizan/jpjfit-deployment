<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1f2937; }
        h1 { margin: 0 0 4px; font-size: 22px; }
        p { margin: 0 0 12px; font-size: 12px; color: #4b5563; }
        table { width: 100%; border-collapse: collapse; font-size: 11px; }
        th, td { border: 1px solid #cbd5e1; padding: 6px; text-align: left; }
        th { background: #e2e8f0; text-transform: uppercase; font-size: 10px; }
    </style>
</head>
<body>
    <h1>{{ __('JPJFit Fitness Results Report') }}</h1>
    <p>{{ __('Generated at') }} {{ $generatedAt->format('d M Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>{{ __('Participant No') }}</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Session') }}</th>
                <th>{{ __('Push') }}</th>
                <th>{{ __('Sit') }}</th>
                <th>{{ __('Reach') }}</th>
                <th>{{ __('Shuttle') }}</th>
                <th>{{ __('2.4km (sec)') }}</th>
                <th>{{ __('Score') }}</th>
                <th>{{ __('Class') }}</th>
                <th>{{ __('Result') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $result)
                <tr>
                    <td>{{ $result->participant?->participant_no }}</td>
                    <td>{{ $result->participant?->full_name }}</td>
                    <td>{{ $result->testSession?->session_code }}</td>
                    <td>{{ $result->push_ups }}</td>
                    <td>{{ $result->sit_ups }}</td>
                    <td>{{ $result->sit_and_reach_cm }}</td>
                    <td>{{ $result->shuttle_run_level }}</td>
                    <td>{{ $result->run_2_4km_seconds }}</td>
                    <td>{{ $result->total_score }}</td>
                    <td>{{ __($result->classification) }}</td>
                    <td>{{ __($result->result_status) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
