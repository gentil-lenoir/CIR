<p style="margin:0 0 18px;font-size:16px;line-height:1.6;">{{ $greeting }}</p>

<h2 style="margin:0 0 16px;font-size:24px;line-height:1.3;color:#0f172a;">{{ $headline }}</h2>

@foreach ($bodyLines as $line)
    <p style="margin:0 0 14px;font-size:15px;line-height:1.7;color:#334155;">{{ $line }}</p>
@endforeach

<div style="margin:24px 0;padding:18px;border:1px solid #e2e8f0;border-radius:14px;background-color:#f8fafc;">
    <p style="margin:0 0 10px;font-size:14px;line-height:1.6;"><strong>Issue ID:</strong> #{{ $issue->id }}</p>
    <p style="margin:0 0 10px;font-size:14px;line-height:1.6;"><strong>Title:</strong> {{ $issueTitle }}</p>
    <p style="margin:0 0 10px;font-size:14px;line-height:1.6;"><strong>Status:</strong> {{ $issueStatus }}</p>
    <p style="margin:0 0 10px;font-size:14px;line-height:1.6;"><strong>Citizen:</strong> {{ $citizenName }}</p>
    <p style="margin:0 0 10px;font-size:14px;line-height:1.6;"><strong>Worker:</strong> {{ $workerName }}</p>
    <p style="margin:0 0 10px;font-size:14px;line-height:1.6;"><strong>Category:</strong> {{ $issue->category ?? 'General' }}</p>
    <p style="margin:0 0 10px;font-size:14px;line-height:1.6;"><strong>Priority:</strong> {{ str($issue->priority ?? 'medium')->title() }}</p>
    <p style="margin:0;font-size:14px;line-height:1.6;"><strong>Address:</strong> {{ $issue->address ?? 'Not provided' }}</p>
</div>

<p style="margin:0;font-size:14px;line-height:1.7;color:#475569;">{{ $footer }}</p>
