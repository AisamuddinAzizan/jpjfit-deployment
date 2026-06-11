<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1f2937; }
        h1 { margin: 0 0 4px; font-size: 22px; color: #123b76; }
        p { margin: 0 0 12px; font-size: 12px; color: #4b5563; }
        table { width: 100%; border-collapse: collapse; font-size: 10px; }
        th, td { border: 1px solid #cbd5e1; padding: 6px; text-align: left; }
        th { background: #123b76; color: #ffffff; text-transform: uppercase; font-size: 9px; font-weight: bold; }
    </style>
</head>
<body>
    <h1>JPJFit Fitness & Health Results Report</h1>
    <p>Generated at: {{ $generatedAt->format('d M Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>Participant No</th>
                <th>Name</th>
                
                <th>Height/Weight</th>
                <th>BMI</th>
                <th>BP (mmHg)</th>
                <th>Glucose/Chol</th>
                
                <th>Push Ups</th>
                <th>Sit Ups</th>
                <th>Reach (cm)</th>
                <th>Score</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $result)
                @php
                    // Cari rekod kesihatan yang sepadan dengan baris peserta ini
                    $key = $result->participant_id . '-' . $result->test_session_id;
                    $health = $healthRecords->get($key) ?? null;

                    // Kira BMI secara automatik jika ada rekod kesihatan
                    $bmiValue = '-';
                    if ($health && $health->height_cm > 0 && $health->weight_kg > 0) {
                        $heightInMeters = $health->height_cm / 100;
                        $bmiValue = round($health->weight_kg / ($heightInMeters * $heightInMeters), 1);
                    }
                @endphp
                <tr>
                    <td>{{ $result->participant?->participant_no ?? '-' }}</td>
                    <td>{{ $result->participant?->full_name ?? '-' }}</td>
                    
                    <td>{{ $health ? $health->height_cm.' cm / '.$health->weight_kg.' kg' : '-' }}</td>
                    <td>{{ $bmiValue }}</td>
                    <td>{{ $health ? $health->blood_pressure_systolic.'/'.$health->blood_pressure_diastolic : '-' }}</td>
                    <td>
                        Gula: {{ $health && $health->glucose_mmol ? $health->glucose_mmol : '-' }}<br>
                        Kol: {{ $health && $health->cholesterol_mmol ? $health->cholesterol_mmol : '-' }}
                    </td>

                    <td>{{ $result->push_ups }}</td>
                    <td>{{ $result->sit_ups }}</td>
                    <td>{{ $result->sit_and_reach_cm }} cm</td>
                    <td><strong>{{ $result->total_score }}</strong></td>
                    <td>{{ $result->result_status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>