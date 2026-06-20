@php
    $issueTitle = $issue->title ?? 'Issue';
    $issueStatus = str($issue->status ?? 'reported')->replace('_', ' ')->title();
    $workerName = $issue->worker?->name ?? 'Not assigned yet';
    $citizenName = $issue->user?->name ?? 'Not available';
@endphp

@include('emails.layout', [
    'title' => $headline,
    'subtitle' => $subtitle ?? null,
    'slot' => new \Illuminate\Support\HtmlString(
        view('emails.partials.issue-message-body', [
            'greeting' => $greeting,
            'headline' => $headline,
            'bodyLines' => $bodyLines,
            'footer' => $footer,
            'issue' => $issue,
            'issueTitle' => $issueTitle,
            'issueStatus' => $issueStatus,
            'workerName' => $workerName,
            'citizenName' => $citizenName,
        ])->render()
    ),
])
